<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Class Date.
 */
class Date
{
    /**
     * @param int $timestamp
     *
     * @return string
     */
    public static function humanizeTimestamp(int $timestamp = 0): string
    {
        if (0 == $timestamp) {
            return 'Never';
        }

        return (string) \gmdate('Y-m-d H:i:s \G\M\T', $timestamp);
    }
}
