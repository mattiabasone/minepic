<?php

declare(strict_types=1);

namespace Minepic\Image;

use Minepic\Image\Exceptions\ImageCreateFromPngFailedException;
use Minepic\Image\Exceptions\ImageResourceCreationFailedException;

abstract class ImageSection
{
    /**
     * Skin Path.
     *
     * @var string
     */
    protected string $skinPath = '';
    /**
     * @var \GdImage
     */
    protected $skinResource;
    /**
     * @var int
     */
    protected int $skinWidth;
    /**
     * @var int
     */
    protected int $skinHeight;
    /**
     * Resource with the image.
     *
     * @var \GdImage
     */
    protected $imgResource;

    /**
     * @param string $skinPath
     * @throws ImageCreateFromPngFailedException
     */
    public function __construct(string $skinPath)
    {
        $this->skinPath = $skinPath;
        $this->skinResource = $this->createImageFromPng($this->skinPath);
        $this->skinWidth = (int) imagesx($this->skinResource);
        $this->skinHeight = (int) imagesy($this->skinResource);
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        if ($this->imgResource) {
            imagedestroy($this->imgResource);
        }
    }

    /**
     * From resource to string.
     */
    public function __toString(): string
    {
        ob_start();
        imagepng($this->imgResource);
        $imgToString = (string) ob_get_clean();

        return $imgToString;
    }

    /**
     * @return bool
     */
    public function is64x64(): bool
    {
        return $this->skinWidth === 64 && $this->skinHeight === 64;
    }

    /**
     * Get generated resource image.
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->imgResource;
    }

    /**
     * Creates imagecreatefrompng resource, it fails throws an Exception.
     *
     * @param string $path
     *
     * @throws ImageCreateFromPngFailedException
     *
     * @return \GdImage
     */
    protected function createImageFromPng(string $path)
    {
        $resource = imagecreatefrompng($path);
        if ($resource === false) {
            throw new ImageCreateFromPngFailedException("Cannot create png image from file {$path}");
        }

        return $resource;
    }

    /**
     * @param int $width
     * @param int $height
     *
     * @throws ImageResourceCreationFailedException
     *
     * @return \GdImage
     */
    protected function emptyBaseImage(int $width, int $height)
    {
        $tmpImageResource = imagecreatetruecolor($width, $height);
        if ($tmpImageResource === false) {
            throw new ImageResourceCreationFailedException('imagecreatetruecolor() failed');
        }
        imagealphablending($tmpImageResource, false);
        imagesavealpha($tmpImageResource, true);
        $transparent = imagecolorallocatealpha($tmpImageResource, 255, 255, 255, 127);
        imagefilledrectangle($tmpImageResource, 0, 0, $width, $height, $transparent);

        return $tmpImageResource;
    }
}
