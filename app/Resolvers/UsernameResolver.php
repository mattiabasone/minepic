<?php

declare(strict_types=1);

namespace App\Resolvers;

use App\Cache\UserNotFoundCache;
use App\Helpers\UserDataValidator;
use App\Minecraft\MinecraftDefaults;
use App\Minecraft\MojangClient;
use App\Repositories\AccountRepository;

class UsernameResolver
{
    /**
     * @var AccountRepository
     */
    private AccountRepository $accountRepository;
    /**
     * @var MojangClient
     */
    private MojangClient $mojangClient;

    public function __construct(
        AccountRepository $accountRepository,
        MojangClient $mojangClient
    ) {
        $this->accountRepository = $accountRepository;
        $this->mojangClient = $mojangClient;
    }

    /**
     * @param string $username
     *
     * @throws \Exception
     *
     * @return string|null
     */
    public function resolve(string $username): ?string
    {
        if (!$this->isValidUsername($username)) {
            return null;
        }

        /** @var \App\Models\Account $account */
        $account = $this->accountRepository->findLastUpdatedByUsername($username);
        if ($account !== null) {
            return $account->uuid;
        }

        if (!UserNotFoundCache::has($username)) {
            try {
                $mojangAccount = $this->mojangClient->sendUsernameInfoRequest($username);

                return $mojangAccount->getUuid();
            } catch (\Throwable $exception) {
                UserNotFoundCache::add($username);
            }
        }

        return null;
    }

    /**
     * @param string $username
     *
     * @return bool
     */
    private function isValidUsername(string $username): bool
    {
        return UserDataValidator::isValidUsername($username) || UserDataValidator::isValidEmail($username);
    }
}
