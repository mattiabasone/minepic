<?php

declare(strict_types=1);

namespace Minepic\Image\Components;

/**
 * Class Coordinates.
 *
 * Stores Avatar coordinates in array, first X second Y
 */
final class Coordinates
{
    public const HEAD = [
        Side::TOP => [[8, 0], [16, 8]],
        Side::BOTTOM => [[16, 0], [24, 8]],
        Side::FRONT => [[8, 8],  [16, 16]],
        Side::BACK => [[24, 8], [32, 16]],
        Side::RIGHT => [[0, 8], [8, 16]],
        Side::LEFT => [[16, 8], [24, 16]],
    ];

    public const HELM = [
        Side::TOP => [[40, 0], [48, 8]],
        Side::BOTTOM => [[48, 0], [56, 8]],
        Side::FRONT => [[40, 8], [48, 16]],
        Side::BACK => [[56, 8], [64, 16]],
        Side::RIGHT => [[32, 8], [40, 16]],
        Side::LEFT => [[48, 8], [56, 16]],
    ];

    public const TORSO = [
        Side::TOP => [[20, 16], [28, 20]],
        Side::BOTTOM => [[28, 16], [36, 10]],
        Side::FRONT => [[20, 20], [28, 32]],
        Side::BACK => [[32, 20], [40, 32]],
        Side::RIGHT => [[16, 20], [20, 32]],
        Side::LEFT => [[28, 20], [32, 32]],
    ];

    public const RIGHT_ARM = [
        Side::TOP => [[44, 16], [48, 20]],
        Side::BOTTOM => [[48, 16], [52, 20]],
        Side::FRONT => [[44, 20], [48, 32]],
        Side::BACK => [[52, 20], [56, 32]],
        Side::RIGHT => [[40, 20], [44, 32]],
        Side::LEFT => [[48, 20], [52, 32]],
    ];

    public const RIGHT_LEG = [
        Side::TOP => [[4, 16], [8, 20]],
        Side::BOTTOM => [[8, 16], [12, 20]],
        Side::FRONT => [[4, 20], [8, 32]],
        Side::BACK => [[12, 20], [16, 32]],
        Side::RIGHT => [[0, 20], [4, 32]],
        Side::LEFT => [[8, 20], [12, 32]],
    ];

    public const LEFT_ARM = [
        Side::TOP => [[36, 48], [40, 52]],
        Side::BOTTOM => [[40, 48], [44, 52]],
        Side::FRONT => [[36, 52], [40, 64]],
        Side::BACK => [[44, 52], [48, 64]],
        Side::RIGHT => [[32, 52], [36, 64]],
        Side::LEFT => [[40, 52], [44, 64]],
    ];

    public const LEFT_LEG = [
        Side::TOP => [[20, 48], [24, 52]],
        Side::BOTTOM => [[24, 48], [28, 52]],
        Side::FRONT => [[20, 52], [24, 64]],
        Side::BACK => [[28, 52], [32, 64]],
        Side::RIGHT => [[16, 52], [20, 64]],
        Side::LEFT => [[24, 52], [28, 64]],
    ];

    public const TORSO_LAYER = [
        Side::TOP => [[20, 48], [28, 36]],
        Side::BOTTOM => [[28, 48], [36, 36]],
        Side::FRONT => [[20, 36], [28, 48]],
        Side::BACK => [[32, 36], [40, 48]],
        Side::RIGHT => [[16, 36], [20, 48]],
        Side::LEFT => [[28, 36], [32, 48]],
    ];

    public const RIGHT_ARM_LAYER = [
        Side::TOP => [[44, 48], [48, 36]],
        Side::BOTTOM => [[48, 48], [52, 36]],
        Side::FRONT => [[44, 36], [48, 48]],
        Side::BACK => [[52, 36], [64, 48]],
        Side::RIGHT => [[40, 36], [44, 48]],
        Side::LEFT => [[48, 36], [52, 48]],
    ];

    public const RIGHT_LEG_LAYER = [
        Side::TOP => [[4, 48], [8, 36]],
        Side::BOTTOM => [[8, 48], [12, 36]],
        Side::FRONT => [[4, 36], [8, 48]],
        Side::BACK => [[12, 36], [16, 48]],
        Side::RIGHT => [[0, 36], [4, 48]],
        Side::LEFT => [[8, 36], [12, 48]],
    ];

    public const LEFT_ARM_LAYER = [
        Side::TOP => [[52, 48], [56, 52]],
        Side::BOTTOM => [[56, 48], [60, 52]],
        Side::FRONT => [[52, 52], [56, 64]],
        Side::BACK => [[60, 52], [64, 64]],
        Side::RIGHT => [[48, 52], [52, 64]],
        Side::LEFT => [[56, 52], [60, 64]],
    ];

    public const LEFT_LEG_LAYER = [
        Side::TOP => [[4, 48], [8, 52]],
        Side::BOTTOM => [[8, 48], [12, 52]],
        Side::FRONT => [[4, 52], [8, 64]],
        Side::BACK => [[12, 52], [16, 64]],
        Side::RIGHT => [[0, 52], [4, 64]],
        Side::LEFT => [[8, 52], [12, 64]],
    ];
}
