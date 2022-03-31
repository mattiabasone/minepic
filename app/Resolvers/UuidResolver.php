<?php

declare(strict_types=1);

namespace Minepic\Resolvers;

use Illuminate\Contracts\Events\Dispatcher;
use Log;
use Minepic\Cache\UserNotFoundCache;
use Minepic\Events\Account\AccountCreatedEvent;
use Minepic\Events\Account\UsernameChangeEvent;
use Minepic\Helpers\Storage\Files\SkinsStorage;
use Minepic\Minecraft\MojangAccount;
use Minepic\Minecraft\MojangClient;
use Minepic\Models\Account;

class UuidResolver
{
    /**
     * Requested string.
     *
     * @var string
     */
    private string $request;
    /**
     * @var null|string
     */
    private ?string $uuid = null;
    /**
     * Userdata from/to DB.
     *
     * @var null|Account
     */
    private ?Account $account;
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
     * @param MojangClient $mojangClient    Client for Mojang API
     * @param Dispatcher   $eventDispatcher
     */
    public function __construct(
        private MojangClient $mojangClient,
        private Dispatcher $eventDispatcher
    ) {}

    /**
     * @return null|string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
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
     * Insert user data in database.
     **
     * @throws \Throwable
     *
     * @return bool
     */
    public function insertNewUuid(): bool
    {
        if ($this->request === '' || UserNotFoundCache::has($this->request)) {
            return false;
        }

        $mojangAccount = $this->getFullUserdataApi();
        if ($mojangAccount instanceof MojangAccount) {
            $this->account = Account::create([
                'username' => $mojangAccount->getUsername(),
                'uuid' => $mojangAccount->getUuid(),
                'skin' => $mojangAccount->getSkin(),
                'cape' => $mojangAccount->getCape(),
            ]);

            $this->saveRemoteSkin();

            $this->uuid = $this->account->uuid;
            $this->eventDispatcher->dispatch(new AccountCreatedEvent($this->account));

            return true;
        }

        UserNotFoundCache::add($this->request);

        return false;
    }

    /**
     * Check requested string and initialize objects.
     *
     * @param null|string $uuid
     *
     * @throws \Throwable
     * @return bool
     */
    public function resolve(?string $uuid): bool
    {
        $this->dataUpdated = false;
        $this->request = $uuid ?? '';

        if ($uuid === null) {
            Log::debug('UUID is null');

            return false;
        }

        if ($this->initializeUuidRequest()) {
            return true;
        }

        $this->setFailedRequest('Account not found');

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
     * Save skin image.
     *
     * @throws \Throwable
     *
     * @return bool
     */
    public function saveRemoteSkin(): bool
    {
        if ($this->account instanceof Account === false) {
            return false;
        }

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
     * Check if cache is still valid.
     *
     * @return bool
     */
    private function checkDbCache(): bool
    {
        $accountUpdatedAtTimestamp = (int) ($this->account->updated_at->timestamp ?? 0);

        return (time() - $accountUpdatedAtTimestamp) < (int) env('USERDATA_CACHE_TIME');
    }

    /**
     * Check if an UUID is in the database.
     *
     * @return bool Returns true/false
     */
    private function requestedUuidInDb(): bool
    {
        $this->account = Account::query()
            ->whereUuid($this->request)
            ->first();

        if ($this->account === null) {
            return false;
        }

        $this->uuid = $this->account->uuid;

        return true;
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
            $mojangAccount = $this->getFullUserdataApi();
            if ($mojangAccount instanceof MojangAccount) {
                $previousUsername = $this->account->username;
                // Update database
                $this->account->username = $mojangAccount->getUsername();
                $this->account->skin = $mojangAccount->getSkin() ?? '';
                $this->account->cape = $mojangAccount->getCape() ?? '';
                $this->account->fail_count = 0;
                $this->account->save();

                $this->account->refresh();

                // Update skin
                $this->saveRemoteSkin();
                $this->logUsernameChange($this->account, $previousUsername);

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
     * Log the username change.
     *
     * @param Account $account User Account
     * @param string $previousUsername Previous username
     */
    private function logUsernameChange(Account $account, string $previousUsername): void
    {
        if ($account->username !== $previousUsername && $previousUsername !== '') {
            $this->eventDispatcher->dispatch(
                new UsernameChangeEvent($account->uuid, $previousUsername, $account->username)
            );
        }
    }

    /**
     * Get userdata from Mojang/Minecraft API.
     *
     * @return null|MojangAccount
     */
    private function getFullUserdataApi(): ?MojangAccount
    {
        try {
            return $this->mojangClient->getUuidInfo($this->request);
        } catch (\Throwable $e) {
            Log::error($e->getTraceAsString(), ['request' => $this->request]);

            return null;
        }
    }

    /**
     * Can I exec force update?
     */
    private function forceUpdatePossible(): bool
    {
        return ($this->forceUpdate) &&
            ((time() - (int) $this->account->updated_at->timestamp) > (int) env('MIN_USERDATA_UPDATE_INTERVAL'));
    }

    /**
     * @throws \Throwable
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

            if (!SkinsStorage::exists($this->account->uuid)) {
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
