<?php

declare(strict_types=1);

namespace Minepic\Image\Sections;

use Minepic\Image\Components\Side;
use Minepic\Image\Exceptions\ImageResourceCreationFailedException;
use Minepic\Image\ImageSection;

class Skin extends ImageSection
{
    /**
     * @param string $type
     *
     * @return string
     */
    private function checkType(string $type): string
    {
        if ($type !== Side::BACK) {
            $type = Side::FRONT;
        }

        return $type;
    }

    /**
     * @param $skinHeight
     *
     * @return int
     */
    private function checkHeight($skinHeight): int
    {
        if ($skinHeight === 0 || $skinHeight < 0 || $skinHeight > (int) env('MAX_SKINS_SIZE')) {
            $skinHeight = (int) env('DEFAULT_SKIN_SIZE');
        }

        return $skinHeight;
    }

    /**
     * Create a PNG with raw texture.
     *
     * @throws \Minepic\Image\Exceptions\ImageCreateFromPngFailedException
     */
    public function prepareTextureDownload(): void
    {
        $this->imgResource = $this->createImageFromPng($this->skinPath);
        \imagealphablending($this->imgResource, true);
        \imagesavealpha($this->imgResource, true);
    }

    /**
     * Render skin.
     *
     * @param int
     * @param string
     *
     * @throws \Throwable
     */
    public function render(int $skin_height = 256, $type = Side::FRONT): void
    {
        $type = $this->checkType($type);
        $skin_height = $this->checkHeight($skin_height);

        $image = $this->createImageFromPng($this->skinPath);

        $tmpImageResource = $this->emptyBaseImage(16, 32);

        $tmpAvatar = new Avatar($this->skinPath);
        $tmpAvatar->render(8, $type);
        // Front
        if ($type === Side::FRONT) {
            // Head
            \imagecopyresized($tmpImageResource, $tmpAvatar->getResource(), 4, 0, 0, 0, 8, 8, 8, 8);
            // Body Front
            \imagecopyresized($tmpImageResource, $image, 4, 8, 20, 20, 8, 12, 8, 12);
            // Right Arm (left on img)
            $r_arm = \imagecreatetruecolor(4, 12);
            \imagecopy($r_arm, $image, 0, 0, 44, 20, 4, 12);
            \imagecopyresized($tmpImageResource, $r_arm, 0, 8, 0, 0, 4, 12, 4, 12);
            // Right leg (left on img)
            $r_leg = \imagecreatetruecolor(4, 20);
            \imagecopy($r_leg, $image, 0, 0, 4, 20, 4, 12);
            \imagecopyresized($tmpImageResource, $r_leg, 4, 20, 0, 0, 4, 12, 4, 12);
        } else {
            // Head
            \imagecopyresized($tmpImageResource, $tmpAvatar->getResource(), 4, 0, 0, 0, 8, 8, 8, 8);
            // Body Back
            \imagecopyresized($tmpImageResource, $image, 4, 8, 32, 20, 8, 12, 8, 12);
            // Right Arm Back (left on img)
            $r_arm = \imagecreatetruecolor(4, 12);
            \imagecopy($r_arm, $image, 0, 0, 52, 20, 4, 12);
            \imagecopyresized($tmpImageResource, $r_arm, 0, 8, 0, 0, 4, 12, 4, 12);
            // Right leg Back (left on img)
            $r_leg = \imagecreatetruecolor(4, 20);
            \imagecopy($r_leg, $image, 0, 0, 12, 20, 4, 12);
            \imagecopyresized($tmpImageResource, $r_leg, 4, 20, 0, 0, 4, 12, 4, 12);
        }

        // Left Arm (right flipped)
        $l_arm = \imagecreatetruecolor(4, 12);
        for ($x = 0; $x < 4; ++$x) {
            \imagecopy($l_arm, $r_arm, $x, 0, 4 - $x - 1, 0, 1, 12);
        }
        \imagecopyresized($tmpImageResource, $l_arm, 12, 8, 0, 0, 4, 12, 4, 12);
        // Left leg (right flipped)
        $l_leg = \imagecreatetruecolor(4, 20);
        for ($x = 0; $x < 4; ++$x) {
            \imagecopy($l_leg, $r_leg, $x, 0, 4 - $x - 1, 0, 1, 20);
        }
        \imagecopyresized($tmpImageResource, $l_leg, 8, 20, 0, 0, 4, 12, 4, 12);

        $scale = $skin_height / 32;
        if ($scale === 0) {
            $scale = 1;
        }
        $skin_width = (int) \round($scale * (16));

        $this->imgResource = $this->emptyBaseImage($skin_width, $skin_height);
        \imagecopyresized($this->imgResource, $tmpImageResource, 0, 0, 0, 0, $skin_width, $skin_height, 16, 32);
    }

    /**
     * @param int $width
     * @param int $height
     *
     * @throws ImageResourceCreationFailedException
     *
     * @return resource
     */
    private function emptyBaseImage(int $width, int $height)
    {
        $tmpImageResource = \imagecreatetruecolor($width, $height);
        if ($tmpImageResource === false) {
            throw new ImageResourceCreationFailedException('imagecreatetruecolor() failed');
        }
        \imagealphablending($tmpImageResource, false);
        \imagesavealpha($tmpImageResource, true);
        $transparent = \imagecolorallocatealpha($tmpImageResource, 255, 255, 255, 127);
        \imagefilledrectangle($tmpImageResource, 0, 0, $width, $height, $transparent);

        return $tmpImageResource;
    }
}
