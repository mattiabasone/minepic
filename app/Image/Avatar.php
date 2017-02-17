<?php

namespace App\Image;

class Avatar {

    /**
     * Max Standard Deviation value for helm check
     */
    const DEFAULT_STDDEV = 0.2;

    /**
     * Skin Path
     *
     * @var string
     */
    private $skinPath = '';

    /**
     * Skin width
     *
     * @var
     */
    private $skinWidth;

    /**
     * @var
     */
    private $skinHeight;

    /**
     * @var
     */
    private $skinType;

    /**
     * @var
     */
    private $skinAttr;

    /**
     * Resource with the image
     *
     * @var  resource
     */
    private $imgResource;

    /**
     * Mean Alpha value (Helm)
     *
     * @var int
     */
    private $meanAlpha = 0;

    /**
     * Red Standard Deviation value (Helm)
     *
     * @var int
     */
    private $redStdDev = 0;

    /**
     * Green Standard Deviation value (Helm)
     *
     * @var int
     */
    private $greenStdDev = 0;

    /**
     * Blue Standard Deviation value (Helm)
     *
     * @var int
     */
    private $blueStdDev = 0;

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
    public function __toString() : string {
        ob_start();
        imagepng($this->imgResource);
        $imgToString = ob_get_contents();
        ob_end_clean();
        return $imgToString;
    }

    /**
     * Get generated resource image
     *
     * @return resource
     */
    public function getResource() {
        return $this->imgResource;
    }

    /**
     * Load image params
     * @param void
     */
    private function getCurrentSkinParams() {
        list($this->skinWidth, $this->skinHeight, $this->skinYype, $this->skinAttr) = getimagesize($this->skinPath);
    }

    /**
     * Calculate sttdev for merging helm
     *
     * @param $head_part
     */
    private function calcSttDevHelm($head_part) {
        // Check for helm image
        $all_red = array();
        $all_green = array();
        $all_blue = array();
        $all_alpha = array();
        $x = 0;
        while ($x<8) {
            $y = 0;
            while ($y<8) {
                $color=imagecolorat($head_part, $x, $y);
                $colors = imagecolorsforindex($head_part, $color);
                $all_red[] = $colors['red'];
                $all_green[] = $colors['green'];
                $all_blue[] = $colors['blue'];
                $all_alpha[] = $colors['alpha'];
                $y++;
            }
            $x++;
        }
        // mean value for each color
        $mean_red = array_sum($all_red) / 64;
        $mean_green = array_sum($all_green) / 64;
        $mean_blue = array_sum($all_blue) / 64;
        $this->meanAlpha = array_sum($all_alpha) / 64;
        // Arrays deviation
        $devs_red = array();
        $devs_green = array();
        $devs_blue = array();
        $i = 0;
        while ($i<64) {
            $devs_red[] = pow($all_red[$i] - $mean_red, 2);
            $devs_green[] = pow($all_green[$i] - $mean_green, 2);
            $devs_blue[] = pow($all_blue[$i] - $mean_blue, 2);
            $i++;
        }
        // stddev for each color
        $this->redStdDev = sqrt(array_sum($devs_red) / 64);
        $this->greenStdDev = sqrt(array_sum($devs_green) / 64);
        $this->blueStdDev = sqrt(array_sum($devs_blue) / 64);
    }

