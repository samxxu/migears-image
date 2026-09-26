# migears/image

![Version](https://img.shields.io/badge/version-2.0.0-blue)

A minimalist image processing toolkit based on PHP 8.1+ and the GD extension.

> **Background**: miGears is the open-source successor of **TinyGears**, a
> self-developed PHP framework. It was renamed and open-sourced recently because
> the name *TinyGears* is already taken in the open-source community.

## Features

- **Zero required dependencies** — GD extension is a suggested dependency, implementations can be replaced as needed
- **Minimalist API** — Chainable calls, intuitive and easy to use
- **Type-safe** — Full type declarations, readonly value objects, enums
- **Lightweight** — Core class within a few hundred lines, readable in one sitting
- **High test coverage** — Uses GD to generate test images, covering all core functionality

## Requirements

- PHP 8.1+
- ext-gd (suggested)

## Installation

```bash
composer require migears/image
```

## Quick Start

```php
use MiGears\Image\GDImage;
use MiGears\Image\ImageType;

// Load from file
$img = new GDImage('/path/to/image.jpg');

// Chain operations and save
$img->resizeToWidth(800)
    ->cropCenter(400, 300)
    ->save('/path/to/output.jpg', ImageType::JPEG, 90);

// Generate thumbnail (resize then crop, centered)
$img->thumbnail(200, 200)->save('/path/to/thumb.jpg');

// Add image watermark
$img->imageWatermark('/path/to/watermark.png', 10, 10, 70)
    ->save('/path/to/wm.jpg');

// Add text watermark
$img->textWatermark(
    text: 'Copyright',
    fontFile: '/path/to/font.ttf',
    fontSize: 14,
    x: 10,
    y: 10,
    opacity: 80,
    color: 0xFFFFFF,
)->save('/path/to/text-wm.jpg');

// Format conversion
$img->save('/path/to/output.webp', ImageType::WEBP);
```

### Rotate & Flip

```php
use MiGears\Image\GDImage;

$img = new GDImage('/path/to/photo.jpg');

// Rotate 90 degrees clockwise
$img->rotate(90)->save('/path/to/rotated.jpg');

// Rotate with custom background color (for non-90° angles)
$img->rotate(45, 0xFFFFFF)->save('/path/to/tilted.jpg');

// Flip horizontally (mirror effect)
$img->flipHorizontal()->save('/path/to/mirrored.jpg');

// Flip vertically
$img->flipVertical()->save('/path/to/flipped.jpg');
```

### Output Directly to Browser

```php
use MiGears\Image\GDImage;
use MiGears\Image\ImageType;

// Generate avatar dynamically
$img = new GDImage('/path/to/avatar.png');
$img->resizeToWidth(128);

// Set Content-Type and output
header('Content-Type: ' . ImageType::PNG->mime());
echo $img->output(ImageType::PNG);
```

### Practical: User Upload Thumbnail Generator

```php
use MiGears\Image\GDImage;
use MiGears\Image\ImageType;

function generateThumbnails(string $sourcePath, string $outputDir): void
{
    $img = new GDImage($sourcePath);

    // Large: 800px wide
    $img->resizeToWidth(800)
        ->save("{$outputDir}/large.jpg", ImageType::JPEG, 85);

    // Medium: 400px wide
    $img->resizeToWidth(400)
        ->save("{$outputDir}/medium.jpg", ImageType::JPEG, 80);

    // Square thumbnail: 200x200 (center crop)
    $img->thumbnail(200, 200)
        ->save("{$outputDir}/thumb.jpg", ImageType::JPEG, 75);

    $img->destroy();
}
```

## API Overview

### Construction

```php
// From file path — the type is detected from the file content
new GDImage(string $filePath);

// Passing a type for a file path is optional and may only confirm the detected type,
// otherwise ImageException is thrown
new GDImage(string $filePath, ImageType $type);

// From GD resource — there is no file to inspect, so $type is used as-is (defaults to PNG)
new GDImage(\GdImage $resource, ?ImageType $type = null);
```

### Information

| Method | Description |
|--------|-------------|
| `info(): ImageInfo` | Get image information (width, height, type, mime, extension) |

### Resizing

| Method | Description |
|--------|-------------|
| `resize(int $width, int $height): static` | Resize to specified dimensions (may distort) |
| `resizeToWidth(int $width): static` | Proportional resize (by width) |
| `resizeToHeight(int $height): static` | Proportional resize (by height) |
| `resizeToMax(int $maxSize): static` | Proportional resize by the longest side |

### Cropping

| Method | Description |
|--------|-------------|
| `crop(int $w, int $h, int $x = 0, int $y = 0): static` | Crop at specified position |
| `cropCenter(int $width, int $height): static` | Center crop |
| `thumbnail(int $width, int $height): static` | Thumbnail (resize + center crop) |

### Rotation & Flipping

| Method | Description |
|--------|-------------|
| `rotate(int $degrees, int $bgColor = 0): static` | Rotate image |
| `flipHorizontal(): static` | Flip horizontally (left-right mirror) |
| `flipVertical(): static` | Flip vertically (top-bottom mirror) |
| `flipBoth(): static` | Flip both directions |

### Watermarks

| Method | Description |
|--------|-------------|
| `imageWatermark(string\|ImageInterface $wm, int $x, int $y, int $opacity): static` | Image watermark (`$opacity` 0-100) |
| `textWatermark(string $text, string $font, int $size, int $x, int $y, int $opacity, int $color): static` | Text watermark (`$opacity` 0-100) |

### Saving & Output

| Method | Description |
|--------|-------------|
| `save(string $path, ?ImageType $type = null, int $quality = 90): bool` | Save to file (`$quality` 0-100) |
| `output(?ImageType $type = null, int $quality = 90): string` | Output as binary string (`$quality` 0-100) |

### Other

| Method | Description |
|--------|-------------|
| `info(): ImageInfo` | Get image info (width, height, type) |
| `resource(): \GdImage` | Get raw GD image object |
| `destroy(): void` | Releases the image (no-op on PHP 8.0+, where it is freed when released) |

## Core Classes

### ImageInfo (readonly value object)

```php
$info = $img->info();

$info->width;       // int
$info->height;      // int
$info->type;        // ImageType enum
$info->mime();      // string
$info->extension(); // string
$info->aspectRatio(); // float
$info->isSquare();  // bool
$info->isLandscape(); // bool
$info->isPortrait();  // bool
```

### ImageType Enum

```php
ImageType::JPEG;
ImageType::PNG;
ImageType::GIF;
ImageType::WEBP;

ImageType::fromExtension('jpg');
ImageType::fromMime('image/jpeg');
```

## Exceptions

All failures throw `MiGears\Image\Exception\ImageException`, including out-of-range parameters (`$quality` and `$opacity` must be between 0 and 100) and a declared type that contradicts the detected one.

## Testing

```bash
composer install
./vendor/bin/phpunit
```

## License

MIT

---

# migears/image

![Version](https://img.shields.io/badge/version-2.0.0-blue)

极简图像处理工具包，基于 PHP 8.1+ 和 GD 扩展。

## 特性

- **零强制依赖** — GD 扩展为建议依赖，可按需替换实现
- **极简 API** — 链式调用，直观易用
- **类型安全** — 全量类型声明、readonly 值对象、枚举
- **轻量级** — 核心类控制在几百行以内，可一口气读完
- **高测试覆盖** — 使用 GD 生成测试图片，覆盖所有核心功能

## 要求

- PHP 8.1+
- ext-gd（建议）

## 安装

```bash
composer require migears/image
```

## 快速开始

```php
use MiGears\Image\GDImage;
use MiGears\Image\ImageType;

// 从文件加载
$img = new GDImage('/path/to/image.jpg');

// 链式操作并保存
$img->resizeToWidth(800)
    ->cropCenter(400, 300)
    ->save('/path/to/output.jpg', ImageType::JPEG, 90);

// 生成缩略图（先缩放后裁剪，居中）
$img->thumbnail(200, 200)->save('/path/to/thumb.jpg');

// 添加图片水印
$img->imageWatermark('/path/to/watermark.png', 10, 10, 70)
    ->save('/path/to/wm.jpg');

// 添加文字水印
$img->textWatermark(
    text: 'Copyright',
    fontFile: '/path/to/font.ttf',
    fontSize: 14,
    x: 10,
    y: 10,
    opacity: 80,
    color: 0xFFFFFF,
)->save('/path/to/text-wm.jpg');

// 格式转换
$img->save('/path/to/output.webp', ImageType::WEBP);
```

### 旋转 & 翻转

```php
use MiGears\Image\GDImage;

$img = new GDImage('/path/to/photo.jpg');

// 顺时针旋转 90 度
$img->rotate(90)->save('/path/to/rotated.jpg');

// 带自定义背景色旋转（非 90° 角度时）
$img->rotate(45, 0xFFFFFF)->save('/path/to/tilted.jpg');

// 水平翻转（镜像效果）
$img->flipHorizontal()->save('/path/to/mirrored.jpg');

// 垂直翻转
$img->flipVertical()->save('/path/to/flipped.jpg');
```

### 直接输出到浏览器

```php
use MiGears\Image\GDImage;
use MiGears\Image\ImageType;

// 动态生成头像
$img = new GDImage('/path/to/avatar.png');
$img->resizeToWidth(128);

// 设置 Content-Type 并输出
header('Content-Type: ' . ImageType::PNG->mime());
echo $img->output(ImageType::PNG);
```

### 实战：用户上传缩略图生成器

```php
use MiGears\Image\GDImage;
use MiGears\Image\ImageType;

function generateThumbnails(string $sourcePath, string $outputDir): void
{
    $img = new GDImage($sourcePath);

    // 大图：宽 800px
    $img->resizeToWidth(800)
        ->save("{$outputDir}/large.jpg", ImageType::JPEG, 85);

    // 中图：宽 400px
    $img->resizeToWidth(400)
        ->save("{$outputDir}/medium.jpg", ImageType::JPEG, 80);

    // 方形缩略图：200x200（居中裁剪）
    $img->thumbnail(200, 200)
        ->save("{$outputDir}/thumb.jpg", ImageType::JPEG, 75);

    $img->destroy();
}
```

## API 概览

### 构造

```php
// 从文件路径 —— 类型由文件内容自动检测
new GDImage(string $filePath);

// 文件路径下 $type 为可选，仅用于确认检测结果；不一致时抛出 ImageException
new GDImage(string $filePath, ImageType $type);

// 从 GD 资源 —— 无文件可检测，$type 直接生效（省略时默认 PNG）
new GDImage(\GdImage $resource, ?ImageType $type = null);
```

### 信息

| 方法 | 说明 |
|------|------|
| `info(): ImageInfo` | 获取图片信息（宽、高、类型、mime、扩展名） |

### 缩放

| 方法 | 说明 |
|------|------|
| `resize(int $width, int $height): static` | 缩放到指定尺寸（可能变形） |
| `resizeToWidth(int $width): static` | 等比例缩放（按宽度） |
| `resizeToHeight(int $height): static` | 等比例缩放（按高度） |
| `resizeToMax(int $maxSize): static` | 按最大边等比例缩放 |

### 裁剪

| 方法 | 说明 |
|------|------|
| `crop(int $w, int $h, int $x = 0, int $y = 0): static` | 指定位置裁剪 |
| `cropCenter(int $width, int $height): static` | 居中裁剪 |
| `thumbnail(int $width, int $height): static` | 缩略图（缩放+居中裁剪） |

### 旋转 & 翻转

| 方法 | 说明 |
|------|------|
| `rotate(int $degrees, int $bgColor = 0): static` | 旋转图片 |
| `flipHorizontal(): static` | 水平翻转（左右镜像） |
| `flipVertical(): static` | 垂直翻转（上下镜像） |
| `flipBoth(): static` | 双向翻转 |

### 水印

| 方法 | 说明 |
|------|------|
| `imageWatermark(string\|ImageInterface $wm, int $x, int $y, int $opacity): static` | 图片水印（`$opacity` 0-100） |
| `textWatermark(string $text, string $font, int $size, int $x, int $y, int $opacity, int $color): static` | 文字水印（`$opacity` 0-100） |

### 保存 & 输出

| 方法 | 说明 |
|------|------|
| `save(string $path, ?ImageType $type = null, int $quality = 90): bool` | 保存到文件（`$quality` 0-100） |
| `output(?ImageType $type = null, int $quality = 90): string` | 输出二进制字符串（`$quality` 0-100） |

### 其他

| 方法 | 说明 |
|------|------|
| `info(): ImageInfo` | 获取图片信息（宽、高、类型） |
| `resource(): \GdImage` | 获取原始 GD 图像对象 |
| `destroy(): void` | 释放图像（PHP 8.0+ 下为空操作，对象释放时自动回收） |

## 核心类

### ImageInfo（readonly 值对象）

```php
$info = $img->info();

$info->width;       // int
$info->height;      // int
$info->type;        // ImageType 枚举
$info->mime();      // string
$info->extension(); // string
$info->aspectRatio(); // float
$info->isSquare();  // bool
$info->isLandscape(); // bool
$info->isPortrait();  // bool
```

### ImageType 枚举

```php
ImageType::JPEG;
ImageType::PNG;
ImageType::GIF;
ImageType::WEBP;

ImageType::fromExtension('jpg');
ImageType::fromMime('image/jpeg');
```

## 异常

所有失败均抛出 `MiGears\Image\Exception\ImageException`，包括参数越界（`$quality` 与 `$opacity` 必须在 0-100 之间）以及声明类型与检测类型不一致。

## 测试

```bash
composer install
./vendor/bin/phpunit
```

## License

MIT
