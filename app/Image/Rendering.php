<?php

declare(strict_types=1);

namespace App\Image;

use App\Image\Sections\Avatar;
use App\Image\Sections\Skin;

class Rendering
{
    /**
     * @param string $imagePath
     * @param int    $size
     * @param string $type
     *
     * @throws Exceptions\ImageCreateFromPngFailedException
     * @throws Exceptions\ImageTrueColorCreationFailedException
     * @throws Exceptions\InvalidSectionSpecifiedException
     *
     * @return Avatar
     */
    public function avatar(string $imagePath, int $size, $type = ImageSection::FRONT): Avatar
    {
        $avatar = new Avatar($imagePath);
        $avatar->render($size, $type);

        return $avatar;
    }

    public function isometricAvatar(string $imagePath, int $size): IsometricAvatar
    {
        $uuid = $this->userdata->uuid ?? env('DEFAULT_UUID');
        $timestamp = $this->userdata->updated_at->timestamp ?? \time();
        $isometricAvatar = new IsometricAvatar(
            $uuid,
            $timestamp
        );
        $isometricAvatar->render($size);

        return $isometricAvatar;
    }

    /**
     * @param string $imagePath
     * @param int    $size
     * @param string $type
     *
     * @throws \Throwable
     *
     * @return Skin
     */
    public function skin(string $imagePath, int $size, $type = ImageSection::FRONT): Skin
    {
        $skin = new Skin($imagePath);
        $skin->render($size, $type);

        return $skin;
    }
}
