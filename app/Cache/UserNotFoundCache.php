<?php

declare(strict_types=1);

namespace Minepic\Cache;

class UserNotFoundCache
{
    private const PREFIX = 'not_found_';
    private const TTL = 86400;

    public static function has(string $usernameOrUuid): bool
    {
        if (\Cache::has(self::PREFIX.md5($usernameOrUuid))) {
            \Log::debug('User not found cache hit: '.$usernameOrUuid);

            return true;
        }

        return false;
    }

    public static function add(string $usernameOrUuid): bool
    {
        return \Cache::add(self::PREFIX.md5($usernameOrUuid), 1, self::TTL);
    }
}
