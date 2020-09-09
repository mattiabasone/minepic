<?php

declare(strict_types=1);

namespace Minepic\Helpers;

/**
 * Class Date.
 */
class UserDataValidator
{
    /**
     * Checks if the given string is a valid username.
     *
     * @param $username
     *
     * @return bool
     */
    public static function isValidUsername(string $username): bool
    {
        return !(\preg_match('#[\W]+#', $username) === 1);
    }

    /**
     * Checks if the given string is a valid UUID.
     *
     * @param string $uuid
     *
     * @return bool
     */
    public static function isValidUuid(string $uuid): bool
    {
        return \preg_match('#[a-f0-9]{32}#i', $uuid) === 1 && \mb_strlen($uuid) === 32;
    }

    /**
     * Check if is an email address has invalid characters.
     *
     * @param string
     *
     * @return bool
     */
    public static function isValidEmail(string $email): bool
    {
        return \preg_match('#^[a-zA-Z0-9\.\_\%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,8}$#', $email) === 1;
    }
}
