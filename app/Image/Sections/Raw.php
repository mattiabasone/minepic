<?php

declare(strict_types=1);

namespace Minepic\Image\Sections;

use Minepic\Image\Exceptions\ImageCreateFromPngFailedException;
use Minepic\Image\ImageSection;

class Raw extends ImageSection
{
    /**
     * @throws ImageCreateFromPngFailedException
     */
    public function render(): void
    {
        $this->imgResource = $this->createImageFromPng($this->skinPath);
        imagealphablending($this->imgResource, true);
        imagesavealpha($this->imgResource, true);
    }
}
