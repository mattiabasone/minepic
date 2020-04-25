<?php

declare(strict_types=1);

namespace App\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserNotFoundCache
{
    private const PREFIX = 'not_found_';
    private const TTL = 86400;

    /**
     * @param string $usernameOrUuid
     *
     * @return bool
     */
    public static function has(string $usernameOrUuid): bool
    {
        if (Cache::has(self::PREFIX.\md5($usernameOrUuid))) {
            Log::debug('User not found cache hit');

            return true;
        }

        return false;
    }

    /**
     * @param $usernameOrUuid
     *
     * @return bool
     */
    public static function add($usernameOrUuid): bool
    {
        return Cache::add(self::PREFIX.\md5($usernameOrUuid), 1, self::TTL);
    }
}
