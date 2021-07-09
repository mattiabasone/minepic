<?php

declare(strict_types=1);

namespace Minepic\Image\Sections;

use Minepic\Image\ImageSection;

class Raw extends ImageSection
{
    /**
     * @throws \Minepic\Image\Exceptions\ImageCreateFromPngFailedException
     */
    public function render()
    {
        $this->imgResource = $this->createImageFromPng($this->skinPath);
        imagealphablending($this->imgResource, true);
        imagesavealpha($this->imgResource, true);
    }
}
