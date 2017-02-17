<?php

namespace App\Image;

/**
 * Class Skin
 * @package App\Image
 */
class Skin {

    /**
     * Skin Path
     *
     * @var string
     */
    private $skinPath = '';

    /**
     * Resource with the image
     *
     * @var resource
     */
    private $imgResource;

    /**
     * Skin constructor.
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
    public function __toString() : string {
        ob_start();
        imagepng($this->imgResource);
        $imgToString = ob_get_contents();
        ob_end_clean();
        return $imgToString;
    }

    /**
     * Create a PNG with raw texture
     */
    public function prepareTextureDownload() {
        $this->imgResource = imagecreatefrompng($this->skinPath);
    }

    /**
     * Render skin
     *
     * @access public
     * @param int
     * @param string
     */
    public function renderSkin($skin_height = 256, $type = 'F') {
        if ($type != 'B') {
            $type = 'F';
        }
        $skin_height = (int) $skin_height;
        if ($skin_height == 0 OR $skin_height < 0 OR $skin_height > env('MAX_SKINS_SIZE')) {
            $skin_height = env('DEFAULT_SKIN_SIZE');
        }

        $image = imagecreatefrompng($this->skinPath);
        $scale = $skin_height / 32;
        $this->imgResource = imagecreatetruecolor(16*$scale, 32*$scale);
        imagealphablending($this->imgResource, false);
        imagesavealpha($this->imgResource, true);
        $transparent = imagecolorallocatealpha($this->imgResource, 255, 255, 255, 127);
        imagefilledrectangle($this->imgResource, 0, 0, 16*$scale, 32*$scale, $transparent);

        $tmpAvatar = new Avatar($this->skinPath);
        $tmpAvatar->renderAvatar(8, $type);
        // Front
        if ($type == 'F') {
            // Head
            imagecopyresized($this->imgResource, $tmpAvatar->getResource(), 4*$scale, 0*$scale, 0, 0, 8*$scale, 8*$scale, 8, 8);
            // Body Front
            imagecopyresized($this->imgResource, $image, 4*$scale, 8*$scale, 20, 20, 8*$scale, 12*$scale, 8, 12);
            // Right Arm (left on img)
            $r_arm = imagecreatetruecolor(4, 12);
            imagecopy($r_arm, $image, 0, 0, 44, 20, 4, 12);
            imagecopyresized($this->imgResource, $r_arm, 0*$scale, 8*$scale, 0, 0, 4*$scale, 12*$scale, 4, 12);
            // Right leg (left on img)
            $r_leg = imagecreatetruecolor(4, 20);
            imagecopy($r_leg, $image, 0, 0, 4, 20, 4, 12);
            imagecopyresized($this->imgResource, $r_leg, 4*$scale, 20*$scale, 0, 0, 4*$scale, 12*$scale, 4, 12);
        } else {
            // Head
            imagecopyresized($this->imgResource, $tmpAvatar->getResource(), 4*$scale, 0*$scale, 0, 0, 8*$scale, 8*$scale, 8, 8);
            // Body Back
            imagecopyresized($this->imgResource, $image, 4*$scale, 8*$scale, 32, 20, 8*$scale, 12*$scale, 8, 12);
            // Right Arm Back (left on img)
            $r_arm = imagecreatetruecolor(4, 12);
            imagecopy($r_arm, $image, 0, 0, 52, 20, 4, 12);
            imagecopyresized($this->imgResource, $r_arm, 0*$scale, 8*$scale, 0, 0, 4*$scale, 12*$scale, 4, 12);
            // Right leg Back (left on img)
            $r_leg = imagecreatetruecolor(4, 20);
            imagecopy($r_leg, $image, 0, 0, 12, 20, 4, 12);
            imagecopyresized($this->imgResource, $r_leg, 4*$scale, 20*$scale, 0, 0, 4*$scale, 12*$scale, 4, 12);
        }
        $tmpAvatar = null;

        // Left Arm (right flipped)
        $l_arm = imagecreatetruecolor(4, 12);
        for ($x = 0; $x < 4; $x++) {
            imagecopy($l_arm, $r_arm, $x, 0, 4 - $x - 1, 0, 1, 12);
        }
        imagecopyresized($this->imgResource, $l_arm, 12*$scale,  8*$scale,  0,  0,  4*$scale,  12*$scale, 4,  12);
        // Left leg (right flipped)
        $l_leg = imagecreatetruecolor(4, 20);
        for ($x = 0; $x < 4; $x++) {
            imagecopy($l_leg, $r_leg, $x, 0, 4 - $x - 1, 0, 1, 20);
        }
        imagecopyresized($this->imgResource, $l_leg, 8*$scale, 20*$scale,  0,  0,  4*$scale,  12*$scale, 4,  12);
        return;
    }

    /**
     * Provide real skin
     *
     * @access public
     * @param string
     */
    public function rawTextureImage() {
        $this->imgResource = imagecreatefrompng($this->skinPath);
        imagealphablending($this->imgResource, true);
        imagesavealpha($this->imgResource, true);
        return;
    }

    /**
     * Destructor
     */
    public function __destruct() {
        if ($this->imgResource) {
            imagedestroy($this->imgResource);
        }
    }

}