<?php

declare(strict_types=1);

namespace App\Image;

use App\Helpers\Storage\Files\IsometricsStorage;
use App\Helpers\Storage\Files\SkinsStorage;
use App\Image\Exceptions\SkinNotFountException;
use App\Image\Sections\Avatar;

/**
 * Class IsometricAvatar.
 */
class IsometricAvatar
{
    /**
     * Cosine PI/6.
     */
    private const COSINE_PI_6 = M_SQRT3 / 2;

    /**
     * Base size (all requests must be <=).
     */
    private const HEAD_BASE_SIZE = 252;

    /**
     * Margin (in pixels) in the final image.
     */
    private const HEAD_MARGIN = 4;

    /**
     * Maximum size for resize operation.
     */
    private const MAX_SIZE = 512;

    /**
     * Minimum size for resize operation.
     */
    private const MIN_SIZE = 16;

    /**
     * User UUID.
     *
     * @var string
     */
    protected string $uuid = '';

    /**
     * Last time user data has been updated.
     *
     * @var int
     */
    protected int $lastUpdate = 0;

    /**
     * Flag for checking cache.
     *
     * @var bool
     */
    protected bool $checkCacheStatusFlag = true;

    /**
     * Skin Path.
     *
     * @var string
     */
    protected string $skinPath = '';

    /**
     * Skin Path.
     *
     * @var string
     */
    protected string $isometricPath = '';

    /**
     * @var \Imagick
     */
    protected \Imagick $head;

    /**
     * IsometricAvatar constructor.
     *
     * @param string $uuid       User UUID
     * @param int    $lastUpdate
     *
     * @throws SkinNotFountException
     */
    public function __construct(string $uuid, int $lastUpdate)
    {
        $this->uuid = $uuid;
        $this->lastUpdate = $lastUpdate;

        if (SkinsStorage::exists($uuid)) {
            $this->skinPath = SkinsStorage::getPath($uuid);
        } else {
            throw new SkinNotFountException();
        }

        if (IsometricsStorage::exists($uuid)) {
            $this->isometricPath = IsometricsStorage::getPath($uuid);
        }
    }

    /**
     * __toString().
     */
    public function __toString(): string
    {
        return (string) $this->head;
    }

    /**
     * Get ImagickPixel transparent object.
     *
     * @return \ImagickPixel
     */
    final protected function getImagickPixelTransparent(): \ImagickPixel
    {
        return new \ImagickPixel('transparent');
    }

    /**
     * Point for face section.
     *
     * @param int $size
     *
     * @return array
     */
    private function getFrontPoints($size = self::HEAD_BASE_SIZE): array
    {
        $cosine_result = \round(self::COSINE_PI_6 * $size);
        $half_size = \round($size / 2);

        return [
            0, 0, 0, 0,
            0, $size, 0, $size,
            $size, 0, -$cosine_result, $half_size,
        ];
    }

    /**
     * Points for top section.
     *
     * @param int $size
     *
     * @return array
     */
    private function getTopPoints($size = self::HEAD_BASE_SIZE): array
    {
        $cosine_result = \round(self::COSINE_PI_6 * $size);
        $half_size = \round($size / 2);

        return [
            0, $size, 0, 0,
            0, 0, -$cosine_result, -($half_size),
            $size, $size, $cosine_result, -($half_size),
        ];
    }

    /**
     * Points for right section.
     *
     * @param int $size
     *
     * @return array
     */
    private function getRightPoints($size = self::HEAD_BASE_SIZE): array
    {
        $cosine_result = \round(self::COSINE_PI_6 * $size);
        $half_size = \round($size / 2);

        return [
            $size, 0, 0, 0,
            0, 0, -($cosine_result), -($half_size),
            $size, $size, 0, $size,
        ];
    }

