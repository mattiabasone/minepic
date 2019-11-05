<?php

declare(strict_types=1);

namespace App\Image;

/**
 * Class ImageSection.
 */
abstract class ImageSection
{
    /**
     * Skin Path.
     *
     * @var string
     */
    protected $skinPath = '';

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
     *
     * @return string
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
}
