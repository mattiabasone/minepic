<?php

declare(strict_types=1);

namespace App\Resolvers;

use App\Cache\UserNotFoundCache;
use App\Events\Account\UsernameChangeEvent;
use App\Helpers\Storage\Files\SkinsStorage;
use App\Minecraft\MinecraftDefaults;
use App\Minecraft\MojangAccount;
use App\Minecraft\MojangClient;
use App\Models\Account;
use App\Repositories\AccountRepository;
use App\Repositories\AccountStatsRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class UuidResolver
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
    private string $uuid = MinecraftDefaults::UUID;
    /**
     * Userdata from/to DB.
     *
     * @var Account
     */
    private ?Account $account;
    /**
     * Full userdata.
     *
     * @var MojangAccount
     */
    private ?MojangAccount $mojangAccount;
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
    public function getUuid(): string
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
        $accountUpdatedAtTimestamp = $this->account->updated_at->timestamp ?? 0;

        return (\time() - $accountUpdatedAtTimestamp) < env('USERDATA_CACHE_TIME');
    }

    /**
     * Return loaded user data.
     *
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account ?? new Account();
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

        $this->account = $account;
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
            $this->account = $this->accountRepository->create([
                'username' => $this->mojangAccount->getUsername(),
                'uuid' => $this->mojangAccount->getUuid(),
                'skin' => $this->mojangAccount->getSkin(),
                'cape' => $this->mojangAccount->getCape(),
            ]);

            $this->saveRemoteSkin();

            $this->accountStatsRepository->create([
                'uuid' => $this->account->uuid,
                'count_search' => 0,
                'count_request' => 0,
                'time_search' => 0,
                'time_request' => 0,
            ]);

            $this->uuid = $this->mojangAccount->getUuid();

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
    public function resolve(string $string): bool
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
        if (isset($this->account->uuid)) {
            ++$this->account->fail_count;

            return $this->account->save();
        }

        return false;
    }

    /**
     * Update db user data.
     */
    private function updateDbUser(): bool
    {
        if (isset($this->account->username) && $this->account->uuid !== '') {
            // Get data from API
            if ($this->getFullUserdataApi()) {
                $originalUsername = $this->account->username;
                // Update database
                $this->accountRepository->update([
                    'username' => $this->mojangAccount->getUsername(),
                    'skin' => $this->mojangAccount->getSkin(),
                    'cape' => $this->mojangAccount->getCape(),
                    'fail_count' => 0,
                ], $this->account->id);

                $this->account->touch();
                $this->account->refresh();

                // Update skin
                $this->saveRemoteSkin();

                // Log username change
                if ($this->account->username !== $originalUsername && $originalUsername !== '') {
                    $this->logUsernameChange($this->account->uuid, $originalUsername, $this->account->username);
                }
                $this->dataUpdated = true;

                return true;
            }

            $this->updateUserFailUpdate();

            if (!SkinsStorage::exists($this->account->uuid)) {
                SkinsStorage::copyAsSteve($this->account->uuid);
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
            $this->mojangAccount = $this->mojangClient->getUuidInfo($this->request);

            return true;
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString(), ['request' => $this->request]);
            $this->mojangAccount = null;

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
        if (!empty($this->account->skin) && $this->account->skin !== '') {
            try {
                $skinData = $this->mojangClient->getSkin($this->account->skin);

                return SkinsStorage::save($this->account->uuid, $skinData);
            } catch (\Exception $e) {
                Log::error($e->getTraceAsString());
            }
        }

        return SkinsStorage::copyAsSteve($this->account->uuid);
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
            ((\time() - $this->account->updated_at->timestamp) > env('MIN_USERDATA_UPDATE_INTERVAL'));
    }

    /**
     * Use steve skin for given username.
     *
     * @param string
     */
    public function updateStats(): void
    {
        if (!empty($this->account->uuid) && $this->account->uuid !== MinecraftDefaults::UUID && env('STATS_ENABLED')) {
            $this->accountStatsRepository->incrementRequestCounter($this->account->uuid);
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
        $this->account = null;
        $this->request = '';
    }
}
