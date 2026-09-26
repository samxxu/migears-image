<?php

declare(strict_types=1);

namespace MiGears\Image;

use MiGears\Image\Exception\ImageException;

interface ImageInterface
{
    /**
     * Gets image information
     */
    public function info(): ImageInfo;

    /**
     * Proportional resize (specify width, height auto-calculated)
     *
     * @throws ImageException
     */
    public function resizeToWidth(int $width): static;

    /**
     * Proportional resize (specify height, width auto-calculated)
     *
     * @throws ImageException
     */
    public function resizeToHeight(int $height): static;

    /**
     * Proportional resize by maximum side
     *
     * @throws ImageException
     */
    public function resizeToMax(int $maxSize): static;

    /**
     * Resize to specified dimensions (may distort)
     *
     * @throws ImageException
     */
    public function resize(int $width, int $height): static;

    /**
     * Crops the image
     *
     * @throws ImageException
     */
    public function crop(int $width, int $height, int $x = 0, int $y = 0): static;

    /**
     * Center crop
     *
     * @throws ImageException
     */
    public function cropCenter(int $width, int $height): static;

    /**
     * Generates a thumbnail (resize then center crop)
     *
     * @throws ImageException
     */
    public function thumbnail(int $width, int $height): static;

    /**
     * Rotates the image
     *
     * @param int $degrees Rotation angle (0-360)
     * @param int $bgColor Background color for uncovered areas (default: black)
     * @throws ImageException
     */
    public function rotate(int $degrees, int $bgColor = 0): static;

    /**
     * Flips the image horizontally (left-right mirror)
     *
     * @throws ImageException
     */
    public function flipHorizontal(): static;

    /**
     * Flips the image vertically (top-bottom mirror)
     *
     * @throws ImageException
     */
    public function flipVertical(): static;

    /**
     * Flips the image both horizontally and vertically
     *
     * @throws ImageException
     */
    public function flipBoth(): static;

    /**
     * Text watermark
     *
     * @param int $opacity Opacity 0-100
     * @throws ImageException
     */
    public function textWatermark(
        string $text,
        string $fontFile,
        int $fontSize = 12,
        int $x = 10,
        int $y = 10,
        int $opacity = 80,
        int $color = 0xFFFFFF,
    ): static;

    /**
     * Image watermark
     *
     * @param int $opacity Opacity 0-100
     * @throws ImageException
     */
    public function imageWatermark(
        string|ImageInterface $watermark,
        int $x = 10,
        int $y = 10,
        int $opacity = 80,
    ): static;

    /**
     * Saves the image to a file
     *
     * @param int $quality Quality 0-100
     * @throws ImageException
     */
    public function save(string $path, ?ImageType $type = null, int $quality = 90): bool;

    /**
     * Outputs the image as a binary string
     *
     * Useful for sending directly to browser or returning in a response.
     *
     * @param int $quality Quality 0-100
     * @throws ImageException
     */
    public function output(?ImageType $type = null, int $quality = 90): string;

    /**
     * Gets the raw GD image resource
     */
    public function resource(): \GdImage;

    /**
     * Releases the image
     *
     * No-op on PHP 8.0+: the image is freed automatically once the last reference
     * is released. Retained for interface compatibility.
     */
    public function destroy(): void;
}
