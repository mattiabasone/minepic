<?php

declare(strict_types=1);

namespace Minepic\Image\Components;

class Component
{
    public const HEAD = 'HEAD';
    public const TORSO = 'TORSO';
    public const RIGHT_ARM = 'RIGHT_ARM';
    public const LEFT_ARM = 'LEFT_ARM';
    public const RIGHT_LEG = 'RIGHT_LEG';
    public const LEFT_LEG = 'LEFT_LEG';

    protected Side $top;
    protected Side $bottom;
    protected Side $front;
    protected Side $back;
    protected Side $right;
    protected Side $left;

    public function __construct(array $sectionsCoordinates)
    {
        $this->top = Side::fromRawPoints($sectionsCoordinates[Side::TOP]);
        $this->bottom = Side::fromRawPoints($sectionsCoordinates[Side::BOTTOM]);
        $this->front = Side::fromRawPoints($sectionsCoordinates[Side::FRONT]);
        $this->back = Side::fromRawPoints($sectionsCoordinates[Side::BACK]);
        $this->right = Side::fromRawPoints($sectionsCoordinates[Side::RIGHT]);
        $this->left = Side::fromRawPoints($sectionsCoordinates[Side::LEFT]);
    }

    public function getTop(): Side
    {
        return $this->top;
    }

    public function getBottom(): Side
    {
        return $this->bottom;
    }

    public function getFront(): Side
    {
        return $this->front;
    }

    public function getBack(): Side
    {
        return $this->back;
    }

    public function getRight(): Side
    {
        return $this->right;
    }

    public function getLeft(): Side
    {
        return $this->left;
    }

    /**
     * @throws \Exception
     */
    public function getSideByIdentifier(string $side): Side
    {
        return match ($side) {
            Side::TOP => $this->getTop(),
            Side::BOTTOM => $this->getBottom(),
            Side::FRONT => $this->getFront(),
            Side::BACK => $this->getBack(),
            Side::RIGHT => $this->getRight(),
            Side::LEFT => $this->getLeft(),
            default => throw new \Exception("Invalid Side {$side}")
        };
    }

    public static function getHead(): self
    {
        return new self(Coordinates::HEAD);
    }

    public static function getHeadLayer(): self
    {
        return new self(Coordinates::HEAD_LAYER);
    }

    public static function getTorso(): self
    {
        return new self(Coordinates::TORSO);
    }

    public static function getTorsoLayer(): self
    {
        return new self(Coordinates::TORSO_LAYER);
    }

    public static function getRightArm(): self
    {
        return new self(Coordinates::RIGHT_ARM);
    }

    public static function getRightArmLayer(): self
    {
        return new self(Coordinates::RIGHT_ARM_LAYER);
    }

    public static function getLeftArm(): self
    {
        return new self(Coordinates::LEFT_ARM);
    }

    public static function getLeftArmLayer(): self
    {
        return new self(Coordinates::LEFT_ARM_LAYER);
    }

    public static function getRightLeg(): self
    {
        return new self(Coordinates::RIGHT_LEG);
    }

    public static function getRightLegLayer(): self
    {
        return new self(Coordinates::RIGHT_LEG_LAYER);
    }

    public static function getLeftLeg(): self
    {
        return new self(Coordinates::LEFT_LEG);
    }

    public static function getLeftLegLayer(): self
    {
        return new self(Coordinates::LEFT_LEG_LAYER);
    }
}