    /**
     * Render Isometric from avatar sections.
     *
     * @throws \Throwable
     */
    protected function renderFullSize(): void
    {
        // Create Avatar Object
        $avatar = new Avatar($this->skinPath);

        // Face
        $avatar->render(self::HEAD_BASE_SIZE, ImageSection::FRONT);

        $face = new \Imagick();
        $face->readImageBlob((string) $avatar);
        $face->brightnessContrastImage(8, 8);
        $face->setImageVirtualPixelMethod(\Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
        $face->setBackgroundColor(
            $this->getImagickPixelTransparent()
        );

        $face->distortImage(
            \Imagick::DISTORTION_AFFINE,
            $this->getFrontPoints(),
            true
        );

        // Top
        $avatar->render(self::HEAD_BASE_SIZE, ImageSection::TOP);

        $top = new \Imagick();
        $top->readImageBlob((string) $avatar);
        $top->brightnessContrastImage(6, 6);
        $top->setImageVirtualPixelMethod(\Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
        $top->setBackgroundColor(
            $this->getImagickPixelTransparent()
        );

        $top->distortImage(
            \Imagick::DISTORTION_AFFINE,
            $this->getTopPoints(),
            true
        );

        // Right
        $avatar->render(self::HEAD_BASE_SIZE, ImageSection::RIGHT);

        $right = new \Imagick();
        $right->readImageBlob((string) $avatar);
        $right->brightnessContrastImage(4, 4);

        $right->setImageVirtualPixelMethod(\Imagick::VIRTUALPIXELMETHOD_TRANSPARENT);
        $right->setBackgroundColor(
            $this->getImagickPixelTransparent()
        );

        $right->distortImage(
            \Imagick::DISTORTION_AFFINE,
            $this->getRightPoints(),
            true
        );

        // Head image
        $doubleAvatarSize = self::HEAD_BASE_SIZE * 2;
        $finalImageSize = $doubleAvatarSize + (self::HEAD_MARGIN * 2);

        $this->head = new \Imagick();
        $this->head->newImage($finalImageSize, $finalImageSize, $this->getImagickPixelTransparent());

        // This is weird, but it works
        $faceX = ((int) \round(($doubleAvatarSize / 2))) - 2 + self::HEAD_MARGIN;
        $faceY = $rightY = ((int) \round($doubleAvatarSize / 4)) - 1 + self::HEAD_MARGIN;
        $topX = $rightX = ((int) \round($doubleAvatarSize / 16)) + self::HEAD_MARGIN;
        $topY = -1 + self::HEAD_MARGIN;

        // Add Face Section
        $this->head->compositeimage($face->getimage(), \Imagick::COMPOSITE_PLUS, $faceX, $faceY);

        // Add Top Section
        $this->head->compositeimage($top->getimage(), \Imagick::COMPOSITE_PLUS, $topX, $topY);

        // Add Right Section
        $this->head->compositeimage($right->getimage(), \Imagick::COMPOSITE_PLUS, $rightX, $rightY);

        // Set format to PNG
        $this->head->setImageFormat('png');

        $this->isometricPath = IsometricsStorage::getPath($this->uuid);
        $this->head->writeImage($this->isometricPath);
    }

    /**
     * Create $head Imagick Object from previously rendered head.
     *
     * @throws \ImagickException
     */
    protected function createFromFile(): void
    {
        $this->head = new \Imagick($this->isometricPath);
    }

    /**
     * Check cached file.
     */
    protected function verifyCachedFile(): bool
    {
        if (!$this->checkCacheStatusFlag) {
            return true;
        }

        if (!$this->isometricPath || (\filemtime($this->isometricPath) <= $this->lastUpdate)) {
            return false;
        }

        return true;
    }

    /**
     * Render image resized.
     *
     * @param $size
     *
     * @throws \Throwable
     */
    public function render($size): void
    {
        if ($size < self::MIN_SIZE || $size > self::MAX_SIZE) {
            $size = 256;
        }

        if (!$this->verifyCachedFile()) {
            $this->renderFullSize();
        } else {
            $this->createFromFile();
        }

        if ($size !== self::MAX_SIZE) {
            $this->head->resizeImage($size, $size, \Imagick::FILTER_LANCZOS2, 0.9);
        }
    }
}
