<?php

namespace App\Services;

use App\Models\Content;
use Illuminate\Support\Facades\Storage;

class TwibbonRenderer
{
    private static array $fontMap = [
        'Arial' => 'Arial',
        'Verdana' => 'Verdana',
        'Helvetica' => 'Arial', // fallback
        'Times New Roman' => 'TimesNewRoman',
        'Georgia' => 'Georgia',
        'Courier New' => 'CourierNew',
        'Trebuchet MS' => 'TrebuchetMS',
        'Impact' => 'Impact',
    ];

    public static function render(Content $content): string
    {
        $content->loadMissing(['template.slots', 'images']);

        $templatePath = Storage::disk('public')->path($content->template->image);
        $templateImg = self::loadImage($templatePath);
        if (!$templateImg) {
            throw new \RuntimeException('Cannot load template image.');
        }

        $width = imagesx($templateImg);
        $height = imagesy($templateImg);

        // Create canvas
        $canvas = imagecreatetruecolor($width, $height);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        // Fill with white background
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);

        // Render each slot with user photo
        foreach ($content->template->slots as $slot) {
            $contentImg = $content->images->where('slot_number', $slot->slot_number)->first();
            if (!$contentImg) continue;

            $photoPath = Storage::disk('public')->path($contentImg->image);
            $photo = self::loadImage($photoPath);
            if (!$photo) continue;

            $slotX = (int) round($slot->x_percent / 100 * $width);
            $slotY = (int) round($slot->y_percent / 100 * $height);
            $slotW = (int) round($slot->width_percent / 100 * $width);
            $slotH = (int) round($slot->height_percent / 100 * $height);

            if ($slotW <= 0 || $slotH <= 0) continue;

            // Apply scale and offset
            $photoW = imagesx($photo);
            $photoH = imagesy($photo);
            $scale = (float) ($contentImg->scale ?: 1);

            // Scale photo to fill slot, then apply user scale
            $fitScale = max($slotW / $photoW, $slotH / $photoH);
            $totalScale = $fitScale * $scale;

            $scaledW = (int) round($photoW * $totalScale);
            $scaledH = (int) round($photoH * $totalScale);

            $scaledPhoto = imagecreatetruecolor($scaledW, $scaledH);
            imagealphablending($scaledPhoto, true);
            imagesavealpha($scaledPhoto, true);
            imagecopyresampled($scaledPhoto, $photo, 0, 0, 0, 0, $scaledW, $scaledH, $photoW, $photoH);
            imagedestroy($photo);

            // Center photo in slot, then apply offset
            $centerOffsetX = (int) round(($slotW - $scaledW) / 2);
            $centerOffsetY = (int) round(($slotH - $scaledH) / 2);

            $offsetX = $centerOffsetX + (int) round($contentImg->offset_x);
            $offsetY = $centerOffsetY + (int) round($contentImg->offset_y);

            // Create clipped slot
            $slotCanvas = imagecreatetruecolor($slotW, $slotH);
            imagealphablending($slotCanvas, true);
            imagesavealpha($slotCanvas, true);
            $transparent = imagecolorallocatealpha($slotCanvas, 0, 0, 0, 127);
            imagefill($slotCanvas, 0, 0, $transparent);

            imagecopy($slotCanvas, $scaledPhoto, $offsetX, $offsetY, 0, 0, $scaledW, $scaledH);
            imagedestroy($scaledPhoto);

            // Paste slot onto canvas
            imagecopy($canvas, $slotCanvas, $slotX, $slotY, 0, 0, $slotW, $slotH);
            imagedestroy($slotCanvas);
        }

        // Paste template overlay (with alpha)
        imagecopy($canvas, $templateImg, 0, 0, 0, 0, $width, $height);
        imagedestroy($templateImg);

        // Render title text
        if ($content->title) {
            self::renderText(
                $canvas, $width, $height,
                $content->title,
                $content->title_x_percent,
                $content->title_y_percent,
                $content->title_font_family ?? 'Arial',
                $content->title_font_size ?? 24,
                $content->title_font_bold,
                $content->title_font_italic,
                $content->title_font_underline,
                $content->title_font_color ?? '#000000'
            );
        }

        // Render caption text
        if ($content->caption) {
            self::renderText(
                $canvas, $width, $height,
                $content->caption,
                $content->caption_x_percent,
                $content->caption_y_percent,
                $content->caption_font_family ?? 'Arial',
                $content->caption_font_size ?? 16,
                $content->caption_font_bold,
                $content->caption_font_italic,
                $content->caption_font_underline,
                $content->caption_font_color ?? '#000000'
            );
        }

        // Save to storage
        $dir = 'finals';
        Storage::disk('public')->makeDirectory($dir);
        $filename = "{$dir}/{$content->id}.png";
        $savePath = Storage::disk('public')->path($filename);

        imagepng($canvas, $savePath);
        imagedestroy($canvas);

        return $filename;
    }

    private static function renderText(
        \GdImage $canvas, int $canvasW, int $canvasH,
        string $text, float $xPercent, float $yPercent,
        string $fontFamily, int $fontSize, bool $bold, bool $italic, bool $underline, string $color
    ): void {
        $fontPath = self::getFontPath($fontFamily, $bold, $italic);

        // Scale font size proportionally to canvas (base reference: 400px preview)
        $scaledSize = (int) round($fontSize * ($canvasW / 400));

        $rgb = self::hexToRgb($color);
        $textColor = imagecolorallocate($canvas, $rgb[0], $rgb[1], $rgb[2]);

        // Calculate text position
        $x = (int) round($xPercent / 100 * $canvasW);
        $y = (int) round($yPercent / 100 * $canvasH);

        // Get text bounding box to center horizontally
        $bbox = imagettfbbox($scaledSize, 0, $fontPath, $text);
        $textWidth = abs($bbox[2] - $bbox[0]);
        $textHeight = abs($bbox[7] - $bbox[1]);

        // Center text at x position (translateX(-50%))
        $drawX = $x - (int) round($textWidth / 2);
        $drawY = $y + (int) round($textHeight / 2);

        imagettftext($canvas, $scaledSize, 0, $drawX, $drawY, $textColor, $fontPath, $text);

        // Underline
        if ($underline) {
            $lineY = $drawY + (int) round($scaledSize * 0.15);
            $thickness = max(1, (int) round($scaledSize / 15));
            imagesetthickness($canvas, $thickness);
            imageline($canvas, $drawX, $lineY, $drawX + $textWidth, $lineY, $textColor);
            imagesetthickness($canvas, 1);
        }
    }

    private static function getFontPath(string $family, bool $bold, bool $italic): string
    {
        $base = self::$fontMap[$family] ?? 'Arial';
        $fontsDir = storage_path('app/fonts');

        $suffix = '';
        if ($bold && $italic) {
            $suffix = '-BoldItalic';
        } elseif ($bold) {
            $suffix = '-Bold';
        } elseif ($italic) {
            $suffix = '-Italic';
        }

        $path = "{$fontsDir}/{$base}{$suffix}.ttf";
        if (file_exists($path)) {
            return $path;
        }

        // Fallback to regular variant
        $regularPath = "{$fontsDir}/{$base}.ttf";
        if (file_exists($regularPath)) {
            return $regularPath;
        }

        // Ultimate fallback to Arial
        return "{$fontsDir}/Arial.ttf";
    }

    private static function loadImage(string $path): ?\GdImage
    {
        if (!file_exists($path)) return null;

        $info = getimagesize($path);
        if (!$info) return null;

        return match ($info[2]) {
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            default => null,
        };
    }

    private static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
