<?php

declare(strict_types=1);

namespace App;

use App\Events\Account\UsernameChangeEvent;
use App\Helpers\Storage\Files\SkinsStorage;
use App\Helpers\UserDataValidator;
use App\Image\ImageSection;
use App\Image\IsometricAvatar;
use App\Image\Sections\Avatar;
use App\Image\Sections\Skin;
use App\Minecraft\MojangAccount;
use App\Minecraft\MojangClient;
use App\Models\Account;
use App\Models\AccountNotFound;
use App\Repositories\AccountRepository;
use App\Repositories\AccountStatsRepository;
use Illuminate\Support\Facades\Event;

/**
 * Class Core.
 */
class Core
{
    /**
     * Requested string.
     *
     * @var string
     */
    private $request = '';

    /**
     * Userdata from/to DB.
     *
     * @var Account
     */
    private $userdata;

    /**
     * Full userdata.
     *
     * @var MojangAccount
     */
    private $apiUserdata;

    /**
     * User data has been updated?
     *
     * @var bool
     */
    private $dataUpdated = false;

    /**
     * Set force update.
     *
     * @var bool
     */
    private $forceUpdate;

    /**
     * Minepic error string.
     *
     * @var string
     */
    private $error = false;

    /**
     * Account not found?
     *
     * @var bool
     */
    private $accountNotFound = false;

    /**
     * Retry for nonexistent usernames.
     *
     * @var string
     */
    private $retryUnexistentCheck = false;

    /**
     * Current image path.
     *
     * @var string
     */
    public $currentUserSkinImage;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var MojangClient
     */
    private $mojangClient;

    /**
     * @var AccountStatsRepository
     */
    private $accountStatsRepository;

    /**
     * Core constructor.
     *
     * @param AccountRepository      $accountRepository      Where user data is stored
     * @param AccountStatsRepository $accountStatsRepository
     * @param MojangClient           $mojangClient           Client for Mojang API
     */
    public function __construct(
        AccountRepository $accountRepository,
        AccountStatsRepository $accountStatsRepository,
        MojangClient $mojangClient
    ) {
        $this->accountRepository = $accountRepository;
        $this->mojangClient = $mojangClient;
        $this->accountStatsRepository = $accountStatsRepository;
    }

    /**
     * Display error.
     */
    public function error(): string
    {
        return $this->error;
    }

    /**
     * Check if is a valid UUID.
     */
    public function isCurrentRequestValidUuid(): bool
    {
        return UserDataValidator::isValidUuid($this->request);
    }

    /**
     * Normalize request.
     */
    private function normalizeRequest(): void
    {
        $this->request = \preg_replace("#\.png.*#", '', $this->request);
        $this->request = \preg_replace('#[^a-zA-Z0-9_]#', '', $this->request);
    }

    /**
     * Check if cache is still valid.
     *
     * @param int
     *
     * @return bool
     */
    private function checkDbCache(): bool
    {
        $accountUpdatedAtTimestamp = $this->userdata->updated_at->timestamp ?? 0;

        return (\time() - $accountUpdatedAtTimestamp) < env('USERDATA_CACHE_TIME');
    }

    /**
     * Load saved Account information.
     *
     * @param Account|null $account
     *
     * @return bool
     */
    private function loadAccountData(?Account $account): bool
    {
        if ($account !== null) {
            $this->userdata = $account;
            $this->currentUserSkinImage = SkinsStorage::getPath($this->userdata->uuid);

            return true;
        }
        $this->currentUserSkinImage = SkinsStorage::getPath(env('DEFAULT_USERNAME'));

        return false;
    }

    /**
     * Return loaded user data.
     *
     * @return Account
     */
    public function getUserdata(): Account
    {
        return $this->userdata ?? new Account();
    }

    /**
     * Check if an UUID is in the database.
     *
     * @return bool Returns true/false
     */
    private function requestedUuidInDb(): bool
    {
        $account = $this->accountRepository->findByUuid($this->request);

        return $this->loadAccountData($account);
    }

    /**
     * Check if a username is in the database.
     *
     * @return bool Returns true/false
     */
    private function requestedUsernameInDb(): bool
    {
        $account = $this->accountRepository->findLastUpdatedByUsername($this->request);

        return $this->loadAccountData($account);
    }

