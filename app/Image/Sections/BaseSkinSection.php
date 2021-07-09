<?php

declare(strict_types=1);

namespace Minepic\Image\Sections;

use Minepic\Image\Components\Component;
use Minepic\Image\Exceptions\ImageResourceCreationFailedException;
use Minepic\Image\ImageSection;
use Minepic\Image\LayerValidator;
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
            $this->copyComponent($tmpImageResource, $componentName, $componentsData[0], $componentsData[1] ?? null);
        }

        $this->patchOldSkin($tmpImageResource);

        $scale = $skinHeight / $this->baseImageHeight;
        if ($scale === 0) {
            $scale = 1;
        }
        $skinWidth = (int) round($scale * ($this->baseImageWidth));

        $this->imgResource = $this->emptyBaseImage($skinWidth, $skinHeight);
        imagecopyresized($this->imgResource, $tmpImageResource, 0, 0, 0, 0, $skinWidth, $skinHeight, $this->baseImageWidth, $this->baseImageHeight);
    }

    /**
     * @return Point[]
     */
    abstract protected function startingPoints(): array;

    /**
     * In old skins (pre 1.8) left arm/leg are right arm/leg flipped
     * @param $tmpImageResource
     * @throws \Exception
     */
    protected function patchOldSkin($tmpImageResource): void
    {
        if ($this->is64x64()) {
            return;
        }

        if (\array_key_exists(Component::LEFT_ARM, $this->startingPoints())) {
            $this->flipComponent(
                $tmpImageResource,
                Component::getLeftArm(),
                Component::getRightArm(),
                $this->startingPoints()[Component::LEFT_ARM]
            );
        }

        if (\array_key_exists(Component::LEFT_LEG, $this->startingPoints())) {
            $this->flipComponent(
                $tmpImageResource,
                Component::getLeftLeg(),
                Component::getRightLeg(),
                $this->startingPoints()[Component::LEFT_LEG]
            );
        }
    }

    /**
     * @param $tmpImageResource
     * @param Component $dstComponent
     * @param Component $srcComponent
     * @param Point $startingPoint
     * @throws \Exception
     */
    protected function flipComponent($tmpImageResource, Component $dstComponent, Component $srcComponent, Point $startingPoint): void
    {
        $leftArmData = $dstComponent->getSideByIdentifier($this->side);
        $rightArmData = $srcComponent->getSideByIdentifier($this->side);
        $width = $leftArmData->getWidth();
        $height = $leftArmData->getHeight();
        $leftArm = imagecreatetruecolor($width, $height);
        for ($x = 0; $x < 4; ++$x) {
            imagecopy(
                $leftArm,
                $this->skinResource,
                $x,
                0,
                $rightArmData->getBottomRight()->getX() - $x - 1,
                $rightArmData->getTopLeft()->getY(),
                1,
                $height
            );
        }
        imagecopymerge(
            $tmpImageResource,
            $leftArm,
            $startingPoint->getX(),
            $startingPoint->getY(),
            0,
            0,
            $width,
            $height,
            100
        );
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
     * @return array[]
     */
    protected function getAllComponents(): array
    {
        if ($this->is64x64()) {
            return [
                Component::HEAD => [Component::getHead(), Component::getHeadLayer()],
                Component::TORSO => [Component::getTorso(), Component::getTorsoLayer()],
                Component::RIGHT_ARM => [Component::getRightArm(), Component::getRightArmLayer()],
                Component::LEFT_ARM => [Component::getLeftArm(), Component::getLeftArmLayer()],
                Component::RIGHT_LEG => [Component::getRightLeg(), Component::getRightLegLayer()],
                Component::LEFT_LEG => [Component::getLeftLeg(), Component::getLeftLegLayer()],
            ];
        }

        return [
            Component::HEAD => [Component::getHead(), Component::getHeadLayer()],
            Component::TORSO => [Component::getTorso()],
            Component::RIGHT_ARM => [Component::getRightArm()],
            Component::RIGHT_LEG => [Component::getRightLeg()],
        ];
    }

    /**
     * @param $tmpImageResource
     * @param string $componentName
     * @param Component $base
     * @param null|Component $layer
     * @throws \Exception
     */
    protected function copyComponent($tmpImageResource, string $componentName, Component $base, ?Component $layer): void
    {
        $sideBase = $base->getSideByIdentifier($this->side);
        $width = $sideBase->getWidth();
        $height = $sideBase->getHeight();

        $startingPoint = $this->startingPoints()[$componentName] ?? new Point(0, 0);
        imagecopy(
            $tmpImageResource,
            $this->skinResource,
            $startingPoint->getX(),
            $startingPoint->getY(),
            $sideBase->getTopLeft()->getX(),
            $sideBase->getTopLeft()->getY(),
            $width,
            $height
        );
        if ($layer !== null && (new LayerValidator())->check($this->skinResource, $layer->getSideByIdentifier($this->side))) {
            $sideLayer = $layer->getSideByIdentifier($this->side);
            imagecopymerge_alpha(
                $tmpImageResource,
                $this->skinResource,
                $startingPoint->getX(),
                $startingPoint->getY(),
                $sideLayer->getTopLeft()->getX(),
                $sideLayer->getTopLeft()->getY(),
                $width,
                $height,
                100
            );
        }
    }
}
