<?php

declare(strict_types=1);

namespace App\Image;

use App\Helpers\Storage\Files\SkinsStorage;
use App\Image\Sections\Avatar;
use App\Image\Sections\Skin;
use App\Minecraft\MinecraftDefaults;

class Rendering
{
    /**
     * @param string|null $uuid
     * @param int         $size
     * @param string      $type
     *
     * @throws Exceptions\ImageCreateFromPngFailedException
     * @throws Exceptions\ImageTrueColorCreationFailedException
     * @throws Exceptions\InvalidSectionSpecifiedException
     *
     * @return Avatar
     */
    public function avatar(?string $uuid, int $size, $type = ImageSection::FRONT): Avatar
    {
        $avatar = new Avatar(
            $this->imagePath($uuid)
        );
        $avatar->render($size, $type);

        return $avatar;
    }

    /**
     * @param string|null $uuid
     * @param int         $size
     * @param int|null    $lastUpdateTimestamp
     *
     * @throws Exceptions\SkinNotFountException
     * @throws \Throwable
     *
     * @return IsometricAvatar
     */
    public function isometricAvatar(?string $uuid, int $size, int $lastUpdateTimestamp = null): IsometricAvatar
    {
        if ($lastUpdateTimestamp === null) {
            $lastUpdateTimestamp = \time();
        }

        $isometricAvatar = new IsometricAvatar(
            $uuid ?? MinecraftDefaults::STEVE_DEFAULT_SKIN_NAME,
            $lastUpdateTimestamp
        );
        $isometricAvatar->render($size);

        return $isometricAvatar;
    }

    /**
     * @param string|null $uuid
     * @param int         $size
     * @param string      $type
     *
     * @throws \Throwable
     *
     * @return Skin
     */
    public function skin(?string $uuid, int $size, $type = ImageSection::FRONT): Skin
    {
        $skin = new Skin(
            $this->imagePath($uuid)
        );
        $skin->render($size, $type);

        return $skin;
    }

    /**
     * @param string|null $uuid
     *
     * @throws \Exception
     *
     * @return string
     */
    private function imagePath(?string $uuid): string
    {
        return $uuid !== null ? SkinsStorage::getPath($uuid) : SkinsStorage::getPath(MinecraftDefaults::STEVE_DEFAULT_SKIN_NAME);
    }
}
