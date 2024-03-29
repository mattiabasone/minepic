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

/**
 * TODO: This class must be refactored. It should return a "resolution" instead of modify its parameters.
 */
class UuidResolver
{
    /**
     * Requested string.
     */
    private string $request;

    private ?string $uuid = null;
    /**
     * Userdata from/to DB.
     */
    private ?Account $account;
    /**
     * User data has been updated?
     */
    private bool $dataUpdated = false;
    /**
     * Set force update.
     */
    private bool $forceUpdate = false;

    public function __construct(
        private readonly MojangClient $mojangClient,
        private readonly Dispatcher $eventDispatcher
    ) {
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * Return loaded user data.
     */
    public function getAccount(): Account
    {
        return $this->account ?? new Account();
    }

    /**
     * Insert user data in database.
     **
     * @throws \Throwable
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
     */
    public function resolve(?string $uuid): bool
    {
        $this->dataUpdated = false;
        $this->request = $uuid ?? '';

        if ($uuid === null) {
            \Log::debug('UUID is null');

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
                \Log::error($e->getTraceAsString());
            }
        }

        return SkinsStorage::copyAsSteve($this->account->uuid);
    }

    /**
     * Set force update.
     */
    public function setForceUpdate(bool $forceUpdate): void
    {
        $this->forceUpdate = $forceUpdate;
    }

    /**
     * Check if cache is still valid.
     */
    private function checkDbCache(): bool
    {
        $accountUpdatedAtTimestamp = (int) ($this->account->updated_at->timestamp ?? 0);

        return (time() - $accountUpdatedAtTimestamp) < (int) env('USERDATA_CACHE_TIME');
    }

    /**
     * Check if an UUID is in the database.
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
    private function updateUserFailUpdate(): void
    {
        if (isset($this->account->uuid)) {
            ++$this->account->fail_count;

            $this->account->save();
        }
    }

    /**
     * Update db user data.
     */
    private function updateDbUser(): void
    {
        if (
            $this->account instanceof Account &&
            isset($this->account->username) &&
            $this->account->uuid !== ''
        ) {
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

                return;
            }

            $this->updateUserFailUpdate();

            if (!SkinsStorage::exists($this->account->uuid)) {
                SkinsStorage::copyAsSteve($this->account->uuid);
            }
        }
        $this->dataUpdated = false;
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
     */
    private function getFullUserdataApi(): ?MojangAccount
    {
        try {
            return $this->mojangClient->getUuidInfo($this->request);
        } catch (\Throwable $e) {
            \Log::error($e->getTraceAsString(), ['request' => $this->request]);

            return null;
        }
    }

    /**
     * Can I exec force update?
     */
    private function forceUpdatePossible(): bool
    {
        return $this->forceUpdate &&
            ((time() - (int) $this->account->updated_at->timestamp) > (int) env('MIN_USERDATA_UPDATE_INTERVAL'));
    }

    private function initializeUuidRequest(): bool
    {
        if ($this->requestedUuidInDb()) {
            // Check if UUID is in my database
            // Data cache still valid?
            if (!$this->checkDbCache() || $this->forceUpdatePossible()) {
                \Log::debug('Refreshing User DB Data');
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
     */
    private function setFailedRequest(string $errorMessage = ''): void
    {
        \Log::notice($errorMessage, ['request' => $this->request]);
        $this->account = null;
        $this->request = '';
    }
}
