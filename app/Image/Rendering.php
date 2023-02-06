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
     * @param string $type
     *
     * @throws Exceptions\ImageCreateFromPngFailedException
     * @throws Exceptions\ImageResourceCreationFailedException
     * @throws Exceptions\ImageTrueColorCreationFailedException
     * @throws \Exception
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
     * @throws Exceptions\SkinNotFountException
     * @throws \Throwable
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
     * @throws Exceptions\ImageResourceCreationFailedException*@throws \Exception
     * @throws Exceptions\ImageCreateFromPngFailedException
     * @throws \Exception
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
     * @throws Exceptions\ImageResourceCreationFailedException*@throws \Exception
     * @throws Exceptions\ImageCreateFromPngFailedException
     * @throws \Exception
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
     * @throws \Exception
     */
    private function imagePath(?string $uuid): string
    {
        return $uuid !== null ? SkinsStorage::getPath($uuid) : SkinsStorage::getPath(MinecraftDefaults::STEVE_DEFAULT_SKIN_NAME);
    }
}
