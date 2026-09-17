<?php

declare(strict_types=1);

namespace MiGears\Image\Tests;

use MiGears\Image\Exception\ImageException;
use MiGears\Image\GDImage;
use MiGears\Image\ImageType;

class GDImageTest extends ImageTestCase
{
    // ===== Constructor tests =====

    public function testConstructFromFile(): void
    {
        $path = $this->createTestImageFile(200, 150, ImageType::PNG);
        $img = new GDImage($path);

        $info = $img->info();
        $this->assertSame(200, $info->width);
        $this->assertSame(150, $info->height);
        $this->assertSame(ImageType::PNG, $info->type);

        $img->destroy();
    }

    public function testConstructFromJpeg(): void
    {
        $path = $this->createTestImageFile(300, 200, ImageType::JPEG);
        $img = new GDImage($path);

        $this->assertSame(ImageType::JPEG, $img->info()->type);
        $img->destroy();
    }

    public function testConstructFromGif(): void
    {
        $path = $this->createTestImageFile(100, 100, ImageType::GIF);
        $img = new GDImage($path);

        $this->assertSame(ImageType::GIF, $img->info()->type);
        $img->destroy();
    }

    public function testConstructFromWebp(): void
    {
        if (! function_exists('imagewebp')) {
            $this->markTestSkipped('WebP not supported');
        }

        $path = $this->createTestImageFile(100, 100, ImageType::WEBP);
        $img = new GDImage($path);

        $this->assertSame(ImageType::WEBP, $img->info()->type);
        $img->destroy();
    }

    public function testConstructFromGdResource(): void
    {
        $resource = $this->createTestImage(100, 80);
        $img = new GDImage($resource, ImageType::JPEG);

        $info = $img->info();
        $this->assertSame(100, $info->width);
        $this->assertSame(80, $info->height);
        $this->assertSame(ImageType::JPEG, $info->type);

        $img->destroy();
    }

    public function testConstructFromGdResourceDefaultType(): void
    {
        $resource = $this->createTestImage(50, 50);
        $img = new GDImage($resource);

        $this->assertSame(ImageType::PNG, $img->info()->type);
        $img->destroy();
    }

    public function testConstructFileNotFound(): void
    {
        $this->expectException(ImageException::class);
        new GDImage('/nonexistent/path/image.png');
    }

    public function testConstructInvalidFile(): void
    {
        $path = $this->tempDir . '/not-an-image.txt';
        file_put_contents($path, 'not an image');

        $this->expectException(ImageException::class);
        new GDImage($path);
    }

    // ===== info() tests =====

    public function testInfoReturnsImageInfo(): void
    {
        $img = $this->createGDImage(400, 300);
        $info = $img->info();

        $this->assertSame(400, $info->width);
        $this->assertSame(300, $info->height);
        $this->assertSame(ImageType::PNG, $info->type);

        $img->destroy();
    }

    // ===== resize tests =====

    public function testResize(): void
    {
        $img = $this->createGDImage(200, 150);
        $img->resize(100, 75);

        $info = $img->info();
        $this->assertSame(100, $info->width);
        $this->assertSame(75, $info->height);

        $img->destroy();
    }

    public function testResizeInvalidWidth(): void
    {
        $img = $this->createGDImage(200, 150);

        $this->expectException(ImageException::class);
        $img->resize(0, 100);
    }

    public function testResizeInvalidHeight(): void
    {
        $img = $this->createGDImage(200, 150);

        $this->expectException(ImageException::class);
        $img->resize(100, -1);
    }

    public function testResizeToWidth(): void
    {
        $img = $this->createGDImage(200, 100); // 2:1
        $img->resizeToWidth(100);

        $info = $img->info();
        $this->assertSame(100, $info->width);
        $this->assertSame(50, $info->height); // preserve aspect ratio

        $img->destroy();
    }

    public function testResizeToWidthInvalid(): void
    {
        $img = $this->createGDImage(200, 100);

        $this->expectException(ImageException::class);
        $img->resizeToWidth(0);
    }

    public function testResizeToHeight(): void
    {
        $img = $this->createGDImage(200, 100); // 2:1
        $img->resizeToHeight(50);

        $info = $img->info();
        $this->assertSame(100, $info->width); // preserve aspect ratio
        $this->assertSame(50, $info->height);

        $img->destroy();
    }

    public function testResizeToHeightInvalid(): void
    {
        $img = $this->createGDImage(200, 100);

        $this->expectException(ImageException::class);
        $img->resizeToHeight(-5);
    }

    public function testResizeToMaxLandscape(): void
    {
        $img = $this->createGDImage(400, 200); // landscape
        $img->resizeToMax(100);

        $info = $img->info();
        $this->assertSame(100, $info->width); // width limited by max
        $this->assertSame(50, $info->height);

        $img->destroy();
    }

