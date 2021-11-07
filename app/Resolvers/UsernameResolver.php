<?php

declare(strict_types=1);

namespace Minepic\Resolvers;

use Minepic\Cache\UserNotFoundCache;
use Minepic\Helpers\UserDataValidator;
use Minepic\Minecraft\MojangClient;
use Minepic\Repositories\AccountRepository;

class UsernameResolver
{
    /**
     * @param AccountRepository $accountRepository
     * @param MojangClient      $mojangClient
     */
    public function __construct(
        private AccountRepository $accountRepository,
        private MojangClient $mojangClient
    ) {
    }

    /**
     * @param string $username
     *
     * @throws \Exception
     *
     * @return null|string
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
