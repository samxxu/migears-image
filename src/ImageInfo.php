<?php

declare(strict_types=1);

namespace MiGears\Image;

final readonly class ImageInfo
{
    public function __construct(
        public int $width,
        public int $height,
        public ImageType $type,
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
