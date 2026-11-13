<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Stores uploads under public/uploads (works on shared hosting without storage:link).
 * Raster images are resized, re-encoded to WebP and given a 640px "-sm" variant.
 */
class Uploader
{
    public const MAX_WIDTH = 1920;

    public const SMALL_WIDTH = 640;

    public static function store(UploadedFile $file, string $dir): string
    {
        $dir = trim($dir, '/').'/'.now()->format('Y/m');
        $absDir = public_path('uploads/'.$dir);
        File::ensureDirectoryExists($absDir);

        $mime = $file->getMimeType();
        $name = Str::random(24);

        if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true) && function_exists('imagewebp')) {
            $path = $dir.'/'.$name.'.webp';
            if (self::processImage($file->getRealPath(), $mime, public_path('uploads/'.$path))) {
                return $path;
            }
        }

        // Non-images (PDF, SVG logos, …) and anything GD can't handle: store as-is with a safe extension.
        $ext = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'bin';
        $file->move($absDir, $name.'.'.$ext);

        return $dir.'/'.$name.'.'.$ext;
    }

    /** Private files (e.g. CVs) live outside the web root and are served only through the dashboard. */
    public static function storePrivate(UploadedFile $file, string $dir): string
    {
        $ext = preg_replace('/[^a-z0-9]/', '', strtolower($file->guessExtension() ?: 'bin'));

        return $file->storeAs(trim($dir, '/').'/'.now()->format('Y/m'), Str::random(32).'.'.$ext, 'local');
    }

    public static function delete(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http') || str_starts_with($path, 'seed/')) {
            return;
        }
        $abs = public_path('uploads/'.$path);
        File::delete([$abs, preg_replace('/\.(\w+)$/', '-sm.$1', $abs)]);
    }

    /** Resize + convert to WebP. Returns false if GD fails so the caller can fall back. */
    public static function processImage(string $source, string $mime, string $target): bool
    {
        try {
            $img = match ($mime) {
                'image/jpeg' => @imagecreatefromjpeg($source),
                'image/png' => @imagecreatefrompng($source),
                'image/webp' => @imagecreatefromwebp($source),
                default => false,
            };
            if (! $img) {
                return false;
            }
            if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
                $img = self::orient($img, @exif_read_data($source) ?: []);
            }
            imagepalettetotruecolor($img);
            imagealphablending($img, true);
            imagesavealpha($img, true);

            self::saveResized($img, $target, self::MAX_WIDTH, 82);
            self::saveResized($img, preg_replace('/\.webp$/', '-sm.webp', $target), self::SMALL_WIDTH, 78);
            imagedestroy($img);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private static function saveResized(\GdImage $img, string $target, int $maxWidth, int $quality): void
    {
        $w = imagesx($img);
        $h = imagesy($img);
        if ($w > $maxWidth) {
            $nh = (int) round($h * $maxWidth / $w);
            $res = imagecreatetruecolor($maxWidth, $nh);
            imagealphablending($res, false);
            imagesavealpha($res, true);
            imagecopyresampled($res, $img, 0, 0, 0, 0, $maxWidth, $nh, $w, $h);
            imagewebp($res, $target, $quality);
            imagedestroy($res);
        } else {
            imagewebp($img, $target, $quality);
        }
    }

    private static function orient(\GdImage $img, array $exif): \GdImage
    {
        return match ((int) ($exif['Orientation'] ?? 1)) {
            3 => imagerotate($img, 180, 0),
            6 => imagerotate($img, -90, 0),
            8 => imagerotate($img, 90, 0),
            default => $img,
        };
    }
}