    /**
     * Render avatar image
     *
     * @param int $size
     * @param string $type
     */
    public function renderAvatar(int $size = 0, string $type = 'F') {

        if ($size <= 0 OR $size > env('MAX_AVATAR_SIZE')) {
            $size = env('DEFAULT_AVATAR_SIZE');
        }
        // generate png from url/path
        $image = imagecreatefrompng($this->skinPath);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        // Head
        $this->imgResource = imagecreatetruecolor($size, $size);
        // Helm
        $helm_check = imagecreatetruecolor($size, $size);
        imagealphablending($helm_check, false);
        imagesavealpha($helm_check, true);
        $transparent = imagecolorallocatealpha($helm_check, 255, 255, 255, 127);
        imagefilledrectangle($helm_check, 0, 0, 8, 8, $transparent);
        switch ($type) {
            case 'F':
                // Avatar front
                @imagecopyresampled($this->imgResource, $image, 0, 0, 8, 8, $size, $size, 8, 8);
                @imagecopyresampled($helm_check, $image, 0, 0, 40, 8, 8, 8, 8, 8);
                break;
            case 'B':
                // Avatar back
                @imagecopyresampled($this->imgResource, $image, 0, 0, 24, 8, $size, $size, 8, 8);
                @imagecopyresampled($helm_check, $image, 0, 0, 56, 8, 8, 8, 8, 8);
                break;
            case 'R':
                @imagecopyresampled($this->imgResource, $image, 0, 0, 0, 8, $size, $size, 8, 8);
                @imagecopyresampled($helm_check, $image, 0, 0, 32, 8, 8, 8, 8, 8);
                break;
            case 'L':
                @imagecopyresampled($this->imgResource, $image, 0, 0, 16, 8, $size, $size, 8, 8);
                @imagecopyresampled($helm_check, $image, 0, 0, 56, 8, 8, 8, 8, 8);
                break;
            case 'T':
                // Head top
                @imagecopyresampled($this->imgResource, $image, 0, 0, 8, 0, $size, $size, 8, 8);
                @imagecopyresampled($helm_check, $image, 0, 0, 40, 0, 8, 8, 8, 8);
                break;
        }
        $this->calcSttDevHelm($helm_check);
        // if all pixel have transparency or the colors aren't the same
        if ( ( ($this->redStdDev > self::DEFAULT_STDDEV AND $this->greenStdDev > self::DEFAULT_STDDEV) OR
                ($this->redStdDev > self::DEFAULT_STDDEV AND $this->blueStdDev > self::DEFAULT_STDDEV) OR
                ($this->greenStdDev > self::DEFAULT_STDDEV AND $this->blueStdDev > self::DEFAULT_STDDEV) ) OR
            ($this->meanAlpha == 127) ) {
            $helm = imagecreatetruecolor($size, $size);
            imagealphablending($helm, false);
            imagesavealpha($helm, true);
            $transparent = imagecolorallocatealpha($helm, 255, 255, 255, 127);
            imagefilledrectangle($helm, 0, 0, $size, $size, $transparent);
            imagecopyresampled($helm, $image, 0, 0, 40, 8, $size, $size, 8, 8);
            $merge = imagecreatetruecolor($size, $size);
            imagecopy($merge, $this->imgResource, 0, 0, 0, 0, $size, $size);
            imagecopy($merge, $helm, 0, 0, 0, 0, $size, $size);
            imagecopymerge($this->imgResource, $merge, 0, 0, 0, 0, $size, $size, 0);
            $this->imgResource = $merge;
            unset($merge);
        }
    }

    /**
     * @TODO
     * Finish this thing
     *
     * @param int $size
     */
    public function renderIsometricAvatar($size = 64) {
        $left_poligon = array(
            0,  $size/4,  // Point 1 (x, y) ---->  0,20
            $size/2,  $size/2, // Point 2 (x, y) ---->  50,50
            $size/2,  $size,    // Point 3 (x, y) ---->  50,100
            0, ($size/4)*3,  // Point 4 (x, y) ---->  0,60
        );
        $top_poligon = array(
            0,  $size/4,  // Point 1 (x, y) ---->  0,33
            $size/2,  0, // Point 2 (x, y) ---->  50,0
            $size,  $size/4,    // Point 3 (x, y) ---->  100,20
            $size/2,  $size/2,  // Point 4 (x, y) ---->  50,50
        );
        $right_poligon = array(
            $size,  $size/4,  // Point 1 (x, y) ---->  100,20
            $size/2,  $size/2, // Point 2 (x, y) ---->  50,50
            $size/2,  $size,    // Point 3 (x, y) ---->  50,100
            $size, ($size/4)*3,  // Point 4 (x, y) ---->  100,60
        );

        $isometric = imagecreatetruecolor($size, $size);
        imagealphablending($isometric, false);
        imagesavealpha($isometric, true);

        // Alpha
        $transparent = imagecolorallocatealpha($isometric, 255, 255, 255, 127);
        imagefilledrectangle($isometric, 0, 0, 8, 8, $transparent);

        $left_part = $this->renderAvatar($size, $type = 'L');
        $top_part = $this->renderAvatar($size, $type = 'T');
        $right_part = $this->renderAvatar($size, $type = 'R');

        imagefilledpolygon($isometric, $left_poligon, 4, $left_part);
        imagefilledpolygon($isometric, $top_poligon, 4, $top_part);
        imagefilledpolygon($isometric, $right_poligon, 4, $right_part);

        $this->imgResource = $isometric;
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