    public function testResizeToMaxPortrait(): void
    {
        $img = $this->createGDImage(200, 400); // portrait
        $img->resizeToMax(100);

        $info = $img->info();
        $this->assertSame(50, $info->width);
        $this->assertSame(100, $info->height); // height limited by max

        $img->destroy();
    }

    public function testResizeToMaxInvalid(): void
    {
        $img = $this->createGDImage(200, 100);

        $this->expectException(ImageException::class);
        $img->resizeToMax(0);
    }

    // ===== crop tests =====

    public function testCrop(): void
    {
        $img = $this->createGDImage(200, 150);
        $img->crop(100, 80, 10, 20);

        $info = $img->info();
        $this->assertSame(100, $info->width);
        $this->assertSame(80, $info->height);

        $img->destroy();
    }

    public function testCropDefaultPosition(): void
    {
        $img = $this->createGDImage(200, 150);
        $img->crop(50, 50); // defaults to x=0, y=0

        $info = $img->info();
        $this->assertSame(50, $info->width);
        $this->assertSame(50, $info->height);

        $img->destroy();
    }

    public function testCropOutOfBounds(): void
    {
        $img = $this->createGDImage(200, 150);

        $this->expectException(ImageException::class);
        $img->crop(100, 100, 150, 100); // exceeds right boundary
    }

    public function testCropNegativePosition(): void
    {
        $img = $this->createGDImage(200, 150);

        $this->expectException(ImageException::class);
        $img->crop(100, 100, -10, 0);
    }

    public function testCropInvalidSize(): void
    {
        $img = $this->createGDImage(200, 150);

        $this->expectException(ImageException::class);
        $img->crop(0, 100);
    }

    public function testCropCenter(): void
    {
        $img = $this->createGDImage(200, 150);
        $img->cropCenter(100, 80);

        $info = $img->info();
        $this->assertSame(100, $info->width);
        $this->assertSame(80, $info->height);

        $img->destroy();
    }

    public function testCropCenterSquare(): void
    {
        $img = $this->createGDImage(200, 150);
        $img->cropCenter(100, 100);

        $info = $img->info();
        $this->assertSame(100, $info->width);
        $this->assertSame(100, $info->height);

        $img->destroy();
    }

    // ===== thumbnail tests =====

    public function testThumbnailLandscape(): void
    {
        $img = $this->createGDImage(400, 200); // 2:1
        $img->thumbnail(100, 100);

        $info = $img->info();
        $this->assertSame(100, $info->width);
        $this->assertSame(100, $info->height);

        $img->destroy();
    }

    public function testThumbnailPortrait(): void
    {
        $img = $this->createGDImage(200, 400);
        $img->thumbnail(80, 60);

        $info = $img->info();
        $this->assertSame(80, $info->width);
        $this->assertSame(60, $info->height);

        $img->destroy();
    }

    public function testThumbnailExactSameRatio(): void
    {
        $img = $this->createGDImage(200, 100); // 2:1
        $img->thumbnail(100, 50); // also 2:1

        $info = $img->info();
        $this->assertSame(100, $info->width);
        $this->assertSame(50, $info->height);

        $img->destroy();
    }

    // ===== save tests =====

    public function testSavePng(): void
    {
        $img = $this->createGDImage(100, 80, ImageType::PNG);
        $outputPath = $this->tempDir . '/output.png';

        $result = $img->save($outputPath);
        $this->assertTrue($result);
        $this->assertFileExists($outputPath);
        $this->assertImageSize($outputPath, 100, 80);
        $this->assertImageType($outputPath, ImageType::PNG);

        $img->destroy();
    }

    public function testSaveJpeg(): void
    {
        $img = $this->createGDImage(100, 80, ImageType::JPEG);
        $outputPath = $this->tempDir . '/output.jpg';

        $result = $img->save($outputPath, quality: 80);
        $this->assertTrue($result);
        $this->assertImageType($outputPath, ImageType::JPEG);

        $img->destroy();
    }

    public function testSaveGif(): void
    {
        $img = $this->createGDImage(100, 80, ImageType::GIF);
        $outputPath = $this->tempDir . '/output.gif';

        $result = $img->save($outputPath);
        $this->assertTrue($result);
        $this->assertImageType($outputPath, ImageType::GIF);

        $img->destroy();
    }

    public function testSaveWebp(): void
    {
        if (! function_exists('imagewebp')) {
            $this->markTestSkipped('WebP not supported');
        }

        $img = $this->createGDImage(100, 80, ImageType::WEBP);
        $outputPath = $this->tempDir . '/output.webp';

        $result = $img->save($outputPath);
        $this->assertTrue($result);
        $this->assertImageType($outputPath, ImageType::WEBP);

        $img->destroy();
    }