    /**
     * Insert user data in database.
     *
     * @param void
     *
     * @return bool
     */
    public function insertNewUuid(): bool
    {
        if ($this->getFullUserdataApi()) {
            $this->userdata = $this->accountRepository->create([
                'username' => $this->apiUserdata->username,
                'uuid' => $this->apiUserdata->uuid,
                'skin' => $this->apiUserdata->skin && \mb_strlen($this->apiUserdata->skin) > 1 ? $this->apiUserdata->skin : '',
                'cape' => $this->apiUserdata->cape && \mb_strlen($this->apiUserdata->cape) > 1 ? $this->apiUserdata->cape : '',
            ]);

            $this->saveRemoteSkin();
            $this->currentUserSkinImage = SkinsStorage::getPath($this->apiUserdata->uuid);

            $this->accountStatsRepository->create([
                'uuid' => $this->userdata->uuid,
                'count_search' => 0,
                'count_request' => 0,
                'time_search' => 0,
                'time_request' => 0,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Get UUID from username.
     *
     * @param string
     *
     * @return bool
     */
    private function convertRequestToUuid(): bool
    {
        if (UserDataValidator::isValidUsername($this->request) || UserDataValidator::isValidEmail($this->request)) {
            try {
                $account = $this->mojangClient->sendUsernameInfoRequest($this->request);
                $this->request = $account->uuid;

                return true;
            } catch (\Exception $e) {
                \Log::error($e);

                return false;
            }
        }

        return false;
    }

    /**
     * Salva account inesistente.
     *
     * @return mixed
     */
    public function saveUnexistentAccount()
    {
        $notFound = AccountNotFound::firstOrNew(['request' => $this->request]);
        $notFound->request = $this->request;

        return $notFound->save();
    }

    /**
     * Check if requested string is a failed request.
     *
     * @return bool
     */
    public function isUnexistentAccount(): bool
    {
        /** @var \App\Models\AccountNotFound $result */
        $result = AccountNotFound::find($this->request);
        if ($result !== null) {
            if ((\time() - $result->updated_at->timestamp) > env('USERDATA_CACHE_TIME')) {
                $result->touch();
                $this->retryUnexistentCheck = true;
            } else {
                $this->retryUnexistentCheck = false;
            }
            $this->accountNotFound = true;

            return true;
        }
        $this->accountNotFound = false;

        return false;
    }

    /**
     * Delete current request from failed cache.
     */
    public function removeFailedRequest(): bool
    {
        $result = AccountNotFound::where('request', $this->request)->delete();

        return \count($result) > 0;
    }

    /**
     * Check requested string and initialize objects.
     *
     * @param string
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function initialize(string $string): bool
    {
        $this->dataUpdated = false;
        $this->request = $string;
        $this->normalizeRequest();

        if ($this->request === '' || \mb_strlen($this->request) > 32) {
            throw new \Exception('Invalid Username or UUID provided');
        }

        if ($this->isCurrentRequestValidUuid()) {
            if ($this->initializeUuidRequest()) {
                return true;
            }
        } elseif ($this->requestedUsernameInDb()) {
            return $this->initializeUsernameRequest();
        } elseif (!$this->isUnexistentAccount() || $this->retryUnexistentCheck) {
            if (!$this->isCurrentRequestValidUuid() && !$this->convertRequestToUuid()) {
                $this->saveUnexistentAccount();
                $this->setFailedRequest('Invalid requested username');

                return false;
            }

            // Check if the uuid is already in the database, maybe the user has changed username and the check
            // nameInDb() has failed
            if ($this->requestedUuidInDb()) {
                $this->updateDbUser();

                return true;
            }

            if ($this->insertNewUuid()) {
                if ($this->accountNotFound) {
                    $this->removeFailedRequest();
                }

                return true;
            }
        }

        $this->setFailedRequest('Account not found');

        return false;
    }

    /**
     * Update current user fail count.
     */
    private function updateUserFailUpdate(): bool
    {
        if (isset($this->userdata->uuid)) {
            ++$this->userdata->fail_count;

            return $this->userdata->save();
        }

        return false;
    }

    /**
     * Update db user data.
     */
    private function updateDbUser(): bool
    {
        if (isset($this->userdata->username) && $this->userdata->uuid !== '') {
            // Get data from API
            if ($this->getFullUserdataApi()) {
                $originalUsername = $this->userdata->username;
                // Update database
                $this->accountRepository->update([
                    'username' => $this->apiUserdata->username,
                    'skin' => $this->apiUserdata->skin,
                    'cape' => $this->apiUserdata->cape,
                    'fail_count' => 0,
                ], $this->userdata->id);

                $this->userdata->refresh();

                // Update skin
                $this->saveRemoteSkin();

                // Log username change
                if ($this->userdata->username !== $originalUsername && $originalUsername !== '') {
                    $this->logUsernameChange($this->userdata->uuid, $originalUsername, $this->userdata->username);
                }
                $this->dataUpdated = true;

                return true;
            }

            $this->updateUserFailUpdate();

            if (!SkinsStorage::exists($this->userdata->uuid)) {
                SkinsStorage::copyAsSteve($this->userdata->uuid);
            }
        }
        $this->dataUpdated = false;

        return false;
    }

    /**
     * Return if data has been updated.
     */
    public function userDataUpdated(): bool
    {
        return $this->dataUpdated;
    }

    /**
     * Log the username change.
     *
     * @param $uuid string User UUID
     * @param $prev string Previous username
     * @param $new string New username
     */
    private function logUsernameChange(string $uuid, string $prev, string $new): void
    {
        Event::dispatch(new UsernameChangeEvent($uuid, $prev, $new));
    }

    /**
     * Get userdata from Mojang API.
     *
     * @param mixed
     *
     * @return bool
     */
    private function getFullUserdataApi(): bool
    {
        try {
            $this->apiUserdata = $this->mojangClient->getUuidInfo($this->request);

            return true;
        } catch (\Exception $e) {
            \Log::error($e);
            $this->apiUserdata = null;

            return false;
        }
    }

    /**
     * Show rendered avatar.
     *
     * @param int
     * @param mixed
     *
     * @throws \Throwable
     *
     * @return Avatar
     */
    public function avatarCurrentUser(int $size = 0): Avatar
    {
        $avatar = new Avatar($this->currentUserSkinImage);
        $avatar->renderAvatar($size);

        return $avatar;
    }

    /**
     * Default Avatar Isometric.
     *
     * @param int $size
     *
     * @throws \Throwable
     *
     * @return IsometricAvatar
     */
    public function isometricAvatarCurrentUser(int $size = 0): IsometricAvatar
    {
        $uuid = $this->userdata->uuid ?? env('DEFAULT_UUID');
        $timestamp = $this->userdata->updated_at->timestamp ?? \time();
        $isometricAvatar = new IsometricAvatar(
            $uuid,
            $timestamp
        );
        $isometricAvatar->render($size);

        return $isometricAvatar;
    }

    /**
     * Save skin image.
     *
     * @param mixed
     *
     * @return bool
     */
    public function saveRemoteSkin(): bool
    {
        if (!empty($this->userdata->skin) && \mb_strlen($this->userdata->skin) > 0) {
            try {
                $skinData = $this->mojangClient->getSkin($this->userdata->skin);

                return SkinsStorage::save($this->userdata->uuid, $skinData);
            } catch (\Exception $e) {
                \Log::error($e);
                $this->error = $e->getMessage();
            }
        }

        return SkinsStorage::copyAsSteve($this->userdata->uuid);
    }

    /**
     * Return rendered skin.
     *
     * @param int
     * @param string
     *
     * @throws \Throwable
     *
     * @return Skin
     */
    public function renderSkinCurrentUser(int $size = 0, string $type = ImageSection::FRONT): Skin
    {
        $skin = new Skin($this->currentUserSkinImage);
        $skin->renderSkin($size, $type);

        return $skin;
    }

    /**
     * Return a Skin object of the current user.
     */
    public function skinCurrentUser(): Skin
    {
        return new Skin($this->currentUserSkinImage);
    }

    /**
     * Set force update.
     *
     * @param bool $forceUpdate
     */
    public function setForceUpdate(bool $forceUpdate): void
    {
        $this->forceUpdate = $forceUpdate;
    }

    /**
     * Can I exec force update?
     */
    private function forceUpdatePossible(): bool
    {
        return ($this->forceUpdate) &&
            ((\time() - $this->userdata->updated_at->timestamp) > env('MIN_USERDATA_UPDATE_INTERVAL'));
    }

    /**
     * Use steve skin for given username.
     *
     * @param string
     */
    public function updateStats($type = 'request'): void
    {
        if (!empty($this->userdata->uuid) && env('STATS_ENABLED') && $this->userdata->uuid !== env('DEFAULT_UUID')) {
            if ($type === 'request') {
                $this->accountStatsRepository->incrementRequestCounter($this->userdata->uuid);
            } elseif ($type === 'search') {
                $this->accountStatsRepository->incrementSearchCounter($this->userdata->uuid);
            }
        }
    }

    /**
     * @return bool
     */
    private function initializeUuidRequest(): bool
    {
        if ($this->requestedUuidInDb()) {
            // Check if UUID is in my database
            // Data cache still valid?
            if (!$this->checkDbCache() || $this->forceUpdatePossible()) {
                // Nope, updating data
                $this->updateDbUser();
            }

            if (!SkinsStorage::exists($this->request)) {
                $this->saveRemoteSkin();
            }

            return true;
        }

        if ($this->insertNewUuid()) {
            return true;
        }

        return false;
    }

    /**
     * @throws \Exception
     *
     * @return bool
     */
    private function initializeUsernameRequest(): bool
    {
        // Check DB datacache
        if (!$this->checkDbCache() || $this->forceUpdatePossible()) {
            // Check UUID (username change/other)
            if ($this->convertRequestToUuid()) {
                if ($this->request === $this->userdata->uuid) {
                    // Nope, updating data
                    $this->request = $this->userdata->uuid;
                    $this->updateDbUser();
                } else {
                    // re-initialize process with the UUID if the name has been changed
                    return $this->initialize($this->request);
                }
            } else {
                $this->request = $this->userdata->uuid;
                $this->updateUserFailUpdate();
                SkinsStorage::copyAsSteve($this->request);
            }
        } elseif (!SkinsStorage::exists($this->request)) {
            SkinsStorage::copyAsSteve($this->request);
        }

        return true;
    }

    /**
     * Set failed request.
     *
     * @param string $errorMessage
     */
    private function setFailedRequest(string $errorMessage = ''): void
    {
        $this->userdata = null;
        $this->currentUserSkinImage = SkinsStorage::getPath(env('DEFAULT_USERNAME'));
        $this->error = $errorMessage;
        $this->request = '';
    }
}
