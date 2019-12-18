<?php

declare(strict_types=1);

namespace App\Image\Sections;

use App\Image\ImageSection;
use App\Image\Exceptions\InvalidSectionSpecifiedException;

/**
 * Class Avatar.
 */
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
    protected $meanAlpha = 0;

    /**
     * Red Standard Deviation value (Helm).
     *
     * @var int
     */
    protected $redStdDev = 0;

    /**
     * Green Standard Deviation value (Helm).
     *
     * @var int
     */
    protected $greenStdDev = 0;

    /**
     * Blue Standard Deviation value (Helm).
     *
     * @var int
     */
    protected $blueStdDev = 0;

    /**
     * Calculate sttdev for merging helm.
     *
     * @param $head_part
     */
    protected function calculateHelmStandardDeviation($head_part): void
    {
        // Check for helm image
        $all_red = [];
        $all_green = [];
        $all_blue = [];
        $all_alpha = [];
        $x = 0;
        while ($x < 8) {
            $y = 0;
            while ($y < 8) {
                $color = \imagecolorat($head_part, $x, $y);
                $colors = \imagecolorsforindex($head_part, $color);
                $all_red[] = $colors['red'];
                $all_green[] = $colors['green'];
                $all_blue[] = $colors['blue'];
                $all_alpha[] = $colors['alpha'];
                ++$y;
            }
            ++$x;
        }
        // mean value for each color
        $mean_red = \array_sum($all_red) / 64;
        $mean_green = \array_sum($all_green) / 64;
        $mean_blue = \array_sum($all_blue) / 64;
        $this->meanAlpha = \array_sum($all_alpha) / 64;
        // Arrays deviation
        $devs_red = [];
        $devs_green = [];
        $devs_blue = [];
        $i = 0;
        while ($i < 64) {
            $devs_red[] = ($all_red[$i] - $mean_red) ** 2;
            $devs_green[] = ($all_green[$i] - $mean_green) ** 2;
            $devs_blue[] = ($all_blue[$i] - $mean_blue) ** 2;
            ++$i;
        }
        // stddev for each color
        $this->redStdDev = \sqrt(\array_sum($devs_red) / 64);
        $this->greenStdDev = \sqrt(\array_sum($devs_green) / 64);
        $this->blueStdDev = \sqrt(\array_sum($devs_blue) / 64);
    }

    /**
     * Render avatar image.
     *
     * @param int $size Avatar size
     * @param string $type Section rendered
     * @throws InvalidSectionSpecifiedException
     */
    public function renderAvatar(int $size = 0, string $type = self::FRONT): void
    {
        if ($size <= 0 || $size > env('MAX_AVATAR_SIZE')) {
            $size = (int) env('DEFAULT_AVATAR_SIZE');
        }
        // generate png from url/path
        $image = \imagecreatefrompng($this->skinPath);
        \imagealphablending($image, false);
        \imagesavealpha($image, true);

        // Head
        $this->imgResource = \imagecreatetruecolor($size, $size);

        // Helm
        $helm_check = \imagecreatetruecolor($size, $size);
        \imagealphablending($helm_check, false);
        \imagesavealpha($helm_check, true);
        $transparent = \imagecolorallocatealpha($helm_check, 255, 255, 255, 127);
        \imagefilledrectangle($helm_check, 0, 0, 8, 8, $transparent);

        switch ($type) {
            case self::FRONT:
                // Avatar front
                $sectionSrcX = 8;
                $sectionSrcY = 8;

                // Avatar Helm front
                $sectionHelmSrcX = 40;
                $sectionHelmSrcY = 8;

                break;
            case self::BACK:
                // Avatar back
                $sectionSrcX = 24;
                $sectionSrcY = 8;

                // Avatar Helm back
                $sectionHelmSrcX = 56;
                $sectionHelmSrcY = 8;

                break;
            case self::RIGHT:
                // Avatar right
                $sectionSrcX = 0;
                $sectionSrcY = 8;

                // Avatar Helm right
                $sectionHelmSrcX = 32;
                $sectionHelmSrcY = 8;

                break;
            case self::LEFT:
                // Avatar left
                $sectionSrcX = 16;
                $sectionSrcY = 8;

                // Avatar Helm left
                $sectionHelmSrcX = 56;
                $sectionHelmSrcY = 8;

                break;
            case self::TOP:
                // Avatar right
                $sectionSrcX = 8;
                $sectionSrcY = 0;

                // Avatar Helm right
                $sectionHelmSrcX = 40;
                $sectionHelmSrcY = 0;
                break;
            default:
                throw new InvalidSectionSpecifiedException();
                break;
        }

        @\imagecopyresampled($this->imgResource, $image, 0, 0, $sectionSrcX, $sectionSrcY, $size, $size, 8, 8);
        @\imagecopyresampled($helm_check, $image, 0, 0, $sectionHelmSrcX, $sectionHelmSrcY, 8, 8, 8, 8);

        $this->calculateHelmStandardDeviation($helm_check);

        // if all pixel have transparency or the colors aren't the same
        if ((
            ($this->redStdDev > self::DEFAULT_STANDARD_DEVIATION && $this->greenStdDev > self::DEFAULT_STANDARD_DEVIATION) ||
            ($this->redStdDev > self::DEFAULT_STANDARD_DEVIATION && $this->blueStdDev > self::DEFAULT_STANDARD_DEVIATION) ||
            ($this->greenStdDev > self::DEFAULT_STANDARD_DEVIATION && $this->blueStdDev > self::DEFAULT_STANDARD_DEVIATION)
            ) ||
            ($this->meanAlpha === 127)) {
            $helm = \imagecreatetruecolor($size, $size);
            \imagealphablending($helm, false);
            \imagesavealpha($helm, true);
            $transparent = \imagecolorallocatealpha($helm, 255, 255, 255, 127);
            \imagefilledrectangle($helm, 0, 0, $size, $size, $transparent);
            \imagecopyresampled($helm, $image, 0, 0, $sectionHelmSrcX, $sectionHelmSrcY, $size, $size, 8, 8);
            $merge = \imagecreatetruecolor($size, $size);
            \imagecopy($merge, $this->imgResource, 0, 0, 0, 0, $size, $size);
            \imagecopy($merge, $helm, 0, 0, 0, 0, $size, $size);
            \imagecopymerge($this->imgResource, $merge, 0, 0, 0, 0, $size, $size, 0);
            $this->imgResource = $merge;
            unset($merge);
        }
    }
}
