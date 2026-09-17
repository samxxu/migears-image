<?php

declare(strict_types=1);

namespace MiGears\Image;

enum ImageType: int
{
    case JPEG = IMAGETYPE_JPEG;
    case PNG = IMAGETYPE_PNG;
    case GIF = IMAGETYPE_GIF;
    case WEBP = IMAGETYPE_WEBP;

    public function extension(): string
    {
        return match ($this) {
            self::JPEG => 'jpg',
            self::PNG => 'png',
            self::GIF => 'gif',
            self::WEBP => 'webp',
        };
    }

    public function mime(): string
    {
        return match ($this) {
            self::JPEG => 'image/jpeg',
            self::PNG => 'image/png',
            self::GIF => 'image/gif',
            self::WEBP => 'image/webp',
        };
    }

    public static function fromExtension(string $extension): ?self
    {
        $ext = strtolower(trim($extension, '.'));

        return match ($ext) {
            'jpg', 'jpeg' => self::JPEG,
            'png' => self::PNG,
            'gif' => self::GIF,
            'webp' => self::WEBP,
            default => null,
        };
    }

    public static function fromMime(string $mime): ?self
    {
        return match (strtolower($mime)) {
            'image/jpeg', 'image/jpg' => self::JPEG,
            'image/png' => self::PNG,
            'image/gif' => self::GIF,
            'image/webp' => self::WEBP,
            default => null,
        };
    }
}
