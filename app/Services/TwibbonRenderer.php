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
        // GD requires significant memory for large images
        $previousLimit = ini_get('memory_limit');
        ini_set('memory_limit', '512M');

        try {
            return self::doRender($content);
        } finally {
            ini_set('memory_limit', $previousLimit);
        }
    }

    private static function doRender(Content $content): string
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
        imagesavealpha($canvas, true);

        // Fill with white background (disable blending for clean fill)
        imagealphablending($canvas, false);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);

        // Re-enable alpha blending for compositing
        imagealphablending($canvas, true);

        // Render each slot: paste photo directly onto canvas
        // Template overlay on top will naturally clip/mask the photos
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

            $photoW = imagesx($photo);
            $photoH = imagesy($photo);
            $scale = (float) ($contentImg->scale ?: 1);

            // Scale photo to fill slot, then apply user scale
            $fitScale = max($slotW / $photoW, $slotH / $photoH);
            $totalScale = $fitScale * $scale;

            $scaledW = (int) round($photoW * $totalScale);
            $scaledH = (int) round($photoH * $totalScale);

            // Center in slot + user offset
            $destX = $slotX + (int) round(($slotW - $scaledW) / 2) + (int) round($contentImg->offset_x);
            $destY = $slotY + (int) round(($slotH - $scaledH) / 2) + (int) round($contentImg->offset_y);

            // Paste scaled photo directly onto canvas
            imagecopyresampled($canvas, $photo, $destX, $destY, 0, 0, $scaledW, $scaledH, $photoW, $photoH);
            imagedestroy($photo);
        }

        // Paste template overlay on top (PNG with transparency)
        // Opaque parts of template cover photo overflow, transparent parts show photos
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

        // Scale font size proportionally to canvas
        // Preview uses fontSize * 0.6 at ~400px width; GD uses points (1pt ≈ 1.33px)
        // So convert: pixelSize * 0.75 = pointSize
        $scaledSize = (int) round($fontSize * 0.6 * ($canvasW / 400) * 0.75);
        $scaledSize = max(1, $scaledSize);

        $rgb = self::hexToRgb($color);
        $textColor = imagecolorallocate($canvas, $rgb[0], $rgb[1], $rgb[2]);

        // Calculate base text position
        $x = (int) round($xPercent / 100 * $canvasW);
        $y = (int) round($yPercent / 100 * $canvasH);

        // Max text width (90% of canvas, matching CSS max-w-[90%])
        $maxTextWidth = (int) round($canvasW * 0.9);

        // Split text into lines (explicit newlines) then word-wrap long lines
        $rawLines = explode("\n", $text);
        $lines = [];
        foreach ($rawLines as $rawLine) {
            $rawLine = trim($rawLine, "\r");
            $wrapped = self::wordWrap($rawLine, $scaledSize, $fontPath, $maxTextWidth);
            foreach ($wrapped as $wl) {
                $lines[] = $wl;
            }
        }

        $lineHeight = (int) round($scaledSize * 1.8);

        // Calculate total block height to position at y
        $totalHeight = $lineHeight * count($lines);
        $startY = $y;

        foreach ($lines as $i => $line) {
            if ($line === '') continue;

            // Get text bounding box to center horizontally
            $bbox = imagettfbbox($scaledSize, 0, $fontPath, $line);
            $textWidth = abs($bbox[2] - $bbox[0]);
            $textHeight = abs($bbox[7] - $bbox[1]);

            // Center text at x position (translateX(-50%))
            $drawX = $x - (int) round($textWidth / 2);
            $drawY = $startY + ($i * $lineHeight) + $textHeight;

            imagettftext($canvas, $scaledSize, 0, $drawX, $drawY, $textColor, $fontPath, $line);

            // Underline
            if ($underline) {
                $lineYPos = $drawY + (int) round($scaledSize * 0.15);
                $thickness = max(1, (int) round($scaledSize / 15));
                imagesetthickness($canvas, $thickness);
                imageline($canvas, $drawX, $lineYPos, $drawX + $textWidth, $lineYPos, $textColor);
                imagesetthickness($canvas, 1);
            }
        }
    }

    /**
     * Word-wrap a single line of text to fit within maxWidth pixels.
     */
    private static function wordWrap(string $text, int $fontSize, string $fontPath, int $maxWidth): array
    {
        if ($text === '') return [''];

        // Check if the whole line fits
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
        $fullWidth = abs($bbox[2] - $bbox[0]);
        if ($fullWidth <= $maxWidth) {
            return [$text];
        }

        // Try word-based wrapping first
        $words = preg_split('/(\s+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine . $word;
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $testLine);
            $testWidth = abs($bbox[2] - $bbox[0]);

            if ($testWidth > $maxWidth && $currentLine !== '') {
                $lines[] = rtrim($currentLine);
                $currentLine = ltrim($word);
            } else {
                $currentLine = $testLine;
            }
        }
        if ($currentLine !== '') {
            $lines[] = rtrim($currentLine);
        }

        // If any line still exceeds max width (single long word), force-break by character
        $result = [];
        foreach ($lines as $line) {
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $line);
            if (abs($bbox[2] - $bbox[0]) <= $maxWidth) {
                $result[] = $line;
                continue;
            }
            $chars = mb_str_split($line);
            $cur = '';
            foreach ($chars as $char) {
                $test = $cur . $char;
                $bbox = imagettfbbox($fontSize, 0, $fontPath, $test);
                if (abs($bbox[2] - $bbox[0]) > $maxWidth && $cur !== '') {
                    $result[] = $cur;
                    $cur = $char;
                } else {
                    $cur = $test;
                }
            }
            if ($cur !== '') {
                $result[] = $cur;
            }
        }

        return $result;
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
