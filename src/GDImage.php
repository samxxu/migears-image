<?php

declare(strict_types=1);

namespace MiGears\Image;

use MiGears\Image\Exception\ImageException;

class GDImage implements ImageInterface
{
    public const VERSION = '2.0.0';

    private \GdImage $image;
    private ImageType $type;

    public function __construct(string|\GdImage $source, ?ImageType $type = null)
    {
        if (! extension_loaded('gd')) {
            throw new ImageException('GD extension is not installed');
        }

        if ($source instanceof \GdImage) {
            $this->image = $source;
            $this->type = $type ?? ImageType::PNG;
            return;
        }

        if (! file_exists($source)) {
            throw new ImageException("Image file does not exist: {$source}");
        }

        $info = getimagesize($source);
        if ($info === false) {
            throw new ImageException("Unrecognized image format: {$source}");
        }

        $detectedType = ImageType::tryFrom($info[2]);
        if ($detectedType === null) {
            throw new ImageException("Unsupported image type: {$info['mime']}");
        }

        // For file sources the real format always wins, so a declared type may only confirm it.
        if ($type !== null && $type !== $detectedType) {
            throw new ImageException("Declared type {$type->name} does not match detected type {$detectedType->name}");
        }

        $this->type = $detectedType;
        $this->image = $this->createFromFile($source, $this->type);
    }

    public function info(): ImageInfo
    {
        return new ImageInfo(
            width: imagesx($this->image),
            height: imagesy($this->image),
            type: $this->type,
        );
    }

    public function resizeToWidth(int $width): static
    {
        if ($width <= 0) throw new ImageException('Width must be greater than 0');
        $info = $this->info();
        return $this->resize($width, (int) round($width / $info->aspectRatio()));
    }

    public function resizeToHeight(int $height): static
    {
        if ($height <= 0) throw new ImageException('Height must be greater than 0');
        $info = $this->info();
        return $this->resize((int) round($height * $info->aspectRatio()), $height);
    }

    public function resizeToMax(int $maxSize): static
    {
        if ($maxSize <= 0) throw new ImageException('Maximum size must be greater than 0');
        $info = $this->info();
        return $info->isLandscape()
            ? $this->resizeToWidth($maxSize)
            : $this->resizeToHeight($maxSize);
    }

    public function resize(int $width, int $height): static
    {
        if ($width <= 0 || $height <= 0) {
            throw new ImageException('Width and height must be greater than 0');
        }
        $info = $this->info();
        $dest = $this->createTrueColor($width, $height);
        /** @var bool $ok */
        $ok = imagecopyresampled(
            $dest, $this->image, 0, 0, 0, 0,
            $width, $height, $info->width, $info->height
        );
        if (! $ok) {
            throw new ImageException('Failed to resize image');
        }
        $this->image = $dest;
        return $this;
    }

    public function crop(int $width, int $height, int $x = 0, int $y = 0): static
    {
        if ($width <= 0 || $height <= 0) {
            throw new ImageException('Width and height must be greater than 0');
        }
        $info = $this->info();
        if ($x + $width > $info->width || $y + $height > $info->height || $x < 0 || $y < 0) {
            throw new ImageException('Crop area exceeds image bounds');
        }
        $dest = $this->createTrueColor($width, $height);
        /** @var bool $ok */
        $ok = imagecopy($dest, $this->image, 0, 0, $x, $y, $width, $height);
        if (! $ok) {
            throw new ImageException('Failed to crop image');
        }
        $this->image = $dest;
        return $this;
    }

    public function cropCenter(int $width, int $height): static
    {
        $info = $this->info();
        if ($width >= $info->width && $height >= $info->height) {
            return $this; // requested crop covers the whole image, keep as-is
        }
        $x = (int) round(($info->width - $width) / 2);
        $y = (int) round(($info->height - $height) / 2);
        return $this->crop($width, $height, max(0, $x), max(0, $y));
    }

    public function thumbnail(int $width, int $height): static
    {
        $info = $this->info();
        $ratio = max($width / $info->width, $height / $info->height);
        $this->resize(
            (int) round($info->width * $ratio),
            (int) round($info->height * $ratio)
        );
        return $this->cropCenter($width, $height);
    }

    public function rotate(int $degrees, int $bgColor = 0): static
    {
        $rotated = imagerotate(
            $this->image,
            $degrees,
            $bgColor
        );
        if ($rotated === false) {
            throw new ImageException('Failed to rotate image');
        }
        // Preserve alpha for PNG/WEBP
        if ($this->type === ImageType::PNG || $this->type === ImageType::WEBP) {
            imagealphablending($rotated, false);
            imagesavealpha($rotated, true);
        }
        $this->image = $rotated;
        return $this;
    }

    public function flipHorizontal(): static
    {
        /** @var bool $ok */
        $ok = imageflip($this->image, IMG_FLIP_HORIZONTAL);
        if (! $ok) {
            throw new ImageException('Failed to flip image horizontally');
        }
        return $this;
    }

    public function flipVertical(): static
    {
        /** @var bool $ok */
        $ok = imageflip($this->image, IMG_FLIP_VERTICAL);
        if (! $ok) {
            throw new ImageException('Failed to flip image vertically');
        }
        return $this;
    }

    public function flipBoth(): static
    {
        /** @var bool $ok */
        $ok = imageflip($this->image, IMG_FLIP_BOTH);
        if (! $ok) {
            throw new ImageException('Failed to flip image');
        }
        return $this;
    }

