<?php

declare(strict_types=1);

namespace MiGears\Image\Tests;

use PHPUnit\Framework\TestCase;
use MiGears\Image\GDImage;
use MiGears\Image\ImageType;

abstract class ImageTestCase extends TestCase
{
    protected string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD extension not installed');
        }

        $this->tempDir = sys_get_temp_dir() . '/migears-image-test-' . uniqid();
        mkdir($this->tempDir, 0755, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->tempDir);
        parent::tearDown();
    }

    /**
     * Create test image (GD resource)
     */
    protected function createTestImage(int $width = 200, int $height = 150, int $color = 0xFF6600): \GdImage
    {
        $img = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($img, ($color >> 16) & 0xFF, ($color >> 8) & 0xFF, $color & 0xFF);
        imagefill($img, 0, 0, $bgColor);

        // Draw a dark rectangle for easier crop and resize testing
        $rectColor = imagecolorallocate($img, 0x33, 0x66, 0x99);
        imagefilledrectangle($img, 20, 20, $width - 20, $height - 20, $rectColor);

        return $img;
    }

    /**
     * Create test image file and return path
     */
    protected function createTestImageFile(
        int $width = 200,
        int $height = 150,
        ImageType $type = ImageType::PNG,
        int $color = 0xFF6600,
    ): string {
        $img = $this->createTestImage($width, $height, $color);
        $path = $this->tempDir . '/test-' . $width . 'x' . $height . '.' . $type->extension();

        match ($type) {
            ImageType::JPEG => imagejpeg($img, $path, 90),
            ImageType::PNG => imagepng($img, $path),
            ImageType::GIF => imagegif($img, $path),
            ImageType::WEBP => imagewebp($img, $path, 90),
        };

        return $path;
    }

    protected function createGDImage(int $width = 200, int $height = 150, ImageType $type = ImageType::PNG): GDImage
    {
        $path = $this->createTestImageFile($width, $height, $type);
        return new GDImage($path);
    }

    /**
     * Create a PNG whose every pixel is fully transparent, and return the path.
     */
    protected function createTransparentPngFile(int $width = 40, int $height = 40): string
    {
        $img = imagecreatetruecolor($width, $height);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparent);

        $path = $this->tempDir . '/transparent-' . $width . 'x' . $height . '.png';
        imagepng($img, $path);

        return $path;
    }

    /**
     * Read the 7-bit alpha channel of a pixel of a PNG on disk (127 = transparent).
     */
    protected function readAlpha(string $path, int $x, int $y): int
    {
        $img = imagecreatefrompng($path);
        return (imagecolorat($img, $x, $y) >> 24) & 0x7F;
    }

    protected function assertImageSize(string $path, int $expectedWidth, int $expectedHeight): void
    {
        $info = getimagesize($path);
        $this->assertNotFalse($info, "Unable to read image: {$path}");
        $this->assertSame($expectedWidth, $info[0], "Width mismatch");
        $this->assertSame($expectedHeight, $info[1], "Height mismatch");
    }

    protected function assertImageType(string $path, ImageType $expectedType): void
    {
        $info = getimagesize($path);
        $this->assertNotFalse($info);
        $this->assertSame($expectedType->value, $info[2], "Image type mismatch");
    }

    private function removeDirectory(string $dir): void
    {
        if (! is_dir($dir)) return;
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $fullPath = $dir . '/' . $item;
            is_dir($fullPath) ? $this->removeDirectory($fullPath) : unlink($fullPath);
        }
        rmdir($dir);
    }
}