    public function testSaveFormatConversion(): void
    {
        // Convert from PNG to JPEG
        $img = $this->createGDImage(100, 80, ImageType::PNG);
        $outputPath = $this->tempDir . '/converted.jpg';

        $img->save($outputPath, ImageType::JPEG);
        $this->assertImageType($outputPath, ImageType::JPEG);
        $this->assertImageSize($outputPath, 100, 80);

        $img->destroy();
    }

    public function testSaveAutoDetectTypeFromExtension(): void
    {
        $img = $this->createGDImage(100, 80, ImageType::PNG);
        $outputPath = $this->tempDir . '/auto.gif';

        $img->save($outputPath); // Auto-detect from extension
        $this->assertImageType($outputPath, ImageType::GIF);

        $img->destroy();
    }

    public function testSaveCreatesDirectory(): void
    {
        $img = $this->createGDImage(50, 50);
        $outputPath = $this->tempDir . '/sub/dir/output.png';

        $img->save($outputPath);
        $this->assertFileExists($outputPath);

        $img->destroy();
    }

    // ===== Chained call tests =====

    public function testChainCalls(): void
    {
        $img = $this->createGDImage(400, 300);
        $outputPath = $this->tempDir . '/chain.jpg';

        $result = $img
            ->resizeToWidth(200)
            ->cropCenter(150, 150)
            ->save($outputPath, ImageType::JPEG);

        $this->assertTrue($result);
        $this->assertImageSize($outputPath, 150, 150);
        $this->assertImageType($outputPath, ImageType::JPEG);

        $img->destroy();
    }

    // ===== Watermark tests =====

    public function testImageWatermark(): void
    {
        $img = $this->createGDImage(200, 150, ImageType::PNG);

        // Create watermark image
        $wmPath = $this->createTestImageFile(50, 30, ImageType::PNG, 0x00FF00);

        $img->imageWatermark($wmPath, 10, 10, 50);

        $outputPath = $this->tempDir . '/watermarked.png';
        $img->save($outputPath);

        $this->assertImageSize($outputPath, 200, 150);

        $img->destroy();
    }

    public function testImageWatermarkWithImageInterface(): void
    {
        $img = $this->createGDImage(200, 150, ImageType::PNG);
        $wm = $this->createGDImage(40, 40, ImageType::PNG);

        $img->imageWatermark($wm, 5, 5, 80);

        $outputPath = $this->tempDir . '/wm-interface.png';
        $img->save($outputPath);
        $this->assertImageSize($outputPath, 200, 150);

        $img->destroy();
        $wm->destroy();
    }

    public function testTextWatermark(): void
    {
        $img = $this->createGDImage(200, 100, ImageType::PNG);

        // Find system font
        $fontFile = $this->findFontFile();
        if ($fontFile === null) {
            $this->markTestSkipped('No usable font file found');
        }

        $img->textWatermark(
            text: 'Hello miGears',
            fontFile: $fontFile,
            fontSize: 14,
            x: 10,
            y: 10,
            opacity: 80,
            color: 0xFFFFFF,
        );

        $outputPath = $this->tempDir . '/text-wm.png';
        $img->save($outputPath);
        $this->assertImageSize($outputPath, 200, 100);

        $img->destroy();
    }

    public function testTextWatermarkFontNotFound(): void
    {
        $img = $this->createGDImage(200, 100);

        $this->expectException(ImageException::class);
        $img->textWatermark('test', '/nonexistent/font.ttf');
    }

    // ===== resource / destroy tests =====

    public function testResourceReturnsGdImage(): void
    {
        $img = $this->createGDImage(50, 50);
        $resource = $img->resource();

        $this->assertInstanceOf(\GdImage::class, $resource);
        $img->destroy();
    }

    public function testDestroy(): void
    {
        $img = $this->createGDImage(50, 50);
        $img->destroy();

        // Calling destroy again in destructor should not error
        $this->assertTrue(true);
    }

    // ===== Rotate tests =====

    public function testRotate90Degrees(): void
    {
        $img = $this->createGDImage(200, 100);
        $result = $img->rotate(90);

        $this->assertSame($img, $result);
        $info = $img->info();
        // After 90° rotation, width and height are swapped
        $this->assertSame(100, $info->width);
        $this->assertSame(200, $info->height);
        $img->destroy();
    }

    public function testRotate180Degrees(): void
    {
        $img = $this->createGDImage(200, 100);
        $img->rotate(180);

        $info = $img->info();
        // 180° rotation preserves dimensions
        $this->assertSame(200, $info->width);
        $this->assertSame(100, $info->height);
        $img->destroy();
    }

    public function testRotate270Degrees(): void
    {
        $img = $this->createGDImage(200, 100);
        $img->rotate(270);

        $info = $img->info();
        $this->assertSame(100, $info->width);
        $this->assertSame(200, $info->height);
        $img->destroy();
    }

