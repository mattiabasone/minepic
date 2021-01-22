<?php

declare(strict_types=1);

namespace Minepic\Image\Components;

class Component
{
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

    /**
     * @return Side
     */
    public function getTop(): Side
    {
        return $this->top;
    }

    /**
     * @return Side
     */
    public function getBottom(): Side
    {
        return $this->bottom;
    }

    /**
     * @return Side
     */
    public function getFront(): Side
    {
        return $this->front;
    }

    /**
     * @return Side
     */
    public function getBack(): Side
    {
        return $this->back;
    }

    /**
     * @return Side
     */
    public function getRight(): Side
    {
        return $this->right;
    }

    /**
     * @return Side
     */
    public function getLeft(): Side
    {
        return $this->left;
    }

    /**
     * @param string $side
     *
     * @throws \Exception
     *
     * @return Side
     */
    public function getSideByIdentifier(string $side): Side
    {
        switch ($side) {
            case Side::TOP:
                return $this->getTop();
            case Side::BOTTOM:
                return $this->getBottom();
            case Side::FRONT:
                return $this->getFront();
            case Side::BACK:
                return $this->getBack();
            case Side::RIGHT:
                return $this->getRight();
            case Side::LEFT:
                return $this->getLeft();
            default:
                throw new \Exception("Invalid Side {$side}");
        }
    }

    public static function getHead(): self
    {
        return new self(Coordinates::HEAD);
    }

    public static function getHelm(): self
    {
        return new self(Coordinates::HELM);
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
