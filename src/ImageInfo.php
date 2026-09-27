<?php

declare(strict_types=1);

namespace MiGears\Image;

/**
 * A value object whose properties are immutable: the constructor is the only
 * way in, and the accessors are read-only views of it.
 *
 * The properties carry `readonly` one by one rather than the class carrying a
 * `readonly class` modifier, which is PHP 8.2 syntax while this package
 * requires php ^8.1.
 */
final class ImageInfo
{
    public function __construct(
        public readonly int $width,
        public readonly int $height,
        public readonly ImageType $type,
    ) {
    }

    public function mime(): string
    {
        return $this->type->mime();
    }

    public function extension(): string
    {
        return $this->type->extension();
    }

    public function aspectRatio(): float
    {
        return $this->height > 0 ? $this->width / $this->height : 0.0;
    }

    public function isSquare(): bool
    {
        return $this->width === $this->height;
    }

    public function isLandscape(): bool
    {
        return $this->width > $this->height;
    }

    public function isPortrait(): bool
    {
        return $this->width < $this->height;
    }
}
