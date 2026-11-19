<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Right-sized copies of uploaded images, so a 80 px category circle or a
 * 300 px card never downloads a 1000 px photo.
 *
 * Copies live on the public disk under thumbs/{width}/{original path}.webp.
 * They are made when an image is saved in the admin (see AppServiceProvider),
 * by `php artisan images:thumbs`, and as a fallback the first time a page
 * asks for one.
 */
class Thumbnails
{
    /** Widths that are generated. Slots pick the smallest one that is large enough. */
    public const WIDTHS = [160, 400, 800, 1200];

    private const QUALITY = 78;

    /** Images bigger than this (in pixels) are never decoded: it could exhaust server memory. */
    private const MAX_PIXELS = 40_000_000;

    /**
     * How many copies a single page view may create on the fly. Normally copies already
     * exist (made on upload); this only matters right after a bulk import, and keeps such
     * a page view fast; remaining copies are made on the next views.
     */
    private const MAX_PER_REQUEST = 4;

    private static int $madeThisRequest = 0;

    /** Copies made by commands/background jobs are not limited. */
    private static bool $unlimited = false;

    /** Called at the start of each request (see AppServiceProvider). */
    public static function resetRequestBudget(): void
    {
        static::$madeThisRequest = 0;
    }

    /** URL of a copy at least $width px wide (or the original, if it is smaller). */
    public static function url(?string $path, int $width): ?string
    {
        if (! static::isLocal($path)) {
            return media_url($path);
        }

        $size = static::pick($width);

        return static::ensure($path, $size) ? media_url(static::thumbPath($path, $size)) : media_url($path);
    }

    /**
     * src + srcset for an image shown $displayWidth CSS pixels wide:
     * a 1× copy and a sharper 2× copy for high-density screens.
     *
     * @return array{src: ?string, srcset: ?string}
     */
    public static function sources(?string $path, int $displayWidth): array
    {
        if (! static::isLocal($path)) {
            return ['src' => media_url($path), 'srcset' => null];
        }

        $one = static::url($path, $displayWidth);
        $two = static::url($path, $displayWidth * 2);

        return ['src' => $one, 'srcset' => $one === $two ? null : "{$one} 1x, {$two} 2x"];
    }

    /** Make every size for one image. Returns how many copies were written. */
    public static function generateAll(?string $path): int
    {
        if (! static::isLocal($path)) {
            return 0;
        }

        $made = 0;
        static::$unlimited = true;

        try {
            foreach (self::WIDTHS as $width) {
                $existed = Storage::disk('public')->exists(static::thumbPath($path, $width));
                if (! $existed && static::ensure($path, $width)) {
                    $made++;
                }
            }
        } finally {
            static::$unlimited = false;
        }

        return $made;
    }

    public static function thumbPath(string $path, int $width): string
    {
        return 'thumbs/'.$width.'/'.preg_replace('/\.[a-z0-9]+$/i', '', ltrim($path, '/')).'.webp';
    }

    /** Raise PHP's memory limit to at least $bytes above current use (never lowers it). */
    private static function raiseMemoryLimit(int $bytes): void
    {
        $limit = ini_get('memory_limit');
        if ($limit === '-1') {
            return;
        }

        $current = (int) $limit * match (strtolower(substr($limit, -1))) {
            'g' => 1024 ** 3, 'm' => 1024 ** 2, 'k' => 1024, default => 1
        };
        $needed = memory_get_usage(true) + $bytes;

        if ($needed > $current) {
            @ini_set('memory_limit', (string) min($needed, 1024 ** 3)); // at most 1 GB
        }
    }

    private static function pick(int $width): int
    {
        foreach (self::WIDTHS as $size) {
            if ($size >= $width) {
                return $size;
            }
        }

        return self::WIDTHS[array_key_last(self::WIDTHS)]; // the largest size
    }

    private static function isLocal(?string $path): bool
    {
        return filled($path) && ! preg_match('#^(https?:)?//|^/#i', $path);
    }

    /**
     * Make sure the copy exists. True when a usable copy is there; false when the
     * original should be served instead (missing, unreadable, or already small).
     */
    private static function ensure(string $path, int $width): bool
    {
        $disk = Storage::disk('public');
        $target = static::thumbPath($path, $width);

        if ($disk->exists($target)) {
            return true;
        }

        $source = $disk->path($path);
        if (! is_file($source)) {
            return false;
        }

        try {
            [$originalWidth, $originalHeight] = getimagesize($source) ?: [0, 0];
            if ($originalWidth === 0 || $originalWidth <= $width) {
                return false; // already small enough: serve the original
            }
            if ($originalWidth * $originalHeight > self::MAX_PIXELS) {
                Log::warning('Image too large to resize safely', ['path' => $path, 'size' => "{$originalWidth}x{$originalHeight}"]);

                return false;
            }
            if (! static::$unlimited && static::$madeThisRequest >= self::MAX_PER_REQUEST) {
                return false; // made on a later page view instead
            }

            // Decoding needs ~5 bytes per pixel (source + resized copy): make sure there is room.
            static::raiseMemoryLimit((int) ($originalWidth * $originalHeight * 6) + 64 * 1024 * 1024);

            $image = imagecreatefromstring(file_get_contents($source));
            if (! $image) {
                return false;
            }

            imagepalettetotruecolor($image);
            $resized = imagescale($image, $width, -1, IMG_BICUBIC);
            unset($image); // free the full-size image straight away
            imagealphablending($resized, false);
            imagesavealpha($resized, true);

            // Write to a temporary name first so a half-written file is never served.
            $absolute = $disk->path($target);
            @mkdir(dirname($absolute), 0775, true);
            $temp = $absolute.'.'.bin2hex(random_bytes(4)).'.tmp';
            imagewebp($resized, $temp, self::QUALITY);
            unset($resized);
            rename($temp, $absolute);

            static::$madeThisRequest++;

            return true;
        } catch (Throwable $e) {
            Log::warning('Thumbnail could not be made', ['path' => $path, 'width' => $width, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
