<?php

declare(strict_types=1);

namespace Minepic\Helpers\Storage;

use Minepic\Minecraft\MinecraftDefaults;

class Storage
{
    /**
     * Storage folder type.
     */
    protected static string $folder;

    public static function getPath(string $uuid): string
    {
        if ($uuid === '') {
            $uuid = MinecraftDefaults::getRandomDefaultSkin();
        }

        return sprintf(storage_path(static::$folder.'/%s.png'), $uuid);
    }

    public static function exists(string $uuid): bool
    {
        return file_exists(static::getPath($uuid));
    }

    /**
     * Save the skin to file.
     *
     * @param mixed  $rawData
     */
    public static function save(string $uuid, $rawData): bool
    {
        $fp = fopen(static::getPath($uuid), 'wb');
        if (\is_resource($fp)) {
            fwrite($fp, $rawData);
            fclose($fp);

            return true;
        }

        return false;
    }

    /**
     * Use Steve file for given uuid.
     */
    public static function copyAsSteve(string $name): bool
    {
        if ($name !== '') {
            return copy(
                static::getPath(MinecraftDefaults::getRandomDefaultSkin()),
                static::getPath($name)
            );
        }

        return false;
    }
}