    public function textWatermark(
        string $text,
        string $fontFile,
        int $fontSize = 12,
        int $x = 10,
        int $y = 10,
        int $opacity = 80,
        int $color = 0xFFFFFF,
    ): static {
        if ($opacity < 0 || $opacity > 100) throw new ImageException('Opacity must be between 0 and 100');
        if (! file_exists($fontFile)) {
            throw new ImageException("Font file does not exist: {$fontFile}");
        }
        $alpha = (int) round((100 - $opacity) * 1.27);
        $textColor = imagecolorallocatealpha(
            $this->image,
            ($color >> 16) & 0xFF,
            ($color >> 8) & 0xFF,
            $color & 0xFF,
            $alpha
        );
        // Suppress GD's own warning: a failed draw is reported as ImageException instead.
        $box = @imagettftext($this->image, $fontSize, 0, $x, $y + $fontSize, $textColor, $fontFile, $text);
        if ($box === false) {
            throw new ImageException('Failed to draw text watermark');
        }
        return $this;
    }

    public function imageWatermark(
        string|ImageInterface $watermark,
        int $x = 10,
        int $y = 10,
        int $opacity = 80,
    ): static {
        if ($opacity < 0 || $opacity > 100) throw new ImageException('Opacity must be between 0 and 100');
        $wm = $watermark instanceof ImageInterface ? $watermark : new self($watermark);
        $wmResource = $wm->resource();
        $wmInfo = $wm->info();
        // Scratch canvas for compositing. Deliberately not createTrueColor(): that helper
        // toggles alpha settings from $this->type, which would change watermark blending.
        $cut = imagecreatetruecolor($wmInfo->width, $wmInfo->height);
        if ($cut === false) {
            throw new ImageException('Failed to create watermark canvas');
        }
        /** @var bool $backgroundCopied */
        $backgroundCopied = imagecopy($cut, $this->image, 0, 0, $x, $y, $wmInfo->width, $wmInfo->height);
        /** @var bool $watermarkCopied */
        $watermarkCopied = imagecopy($cut, $wmResource, 0, 0, 0, 0, $wmInfo->width, $wmInfo->height);
        /** @var bool $merged */
        $merged = imagecopymerge($this->image, $cut, $x, $y, 0, 0, $wmInfo->width, $wmInfo->height, $opacity);
        if (! $backgroundCopied || ! $watermarkCopied || ! $merged) {
            throw new ImageException('Failed to apply image watermark');
        }
        return $this;
    }

    public function save(string $path, ?ImageType $type = null, int $quality = 90): bool
    {
        if ($quality < 0 || $quality > 100) throw new ImageException('Quality must be between 0 and 100');
        $saveType = $type ?? $this->detectTypeFromPath($path) ?? $this->type;
        $dir = dirname($path);
        if (! is_dir($dir) && ! mkdir($dir, 0755, true)) {
            throw new ImageException("Cannot create directory: {$dir}");
        }
        return $this->outputTo($path, $saveType, $quality);
    }

    public function output(?ImageType $type = null, int $quality = 90): string
    {
        if ($quality < 0 || $quality > 100) throw new ImageException('Quality must be between 0 and 100');
        $outputType = $type ?? $this->type;
        $level = ob_get_level();
        ob_start();
        try {
            $ok = $this->outputTo(null, $outputType, $quality);
            $data = ob_get_clean();
        } finally {
            // Never leave a dangling buffer behind if encoding throws.
            if (ob_get_level() > $level) {
                ob_end_clean();
            }
        }
        if (! $ok || $data === false || $data === '') {
            throw new ImageException('Failed to output image data');
        }
        return $data;
    }

    public function resource(): \GdImage
    {
        return $this->image;
    }

    public function destroy(): void
    {
        // No-op on PHP 8.0+: \GdImage is an object, so the image is freed automatically
        // once the last reference is released. Kept for interface compatibility.
    }

    // --- Internal ---

    private function createFromFile(string $path, ImageType $type): \GdImage
    {
        $image = match ($type) {
            ImageType::JPEG => imagecreatefromjpeg($path),
            ImageType::PNG => imagecreatefrompng($path),
            ImageType::GIF => imagecreatefromgif($path),
            ImageType::WEBP => imagecreatefromwebp($path),
        };
        if ($image === false) {
            throw new ImageException("Failed to load image: {$path}");
        }
        return $image;
    }

    private function createTrueColor(int $width, int $height): \GdImage
    {
        $dest = imagecreatetruecolor($width, $height);
        if ($dest === false) {
            throw new ImageException('Failed to create image canvas');
        }
        if ($this->type === ImageType::PNG || $this->type === ImageType::WEBP) {
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
        }
        return $dest;
    }

    private function detectTypeFromPath(string $path): ?ImageType
    {
        return ImageType::fromExtension(strtolower(pathinfo($path, PATHINFO_EXTENSION)));
    }

    private function outputTo(?string $path, ImageType $type, int $quality): bool
    {
        return match ($type) {
            ImageType::JPEG => imagejpeg($this->image, $path, $quality),
            ImageType::PNG => imagepng($this->image, $path, (int) round((100 - $quality) / 11.1)),
            ImageType::GIF => imagegif($this->image, $path),
            ImageType::WEBP => imagewebp($this->image, $path, $quality),
        };
    }
}