    public function testRotateWithBgColor(): void
    {
        $img = $this->createGDImage(100, 100);
        $result = $img->rotate(45, 0xFF0000); // red background

        $this->assertSame($img, $result);
        $info = $img->info();
        // 45° rotation increases dimensions
        $this->assertGreaterThan(100, $info->width);
        $this->assertGreaterThan(100, $info->height);
        $img->destroy();
    }

    // ===== Flip tests =====

    public function testFlipHorizontalPreservesDimensions(): void
    {
        $img = $this->createGDImage(200, 100);
        $result = $img->flipHorizontal();

        $this->assertSame($img, $result);
        $info = $img->info();
        $this->assertSame(200, $info->width);
        $this->assertSame(100, $info->height);
        $img->destroy();
    }

    public function testFlipVerticalPreservesDimensions(): void
    {
        $img = $this->createGDImage(200, 100);
        $result = $img->flipVertical();

        $this->assertSame($img, $result);
        $info = $img->info();
        $this->assertSame(200, $info->width);
        $this->assertSame(100, $info->height);
        $img->destroy();
    }

    public function testFlipBothPreservesDimensions(): void
    {
        $img = $this->createGDImage(200, 100);
        $result = $img->flipBoth();

        $this->assertSame($img, $result);
        $info = $img->info();
        $this->assertSame(200, $info->width);
        $this->assertSame(100, $info->height);
        $img->destroy();
    }

    public function testFlipHorizontalTwiceReturnsOriginal(): void
    {
        // Create an image with a distinct pixel on the left
        $img = $this->createGDImage(10, 5);
        $resource = $img->resource();
        $red = imagecolorallocate($resource, 255, 0, 0);
        imagesetpixel($resource, 0, 2, $red);

        // Get pixel at right side before flip
        $colorBefore = imagecolorat($resource, 9, 2);

        $img->flipHorizontal();
        $img->flipHorizontal();

        $resource = $img->resource();
        $colorAfter = imagecolorat($resource, 0, 2);

        // Pixel at position (0,2) should still be red after two flips
        $this->assertSame($red, $colorAfter);
        $img->destroy();
    }

    // ===== Output tests =====

    public function testOutputReturnsString(): void
    {
        $img = $this->createGDImage(50, 50);
        $data = $img->output();

        $this->assertIsString($data);
        $this->assertNotEmpty($data);
        // PNG header check (default type is PNG for GdImage source)
        $this->assertStringStartsWith(pack('H*', '89504E47'), $data);
        $img->destroy();
    }

    public function testOutputJpeg(): void
    {
        $path = $this->createTestImageFile(50, 50, ImageType::JPEG);
        $img = new GDImage($path);
        $data = $img->output(ImageType::JPEG);

        $this->assertIsString($data);
        $this->assertNotEmpty($data);
        // JPEG header: FF D8 FF
        $this->assertStringStartsWith(pack('H*', 'FFD8FF'), $data);
        $img->destroy();
    }

    public function testOutputGif(): void
    {
        $path = $this->createTestImageFile(50, 50, ImageType::GIF);
        $img = new GDImage($path);
        $data = $img->output(ImageType::GIF);

        $this->assertIsString($data);
        $this->assertNotEmpty($data);
        $this->assertStringStartsWith('GIF', $data);
        $img->destroy();
    }

    public function testOutputWebp(): void
    {
        if (! function_exists('imagewebp')) {
            $this->markTestSkipped('WebP not supported');
        }

        $path = $this->createTestImageFile(50, 50, ImageType::WEBP);
        $img = new GDImage($path);
        $data = $img->output(ImageType::WEBP);

        $this->assertIsString($data);
        $this->assertNotEmpty($data);
        // WebP header: RIFF....WEBP
        $this->assertStringStartsWith('RIFF', $data);
        $this->assertStringContainsString('WEBP', substr($data, 0, 16));
        $img->destroy();
    }

    public function testOutputCanBeLoadedBack(): void
    {
        $img = $this->createGDImage(50, 50);
        $data = $img->output(ImageType::PNG);

        // Create a temp file and load it back
        $tmpFile = sys_get_temp_dir() . '/migears_test_output_' . uniqid() . '.png';
        file_put_contents($tmpFile, $data);

        $loaded = new GDImage($tmpFile);
        $this->assertSame(50, $loaded->info()->width);
        $this->assertSame(50, $loaded->info()->height);

        $img->destroy();
        $loaded->destroy();
        unlink($tmpFile);
    }

    // ===== Helper methods =====

    private function findFontFile(): ?string
    {
        $possiblePaths = [
            '/System/Library/Fonts/Supplemental/Arial.ttf',
            '/System/Library/Fonts/PingFang.ttc',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
