<?php

declare(strict_types=1);

namespace App\Image;

use App\Helpers\Storage\Files\SkinsStorage;
use App\Image\Sections\Avatar;
use App\Image\Sections\Skin;

class Rendering
{
    /**
     * @param string $uuid
     * @param int    $size
     * @param string $type
     *
     * @throws Exceptions\ImageCreateFromPngFailedException
     * @throws Exceptions\ImageTrueColorCreationFailedException
     * @throws Exceptions\InvalidSectionSpecifiedException
     *
     * @return Avatar
     */
    public function avatar(string $uuid, int $size, $type = ImageSection::FRONT): Avatar
    {
        $avatar = new Avatar(
            SkinsStorage::getPath($uuid)
        );
        $avatar->render($size, $type);

        return $avatar;
    }

    /**
     * @param string   $uuid
     * @param int      $size
     * @param int|null $lastUpdateTimestamp
     *
     * @throws Exceptions\SkinNotFountException
     * @throws \Throwable
     *
     * @return IsometricAvatar
     */
    public function isometricAvatar(string $uuid, int $size, int $lastUpdateTimestamp = null): IsometricAvatar
    {
        if ($lastUpdateTimestamp === null) {
            $lastUpdateTimestamp = \time();
        }
        $isometricAvatar = new IsometricAvatar(
            $uuid,
            $lastUpdateTimestamp
        );
        $isometricAvatar->render($size);

        return $isometricAvatar;
    }

    /**
     * @param string $uuid
     * @param int    $size
     * @param string $type
     *
     * @throws \Throwable
     *
     * @return Skin
     */
    public function skin(string $uuid, int $size, $type = ImageSection::FRONT): Skin
    {
        $skin = new Skin(
            SkinsStorage::getPath($uuid)
        );
        $skin->render($size, $type);

        return $skin;
    }
}
