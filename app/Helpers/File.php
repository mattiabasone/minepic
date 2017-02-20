<?php

namespace App\Helpers;
/**
 * Class DateHelper
 */
class File {

    /**
     * Skin Path
     *
     * @param string $uuid
     * @return string
     */
    public static function getFullPath(string $uuid = ''): string {
        if ($uuid == '') {
            $uuid = env('DEFAULT_USERNAME');
        }
        return sprintf(storage_path(env('SKINS_FOLDER').'/%s.png'), $uuid);
    }

    /**
     * Checks if file exists
     *
     * @param string $uuid
     * @return bool
     */
    public static function exists(string $uuid = ''): bool {
        return file_exists(self::getFullPath($uuid));
    }

    /**
     * Save the skin to file
     *
     * @param string $uuid
     * @param mixed $skinData
     * @return bool
     */
    public static function saveSkin(string $uuid, $skinData): bool {
        $fp = fopen(self::getFullPath($uuid), 'w');
        if ($fp) {
            fwrite($fp, $skinData);
            fclose($fp);
            return true;
        }
        return false;
    }

    /**
     * Use steve skin for given username
     *
     * @access public
     * @param mixed
     * @return bool
     */
    public static function copyAsSteve(string $string = ''): bool {
        if ($string != '') {
            return copy(
                File::getFullPath(env('DEFAULT_USERNAME')),
                File::getFullPath($string)
            );
        }
        return false;
    }
}