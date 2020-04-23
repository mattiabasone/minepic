<?php

declare(strict_types=1);

namespace App\Image\Sections\Avatar;

use App\Image\ImageSection;
use App\Image\Point;

/**
 * Class Coordinates.
 *
 * Stores Avatar coordinates in array, first X second Y
 */
final class Coordinates
{
    public const FACE = [
        ImageSection::TOP => [8, 0],
        ImageSection::BOTTOM => [16, 0],
        ImageSection::FRONT => [8, 8],
        ImageSection::BACK => [24, 8],
        ImageSection::RIGHT => [0, 8],
        ImageSection::LEFT => [16, 8],
    ];

    public const HELM = [
        ImageSection::TOP => [40, 0],
        ImageSection::BOTTOM => [48, 8],
        ImageSection::FRONT => [40, 8],
        ImageSection::BACK => [56, 8],
        ImageSection::RIGHT => [32, 8],
        ImageSection::LEFT => [48, 8],
    ];

    /**
     * @param string $section
     *
     * @return Point
     */
    public static function getAvatarSection(string $section): Point
    {
        [$x, $y] = self::FACE[$section];

        return new Point($x, $y);
    }

    /**
     * @param string $section
     *
     * @return Point
     */
    public static function getHelmSection(string $section): Point
    {
        [$x, $y] = self::HELM[$section];

        return new Point($x, $y);
    }
}
