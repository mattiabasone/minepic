<?php

declare(strict_types=1);

namespace Minepic\Image\Sections;

use Minepic\Image\Components\Component;
use Minepic\Image\Components\Side;
use Minepic\Image\ImageSection;
use Minepic\Image\LayerValidator;

class Avatar extends ImageSection
{
    /**
     * Render avatar image.
     *
     * @param int $size Avatar size
     * @param string $type Section rendered
     *
     * @throws \Minepic\Image\Exceptions\ImageCreateFromPngFailedException
     * @throws \Minepic\Image\Exceptions\ImageResourceCreationFailedException
     * @throws \Minepic\Image\Exceptions\ImageTrueColorCreationFailedException
     */
    public function render(int $size = 0, string $type = Side::FRONT): void
    {
        if ($size <= 0 || $size > (int) env('MAX_AVATAR_SIZE')) {
            $size = (int) env('DEFAULT_AVATAR_SIZE');
        }
        // generate png from url/path
        $baseSkinImage = $this->createImageFromPng($this->skinPath);
        \imagealphablending($baseSkinImage, false);
        \imagesavealpha($baseSkinImage, true);

        // Sections Coordinates
        $headSide = Component::getHead()->getSideByIdentifier($type);
        $helmSide = Component::getHeadLayer()->getSideByIdentifier($type);

        $tmpImageResource = $this->emptyBaseImage($headSide->getWidth(), $headSide->getHeight());
        \imagecopy($tmpImageResource, $baseSkinImage, 0, 0, $headSide->getTopLeft()->getX(), $headSide->getTopLeft()->getY(), $headSide->getWidth(), $headSide->getHeight());

        // if all pixel have transparency or the colors are not the same
        if ((new LayerValidator())->check($baseSkinImage, $helmSide)) {
            \imagecopymerge_alpha(
                $tmpImageResource,
                $baseSkinImage,
                0,
                0,
                $helmSide->getTopLeft()->getX(),
                $helmSide->getTopLeft()->getY(),
                $headSide->getWidth(),
                $headSide->getHeight(),
                100
            );
        }

        $this->imgResource = $this->emptyBaseImage($size, $size);
        \imagecopyresized($this->imgResource, $tmpImageResource, 0, 0, 0, 0, $size, $size, $headSide->getWidth(), $headSide->getHeight());
    }
}
