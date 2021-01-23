<?php

declare(strict_types=1);

namespace Minepic\Image\Sections;

use Minepic\Image\Components\Component;
use Minepic\Image\Components\Side;
use Minepic\Image\ImageSection;

class Avatar extends ImageSection
{
    /**
     * Max Standard Deviation value for helm check.
     */
    private const DEFAULT_STANDARD_DEVIATION = 0.2;

    /**
     * Mean Alpha value (Helm).
     *
     * @var int
     */
    private int $meanAlpha = 0;

    /**
     * Red Standard Deviation value (Helm).
     *
     * @var float
     */
    private float $redStdDev = 0.0;

    /**
     * Green Standard Deviation value (Helm).
     *
     * @var float
     */
    private float $greenStdDev = 0.0;

    /**
     * Blue Standard Deviation value (Helm).
     *
     * @var float
     */
    private float $blueStdDev = 0;

    /**
     * Calculate sttdev for merging helm.
     *
     * @param $headSection
     */
    protected function calculateHelmStandardDeviation($headSection): void
    {
        // Check for helm image
        $allRed = [];
        $allGreen = [];
        $allBlue = [];
        $allAlpha = [];
        $x = 0;
        while ($x < 8) {
            $y = 0;
            while ($y < 8) {
                $color = \imagecolorat($headSection, $x, $y);
                $colors = \imagecolorsforindex($headSection, $color);
                $allRed[] = $colors['red'];
                $allGreen[] = $colors['green'];
                $allBlue[] = $colors['blue'];
                $allAlpha[] = $colors['alpha'];
                ++$y;
            }
            ++$x;
        }
        // mean value for each color
        $meanRed = \array_sum($allRed) / 64;
        $meanGreen = \array_sum($allGreen) / 64;
        $meanBlue = \array_sum($allBlue) / 64;
        $this->meanAlpha = (int) \round(\array_sum($allAlpha) / 64);
        // Arrays deviation
        $devsRed = [];
        $devsGreen = [];
        $devsBlue = [];
        $i = 0;
        while ($i < 64) {
            $devsRed[] = ($allRed[$i] - $meanRed) ** 2;
            $devsGreen[] = ($allGreen[$i] - $meanGreen) ** 2;
            $devsBlue[] = ($allBlue[$i] - $meanBlue) ** 2;
            ++$i;
        }
        // stddev for each color
        $this->redStdDev = \sqrt(\array_sum($devsRed) / 64);
        $this->greenStdDev = \sqrt(\array_sum($devsGreen) / 64);
        $this->blueStdDev = \sqrt(\array_sum($devsBlue) / 64);
    }

    /**
     * Checks if base image has helm for section.
     *
     * @param resource $baseSkinImage
     * @param Side     $helmSide
     *
     *@throws \Minepic\Image\Exceptions\ImageTrueColorCreationFailedException
     *
     * @return bool
     */
    private function hasHelm($baseSkinImage, Side $helmSide): bool
    {
        $helmCheckImage = $this->createHelmCheckImage($baseSkinImage, $helmSide);
        $this->calculateHelmStandardDeviation($helmCheckImage);

        return $this->isValidHelmStandardDeviation() || $this->meanAlpha === 127;
    }

    /**
     * @return bool
     */
    private function isValidHelmStandardDeviation(): bool
    {
        return ($this->redStdDev > self::DEFAULT_STANDARD_DEVIATION && $this->greenStdDev > self::DEFAULT_STANDARD_DEVIATION) ||
            ($this->redStdDev > self::DEFAULT_STANDARD_DEVIATION && $this->blueStdDev > self::DEFAULT_STANDARD_DEVIATION) ||
            ($this->greenStdDev > self::DEFAULT_STANDARD_DEVIATION && $this->blueStdDev > self::DEFAULT_STANDARD_DEVIATION);
    }

    /**
     * Render avatar image.
     *
     * @param int    $size Avatar size
     * @param string $type Section rendered
     *
     * @throws \Minepic\Image\Exceptions\ImageCreateFromPngFailedException
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

        // Head
        $this->imgResource = $this->createTrueColorSquare($size);

        // Sections Coordinates
        $headSide = Component::getHead()->getSideByIdentifier($type);
        $helmSide = Component::getHeadLayer()->getSideByIdentifier($type);

        \imagecopyresampled($this->imgResource, $baseSkinImage, 0, 0, $headSide->getTopLeft()->getX(), $headSide->getTopLeft()->getY(), $size, $size, $headSide->getWidth(), $headSide->getHeight());

        // if all pixel have transparency or the colors are not the same
        if ($this->hasHelm($baseSkinImage, $helmSide)) {
            $helm = $this->createTrueColorSquare($size);
            \imagealphablending($helm, false);
            \imagesavealpha($helm, true);
            \imagefilledrectangle($helm, 0, 0, $size, $size, $this->colorAllocateAlpha($helm));
            \imagecopyresampled($helm, $baseSkinImage, 0, 0, $helmSide->getTopLeft()->getX(), $helmSide->getTopLeft()->getY(), $size, $size, 8, 8);
            $merge = $this->createTrueColorSquare($size);
            \imagecopy($merge, $this->imgResource, 0, 0, 0, 0, $size, $size);
            \imagecopy($merge, $helm, 0, 0, 0, 0, $size, $size);
            \imagecopymerge($this->imgResource, $merge, 0, 0, 0, 0, $size, $size, 0);
            $this->imgResource = $merge;
            unset($merge);
        }
    }

    /**
     * @param $image
     * @param Side $helmSide
     *
     * @throws \Minepic\Image\Exceptions\ImageTrueColorCreationFailedException
     *
     * @return resource
     */
    public function createHelmCheckImage($image, Side $helmSide)
    {
        $width = $helmSide->getWidth();
        $height = $helmSide->getHeight();
        $helmCheckImage = $this->createTrueColorSquare($width);
        \imagealphablending($helmCheckImage, false);
        \imagesavealpha($helmCheckImage, true);
        \imagefilledrectangle($helmCheckImage, 0, 0, 8, 8, $this->colorAllocateAlpha($helmCheckImage));
        \imagecopyresampled($helmCheckImage, $image, 0, 0, $helmSide->getTopLeft()->getX(), $helmSide->getTopLeft()->getY(), $width, $height, $width, $height);

        return $helmCheckImage;
    }
}
