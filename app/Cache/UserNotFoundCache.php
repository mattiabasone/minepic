<?php

declare(strict_types=1);

namespace App\Cache;

use Illuminate\Support\Facades\Cache;

class UserNotFoundCache
{
    private const PREFIX = 'not_found_';
    private const TTL = 86400;

    /**
     * @param string $usernameOrUuid
     *
     * @return bool
     */
    public static function has(string $usernameOrUuid)
    {
        return Cache::has(self::PREFIX.\md5($usernameOrUuid));
    }

    /**
     * @param $usernameOrUuid
     *
     * @return bool
     */
    public static function add($usernameOrUuid)
    {
        return Cache::add(self::PREFIX.\md5($usernameOrUuid), 1, self::TTL);
    }
}
