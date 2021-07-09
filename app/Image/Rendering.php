<?php

declare(strict_types=1);

namespace Minepic\Image;

use Minepic\Helpers\Storage\Files\SkinsStorage;
use Minepic\Image\Components\Side;
use Minepic\Image\Sections\Avatar;
use Minepic\Image\Sections\SkinBack;
use Minepic\Image\Sections\SkinFront;
use Minepic\Minecraft\MinecraftDefaults;

class Rendering
{
    /**
     * @param null|string $uuid
     * @param int $size
     * @param string $type
     *
     * @throws Exceptions\ImageCreateFromPngFailedException
     * @throws Exceptions\ImageResourceCreationFailedException
     * @throws Exceptions\ImageTrueColorCreationFailedException
     * @throws \Exception
     * @return Avatar
     */
    public function avatar(?string $uuid, int $size, $type = Side::FRONT): Avatar
    {
        $avatar = new Avatar(
            $this->imagePath($uuid)
        );
        $avatar->render($size, $type);

        return $avatar;
    }

    /**
     * @param null|string $uuid
     * @param int         $size
     * @param null|int    $lastUpdateTimestamp
     *
     * @throws Exceptions\SkinNotFountException
     * @throws \Throwable
     *
     * @return IsometricAvatar
     */
    public function isometricAvatar(?string $uuid, int $size, int $lastUpdateTimestamp = null): IsometricAvatar
    {
        if ($lastUpdateTimestamp === null) {
            $lastUpdateTimestamp = time();
        }

        $isometricAvatar = new IsometricAvatar(
            $uuid ?? MinecraftDefaults::STEVE_DEFAULT_SKIN_NAME,
            $lastUpdateTimestamp
        );
        $isometricAvatar->render($size);

        return $isometricAvatar;
    }

    /**
     * @param null|string $uuid
     * @param int $size
     * @throws Exceptions\ImageResourceCreationFailedException*@throws \Exception
     * @throws Exceptions\ImageCreateFromPngFailedException
     * @throws \Exception
     * @return SkinFront
     */
    public function skinFront(?string $uuid, int $size): SkinFront
    {
        $skin = new SkinFront(
            $this->imagePath($uuid)
        );
        $skin->render($size);

        return $skin;
    }

    /**
     * @param null|string $uuid
     * @param int $size
     * @throws Exceptions\ImageResourceCreationFailedException*@throws \Exception
     * @throws Exceptions\ImageCreateFromPngFailedException
     * @throws \Exception
     * @return SkinBack
     */
    public function skinBack(?string $uuid, int $size): SkinBack
    {
        $skin = new SkinBack(
            $this->imagePath($uuid)
        );
        $skin->render($size);

        return $skin;
    }

    /**
     * @param null|string $uuid
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
