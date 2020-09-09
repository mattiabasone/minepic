<?php

declare(strict_types=1);

namespace Minepic\Cache;

use Cache;
use Log;

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
            Log::debug('User not found cache hit: '.$usernameOrUuid);

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
