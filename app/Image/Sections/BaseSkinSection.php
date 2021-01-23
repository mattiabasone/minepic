<?php

declare(strict_types=1);

namespace Minepic\Image\Sections;

use Minepic\Image\Components\Component;
use Minepic\Image\Exceptions\ImageResourceCreationFailedException;
use Minepic\Image\ImageSection;
use Minepic\Image\Point;

abstract class BaseSkinSection extends ImageSection
{
    protected string $side;
    protected int $baseImageWidth = 16;
    protected int $baseImageHeight = 32;

    /**
     * @param int $skinHeight
     * @throws ImageResourceCreationFailedException
     * @throws \Exception
     */
    public function render(int $skinHeight = 256): void
    {
        $skinHeight = $this->checkHeight($skinHeight);

        $tmpImageResource = $this->emptyBaseImage($this->baseImageWidth, $this->baseImageHeight);
        foreach ($this->getAllComponents() as $componentName => $componentsData) {
            $this->copyComponent($tmpImageResource, $componentName, $componentsData[0], $componentsData[1]);
        }

        $scale = $skinHeight / $this->baseImageHeight;
        if ($scale === 0) {
            $scale = 1;
        }
        $skinWidth = (int) \round($scale * ($this->baseImageWidth));

        $this->imgResource = $this->emptyBaseImage($skinWidth, $skinHeight);
        \imagecopyresized($this->imgResource, $tmpImageResource, 0, 0, 0, 0, $skinWidth, $skinHeight, $this->baseImageWidth, $this->baseImageHeight);
    }

    /**
     * @param $skinHeight
     *
     * @return int
     */
    protected function checkHeight($skinHeight): int
    {
        if ($skinHeight === 0 || $skinHeight < 0 || $skinHeight > (int) env('MAX_SKINS_SIZE')) {
            $skinHeight = (int) env('DEFAULT_SKIN_SIZE');
        }

        return $skinHeight;
    }

    /**
     * @return Point[]
     */
    protected function startingPoints(): array
    {
        return [
            Component::HEAD => new Point(0, 0),
            Component::TORSO => new Point(0, 0),
            Component::RIGHT_ARM => new Point(0, 0),
            Component::LEFT_ARM => new Point(0, 0),
            Component::RIGHT_LEG => new Point(0, 0),
            Component::LEFT_LEG => new Point(0, 0),
        ];
    }

    /**
     * @return array[]
     */
    protected function getAllComponents(): array
    {
        return [
            Component::HEAD => [Component::getHead(), Component::getHeadLayer()],
            Component::TORSO => [Component::getTorso(), Component::getTorsoLayer()],
            Component::RIGHT_ARM => [Component::getRightArm(), Component::getRightArmLayer()],
            Component::LEFT_ARM => [Component::getLeftArm(), Component::getLeftArmLayer()],
            Component::RIGHT_LEG => [Component::getRightLeg(), Component::getRightLegLayer()],
            Component::LEFT_LEG => [Component::getLeftLeg(), Component::getLeftLegLayer()],
        ];
    }

    /**
     * @param $tmpImageResource
     * @param string $componentName
     * @param Component $base
     * @param Component $layer
     * @throws \Exception
     */
    protected function copyComponent($tmpImageResource, string $componentName, Component $base, Component $layer): void
    {
        $sideBase = $base->getSideByIdentifier($this->side);
        $sideLayer = $layer->getSideByIdentifier($this->side);
        $width = $sideBase->getWidth();
        $height = $sideBase->getHeight();

        $startingPoint = $this->startingPoints()[$componentName] ?? new Point(0, 0);
        \imagecopyresized(
            $tmpImageResource,
            $this->skinResource,
            $startingPoint->getX(),
            $startingPoint->getY(),
            $sideBase->getTopLeft()->getX(),
            $sideBase->getTopLeft()->getY(),
            $width,
            $height,
            $width,
            $height
        );
        if ($this->is64x64()) {
            \imagecopyresized($tmpImageResource,
                $this->skinResource,
                $startingPoint->getX(),
                $startingPoint->getY(),
                $sideLayer->getTopLeft()->getX(),
                $sideLayer->getTopLeft()->getY(),
                $width,
                $height,
                $width,
                $height
            );
        }
    }

    /**
     * @param int $width
     * @param int $height
     *
     * @throws ImageResourceCreationFailedException
     *
     * @return resource
     */
    protected function emptyBaseImage(int $width, int $height)
    {
        $tmpImageResource = \imagecreatetruecolor($width, $height);
        if ($tmpImageResource === false) {
            throw new ImageResourceCreationFailedException('imagecreatetruecolor() failed');
        }
        \imagealphablending($tmpImageResource, false);
        \imagesavealpha($tmpImageResource, true);
        $transparent = \imagecolorallocatealpha($tmpImageResource, 255, 255, 255, 127);
        \imagefilledrectangle($tmpImageResource, 0, 0, $width, $height, $transparent);

        return $tmpImageResource;
    }
}