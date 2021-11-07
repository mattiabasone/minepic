<?php

declare(strict_types=1);

namespace Minepic\Image\Components;

use Minepic\Image\Point;

class Side
{
    public const TOP = 'TOP';
    public const BOTTOM = 'BOTTOM';
    public const FRONT = 'FRONT';
    public const BACK = 'BACK';
    public const RIGHT = 'RIGHT';
    public const LEFT = 'LEFT';

    /**
     * @param Point $topLeft
     * @param Point $bottomRight
     */
    public function __construct(protected Point $topLeft, protected Point $bottomRight)
    {
    }

    /**
     * @return Point
     */
    public function getTopLeft(): Point
    {
        return $this->topLeft;
    }

    /**
     * @return Point
     */
    public function getBottomRight(): Point
    {
        return $this->bottomRight;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->bottomRight->getX() - $this->topLeft->getX();
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->bottomRight->getY() - $this->topLeft->getY();
    }

    /**
     * @param array $rawPoints
     *
     * @return Side
     */
    public static function fromRawPoints(array $rawPoints): self
    {
        return new self(
            new Point($rawPoints[0][0], $rawPoints[0][1]),
            new Point($rawPoints[1][0], $rawPoints[1][1])
        );
    }
}
