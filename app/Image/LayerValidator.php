<?php

declare(strict_types=1);

namespace Minepic\Image;

use Minepic\Image\Components\Side;
use Minepic\Image\Exceptions\ImageTrueColorCreationFailedException;

class LayerValidator
{
    /**
     * Max Standard Deviation value for layer check.
     */
    private const DEFAULT_STANDARD_DEVIATION = 0.2;
    private int $meanAlpha;
    private float $redStdDev;
    private float $greenStdDev;
    private float $blueStdDev;

    /**
     * Checks if base image has helm for section.
     *
     * @param resource $sourceImage
     * @param Side     $side
     *
     *@return bool
     *@throws \Minepic\Image\Exceptions\ImageTrueColorCreationFailedException
     *
     */
    public function check($sourceImage, Side $side): bool
    {
        $checkImage = $this->createCheckImage($sourceImage, $side);
        $this->calculate($checkImage, $side);

        return $this->validStdDev() || $this->meanAlpha === 127;
    }

    /**
     * Calculate sttdev for merging helm.
     *
     * @param $checkImage
     */
    protected function calculate($checkImage, Side $side): void
    {
        // Check for helm image
        $allRed = [];
        $allGreen = [];
        $allBlue = [];
        $allAlpha = [];
        $x = 0;
        while ($x < $side->getWidth()) {
            $y = 0;
            while ($y < $side->getHeight()) {
                $color = \imagecolorat($checkImage, $x, $y);
                $colors = \imagecolorsforindex($checkImage, $color);
                $allRed[] = $colors['red'];
                $allGreen[] = $colors['green'];
                $allBlue[] = $colors['blue'];
                $allAlpha[] = $colors['alpha'];
                ++$y;
            }
            ++$x;
        }
        // mean value for each color
        $totalPixels = $side->getWidth() * $side->getHeight();
        $meanRed = \array_sum($allRed) / $totalPixels;
        $meanGreen = \array_sum($allGreen) / $totalPixels;
        $meanBlue = \array_sum($allBlue) / $totalPixels;
        $this->meanAlpha = (int) \round(\array_sum($allAlpha) / $totalPixels);
        // Arrays deviation
        $devsRed = [];
        $devsGreen = [];
        $devsBlue = [];
        $i = 0;
        while ($i < $totalPixels) {
            $devsRed[] = ($allRed[$i] - $meanRed) ** 2;
            $devsGreen[] = ($allGreen[$i] - $meanGreen) ** 2;
            $devsBlue[] = ($allBlue[$i] - $meanBlue) ** 2;
            ++$i;
        }
        // stddev for each color
        $this->redStdDev = \sqrt(\array_sum($devsRed) / $totalPixels);
        $this->greenStdDev = \sqrt(\array_sum($devsGreen) / $totalPixels);
        $this->blueStdDev = \sqrt(\array_sum($devsBlue) / $totalPixels);
    }

    /**
     * @param resource $sourceImage
     * @param Side $side
     *
     * @return resource
     *@throws \Minepic\Image\Exceptions\ImageTrueColorCreationFailedException
     *
     */
    private function createCheckImage($sourceImage, Side $side)
    {
        $width = $side->getWidth();
        $height = $side->getHeight();
        $checkImage = \imagecreatetruecolor($side->getWidth(), $side->getHeight());
        if ($checkImage === false) {
            throw new ImageTrueColorCreationFailedException('imagecreatetruecolor failed');
        }
        \imagealphablending($checkImage, false);
        \imagesavealpha($checkImage, true);
        $colorIdentifier = \imagecolorallocatealpha($checkImage, 255, 255, 255, 127);
        \imagefilledrectangle($checkImage, 0, 0, $width, $height, $colorIdentifier);
        \imagecopy($checkImage, $sourceImage, 0, 0, $side->getTopLeft()->getX(), $side->getTopLeft()->getY(), $width, $height);

        return $checkImage;
    }

    /**
     * @return bool
     */
    private function validStdDev(): bool
    {
        return ($this->redStdDev > self::DEFAULT_STANDARD_DEVIATION && $this->greenStdDev > self::DEFAULT_STANDARD_DEVIATION) ||
            ($this->redStdDev > self::DEFAULT_STANDARD_DEVIATION && $this->blueStdDev > self::DEFAULT_STANDARD_DEVIATION) ||
            ($this->greenStdDev > self::DEFAULT_STANDARD_DEVIATION && $this->blueStdDev > self::DEFAULT_STANDARD_DEVIATION);
    }
}
