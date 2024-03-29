<?php

declare(strict_types=1);

namespace Minepic\Resolvers;

use Minepic\Cache\UserNotFoundCache;
use Minepic\Helpers\UserDataValidator;
use Minepic\Minecraft\MojangClient;
use Minepic\Repositories\AccountRepository;

class UsernameResolver
{
    public function __construct(
        private AccountRepository $accountRepository,
        private MojangClient $mojangClient
    ) {
    }

    /**
     * @throws \Exception
     */
    public function resolve(string $username): ?string
    {
        if (!$this->isValidUsername($username)) {
            return null;
        }

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

    private function isValidUsername(string $username): bool
    {
        return UserDataValidator::isValidUsername($username) || UserDataValidator::isValidEmail($username);
    }
}
