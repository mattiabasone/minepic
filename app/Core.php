<?php

declare(strict_types=1);

namespace App;

use App\Cache\UserNotFoundCache;
use App\Events\Account\UsernameChangeEvent;
use App\Helpers\Storage\Files\SkinsStorage;
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
     * @var string
     */
    private string $uuid;
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
     * @return string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
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

        if ($account === null) {
            return false;
        }

        $this->userdata = $account;
        $this->uuid = $account->uuid;

        return true;
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
     * @throws \Throwable
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
    public function updateStats(): void
    {
        if (!empty($this->userdata->uuid) && env('STATS_ENABLED') && $this->userdata->uuid !== env('DEFAULT_UUID')) {
            $this->accountStatsRepository->incrementRequestCounter($this->userdata->uuid);
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
        $this->uuid = env('DEFAULT_UUID');
        $this->userdata = null;
        $this->request = '';
    }
}
