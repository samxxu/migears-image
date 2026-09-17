<?php

declare(strict_types=1);

namespace MiGears\Image\Tests;

use MiGears\Image\ImageType;

class ImageTypeTest extends ImageTestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(IMAGETYPE_JPEG, ImageType::JPEG->value);
        $this->assertSame(IMAGETYPE_PNG, ImageType::PNG->value);
        $this->assertSame(IMAGETYPE_GIF, ImageType::GIF->value);
        $this->assertSame(IMAGETYPE_WEBP, ImageType::WEBP->value);
    }

    public function testExtension(): void
    {
        $this->assertSame('jpg', ImageType::JPEG->extension());
        $this->assertSame('png', ImageType::PNG->extension());
        $this->assertSame('gif', ImageType::GIF->extension());
        $this->assertSame('webp', ImageType::WEBP->extension());
    }

    public function testMime(): void
    {
        $this->assertSame('image/jpeg', ImageType::JPEG->mime());
        $this->assertSame('image/png', ImageType::PNG->mime());
        $this->assertSame('image/gif', ImageType::GIF->mime());
        $this->assertSame('image/webp', ImageType::WEBP->mime());
    }

    public function testFromExtension(): void
    {
        $this->assertSame(ImageType::JPEG, ImageType::fromExtension('jpg'));
        $this->assertSame(ImageType::JPEG, ImageType::fromExtension('jpeg'));
        $this->assertSame(ImageType::JPEG, ImageType::fromExtension('JPG'));
        $this->assertSame(ImageType::PNG, ImageType::fromExtension('png'));
        $this->assertSame(ImageType::GIF, ImageType::fromExtension('gif'));
        $this->assertSame(ImageType::WEBP, ImageType::fromExtension('webp'));
        $this->assertNull(ImageType::fromExtension('bmp'));
        $this->assertNull(ImageType::fromExtension(''));
    }

    public function testFromMime(): void
    {
        $this->assertSame(ImageType::JPEG, ImageType::fromMime('image/jpeg'));
        $this->assertSame(ImageType::JPEG, ImageType::fromMime('image/jpg'));
        $this->assertSame(ImageType::PNG, ImageType::fromMime('image/png'));
        $this->assertSame(ImageType::GIF, ImageType::fromMime('image/gif'));
        $this->assertSame(ImageType::WEBP, ImageType::fromMime('image/webp'));
        $this->assertNull(ImageType::fromMime('image/bmp'));
    }

    public function testTryFrom(): void
    {
        $this->assertSame(ImageType::JPEG, ImageType::tryFrom(IMAGETYPE_JPEG));
        $this->assertSame(ImageType::PNG, ImageType::tryFrom(IMAGETYPE_PNG));
        $this->assertNull(ImageType::tryFrom(999));
    }
}
