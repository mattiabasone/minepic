<?php

declare(strict_types=1);

namespace App;

use App\Cache\UserNotFoundCache;
use App\Events\Account\UsernameChangeEvent;
use App\Helpers\Storage\Files\SkinsStorage;
use App\Image\ImageSection;
use App\Image\IsometricAvatar;
use App\Image\Sections\Avatar;
use App\Image\Sections\Skin;
use App\Minecraft\MojangAccount;
use App\Minecraft\MojangClient;
use App\Models\Account;
use App\Repositories\AccountRepository;
use App\Repositories\AccountStatsRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

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
    private string $request = '';
    /**
     * Userdata from/to DB.
     *
     * @var Account
     */
    private ?Account $userdata;

    /**
     * Full userdata.
     *
     * @var MojangAccount
     */
    private ?MojangAccount $apiUserdata;

    /**
     * User data has been updated?
     *
     * @var bool
     */
    private bool $dataUpdated = false;

    /**
     * Set force update.
     *
     * @var bool
     */
    private bool $forceUpdate = false;

    /**
     * Current image path.
     *
     * @var string
     */
    private string $currentUserSkinImage;

    /**
     * @var AccountRepository
     */
    private AccountRepository $accountRepository;

    /**
     * @var AccountStatsRepository
     */
    private AccountStatsRepository $accountStatsRepository;

    /**
     * @var MojangClient
     */
    private MojangClient $mojangClient;

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
        $this->accountStatsRepository = $accountStatsRepository;
        $this->mojangClient = $mojangClient;
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
     * Insert user data in database.
     *
     * @param void
     *
     * @return bool
     */
    public function insertNewUuid(): bool
    {
        if (UserNotFoundCache::has($this->request)) {
            Log::debug('Cache Hit Not Found', ['request' => $this->request]);

            return false;
        }

        if ($this->getFullUserdataApi()) {
            $this->userdata = $this->accountRepository->create([
                'username' => $this->apiUserdata->getUsername(),
                'uuid' => $this->apiUserdata->getUuid(),
                'skin' => $this->apiUserdata->getSkin(),
                'cape' => $this->apiUserdata->getCape(),
            ]);

            $this->saveRemoteSkin();
            $this->currentUserSkinImage = SkinsStorage::getPath($this->apiUserdata->getUuid());

            $this->accountStatsRepository->create([
                'uuid' => $this->userdata->uuid,
                'count_search' => 0,
                'count_request' => 0,
                'time_search' => 0,
                'time_request' => 0,
            ]);

            return true;
        }

        UserNotFoundCache::add($this->request);

        return false;
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

        if ($this->initializeUuidRequest()) {
            return true;
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
                    'username' => $this->apiUserdata->getUsername(),
                    'skin' => $this->apiUserdata->getSkin(),
                    'cape' => $this->apiUserdata->getCape(),
                    'fail_count' => 0,
                ], $this->userdata->id);

                $this->userdata->touch();
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
            Log::error($e->getTraceAsString(), ['request' => $this->request]);
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
        if (!empty($this->userdata->skin) && $this->userdata->skin !== '') {
            try {
                $skinData = $this->mojangClient->getSkin($this->userdata->skin);

                return SkinsStorage::save($this->userdata->uuid, $skinData);
            } catch (\Exception $e) {
                Log::error($e->getTraceAsString());
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
                Log::debug('Refreshing User DB Data');
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
     * Set failed request.
     *
     * @param string $errorMessage
     */
    private function setFailedRequest(string $errorMessage = ''): void
    {
        Log::notice($errorMessage, ['request' => $this->request]);
        $this->userdata = null;
        $this->currentUserSkinImage = SkinsStorage::getPath(env('DEFAULT_USERNAME'));
        $this->request = '';
    }
}
