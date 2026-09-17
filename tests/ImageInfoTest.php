<?php

declare(strict_types=1);

namespace MiGears\Image\Tests;

use MiGears\Image\ImageInfo;
use MiGears\Image\ImageType;

class ImageInfoTest extends ImageTestCase
{
    public function testConstructorAndProperties(): void
    {
        $info = new ImageInfo(width: 800, height: 600, type: ImageType::JPEG);

        $this->assertSame(800, $info->width);
        $this->assertSame(600, $info->height);
        $this->assertSame(ImageType::JPEG, $info->type);
    }

    public function testMime(): void
    {
        $info = new ImageInfo(width: 100, height: 100, type: ImageType::PNG);
        $this->assertSame('image/png', $info->mime());
    }

    public function testExtension(): void
    {
        $info = new ImageInfo(width: 100, height: 100, type: ImageType::WEBP);
        $this->assertSame('webp', $info->extension());
    }

    public function testAspectRatioLandscape(): void
    {
        $info = new ImageInfo(width: 800, height: 400, type: ImageType::JPEG);
        $this->assertEqualsWithDelta(2.0, $info->aspectRatio(), 0.001);
    }

    public function testAspectRatioPortrait(): void
    {
        $info = new ImageInfo(width: 400, height: 800, type: ImageType::JPEG);
        $this->assertEqualsWithDelta(0.5, $info->aspectRatio(), 0.001);
    }

    public function testAspectRatioSquare(): void
    {
        $info = new ImageInfo(width: 500, height: 500, type: ImageType::JPEG);
        $this->assertEqualsWithDelta(1.0, $info->aspectRatio(), 0.001);
    }

    public function testIsSquare(): void
    {
        $this->assertTrue((new ImageInfo(width: 300, height: 300, type: ImageType::PNG))->isSquare());
        $this->assertFalse((new ImageInfo(width: 300, height: 200, type: ImageType::PNG))->isSquare());
    }

    public function testIsLandscape(): void
    {
        $this->assertTrue((new ImageInfo(width: 800, height: 600, type: ImageType::PNG))->isLandscape());
        $this->assertFalse((new ImageInfo(width: 600, height: 800, type: ImageType::PNG))->isLandscape());
        $this->assertFalse((new ImageInfo(width: 500, height: 500, type: ImageType::PNG))->isLandscape());
    }

    public function testIsPortrait(): void
    {
        $this->assertTrue((new ImageInfo(width: 600, height: 800, type: ImageType::PNG))->isPortrait());
        $this->assertFalse((new ImageInfo(width: 800, height: 600, type: ImageType::PNG))->isPortrait());
        $this->assertFalse((new ImageInfo(width: 500, height: 500, type: ImageType::PNG))->isPortrait());
    }

    public function testReadonly(): void
    {
        $info = new ImageInfo(width: 100, height: 100, type: ImageType::PNG);

        $this->expectException(\Error::class);
        $info->width = 200;
    }
}
