<?php

declare(strict_types=1);

namespace App\Resolvers;

use App\Cache\UserNotFoundCache;
use App\Minecraft\MojangClient;
use App\Repositories\AccountRepository;
use Illuminate\Support\Facades\Log;

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
     * @return string
     */
    public function resolve(string $username): ?string
    {
        /** @var \App\Models\Account $account */
        $account = $this->accountRepository->findLastUpdatedByUsername($username);
        if ($account) {
            return $account->uuid;
        }

        if (UserNotFoundCache::has($username)) {
            Log::debug('User not found cache hit');

            return env('DEFAULT_UUID');
        }

        try {
            $mojangAccount = $this->mojangClient->sendUsernameInfoRequest($username);

            return $mojangAccount->getUuid();
        } catch (\Throwable $exception) {
            UserNotFoundCache::add($username);

            return env('DEFAULT_UUID');
        }
    }
}
