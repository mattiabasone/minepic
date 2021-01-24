<?php

declare(strict_types=1);

namespace Minepic\Image\Sections;

use Minepic\Image\Components\Component;
use Minepic\Image\Components\Side;
use Minepic\Image\Point;

class SkinBack extends BaseSkinSection
{
    /** @var string */
    protected string $side = Side::BACK;

    /**
     * @return Point[]
     */
    protected function startingPoints(): array
    {
        return [
            Component::HEAD => new Point(4, 0),
            Component::TORSO => new Point(4, 8),
            Component::RIGHT_ARM => new Point(0, 8),
            Component::LEFT_ARM => new Point(12, 8),
            Component::RIGHT_LEG => new Point(4, 20),
            Component::LEFT_LEG => new Point(8, 20),
        ];
    }
}
