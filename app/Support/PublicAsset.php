<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PublicAsset
{
    public static function url(?string $path, string $fallback = 'site/img/carousel-1.png'): string
    {
        $resolvedPath = self::path($path, $fallback);

        if (Str::startsWith($resolvedPath, ['http://', 'https://', '//'])) {
            return $resolvedPath;
        }

        return asset($resolvedPath);
    }

    public static function path(?string $path, string $fallback = 'site/img/carousel-1.png'): string
    {
        $candidate = self::normalize($path);

        if ($candidate && self::exists($candidate)) {
            return $candidate;
        }

        $fallbackPath = self::normalize($fallback);

        if ($fallbackPath && self::exists($fallbackPath)) {
            return $fallbackPath;
        }

        return 'site/img/carousel-1.png';
    }

    protected static function normalize(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        $path = parse_url($path, PHP_URL_PATH) ?: $path;

        return ltrim(str_replace('\\', '/', $path), '/');
    }

    protected static function exists(string $path): bool
    {
        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return true;
        }

        $fullPath = public_path($path);

        return File::exists($fullPath) && File::size($fullPath) > 0;
    }
}
