<?php

namespace App\Helpers;
/**
 * Class Date
 */
class Date {

    /**
     * @param int $timestamp
     * @return String
     */
    public static function humanizeTimestamp(int $timestamp = 0) : String  {
        if ($timestamp == 0) {
            return 'Never';
        }
        return (string) gmdate('Y-m-d H:i:s \G\M\T', $timestamp);
    }

}