<?php

declare(strict_types=1);

namespace MiGears\Image;

enum ImageType: int
{
    // The values mirror the IMAGETYPE_* ints of ext-gd, written out because a
    // backed enum case has to be compile-time evaluable on PHP 8.1, where
    // naming a constant is a fatal error; 8.2 moved that check to runtime.
    // ImageTypeTest asserts each value against its constant, so drift shows up.
    case JPEG = 2;
    case PNG = 3;
    case GIF = 1;
    case WEBP = 18;

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
