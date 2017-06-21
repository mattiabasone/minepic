<?php
namespace App\Image;

/**
 * Class ImageSection
 *
 * @package App\Image
 */
class ImageSection {

    /**
     * Skin Path
     *
     * @var string
     */
    protected $skinPath = '';

    /**
     * Resource with the image
     *
     * @var  resource
     */
    protected $imgResource;

    /**
     * Avatar constructor.
     * @param string $skinPath
     */
    public function __construct(string $skinPath) {
        $this->skinPath = $skinPath;
    }

    /**
     * From resource to string
     *
     * @return string
     */
    public function __toString(): string {
        ob_start();
        imagepng($this->imgResource);
        $imgToString = ob_get_contents();
        ob_end_clean();
        return $imgToString;
    }

    /**
     * Destructor
     */
    public function __destruct() {
        if ($this->imgResource) {
            imagedestroy($this->imgResource);
        }
    }

    /**
     * Get generated resource image
     *
     * @return resource
     */
    public function getResource() {
        return $this->imgResource;
    }

}