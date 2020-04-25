<?php

declare(strict_types=1);

namespace App\Image;

use App\Image\Exceptions\ImageCreateFromPngFailedException;
use App\Image\Exceptions\ImageTrueColorCreationFailedException;

/**
 * Class ImageSection.
 */
abstract class ImageSection
{
    public const TOP = 'TOP';
    public const BOTTOM = 'BOTTOM';
    public const FRONT = 'FRONT';
    public const BACK = 'BACK';
    public const RIGHT = 'RIGHT';
    public const LEFT = 'LEFT';

    /**
     * Skin Path.
     *
     * @var string
     */
    protected string $skinPath = '';

    /**
     * Resource with the image.
     *
     * @var resource
     */
    protected $imgResource;

    /**
     * Avatar constructor.
     *
     * @param string $skinPath
     */
    public function __construct(string $skinPath)
    {
        $this->skinPath = $skinPath;
    }

    /**
     * From resource to string.
     */
    public function __toString(): string
    {
        \ob_start();
        \imagepng($this->imgResource);
        $imgToString = (string) \ob_get_clean();

        return $imgToString;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        if ($this->imgResource) {
            \imagedestroy($this->imgResource);
        }
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
     * @return resource
     */
    protected function createImageFromPng(string $path)
    {
        $resource = \imagecreatefrompng($path);
        if ($resource === false) {
            throw new ImageCreateFromPngFailedException("Cannot create png image from file {$path}");
        }

        return $resource;
    }

    /**
     * Create imagecreatetruecolor square empty image.
     *
     * @param $size
     *
     * @throws ImageTrueColorCreationFailedException
     *
     * @return resource
     */
    protected function createTrueColorSquare($size)
    {
        $square = \imagecreatetruecolor($size, $size);
        if ($square === false) {
            throw new ImageTrueColorCreationFailedException('imagecreatetruecolor failed');
        }

        return $square;
    }

    /**
     * @param $image
     *
     * @throws \Exception
     *
     * @return int
     */
    protected function colorAllocateAlpha($image): int
    {
        $colorIdentifier = \imagecolorallocatealpha($image, 255, 255, 255, 127);
        if (!$colorIdentifier) {
            throw new \Exception();
        }

        return $colorIdentifier;
    }
}
