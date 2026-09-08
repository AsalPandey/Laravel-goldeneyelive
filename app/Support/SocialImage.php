<?php

namespace App\Support;

final class SocialImage
{
    /**
     * @return array{url: string, type: string, width: int, height: int}
     */
    public static function resolve(?string ...$candidates): array
    {
        foreach ([...$candidates, 'site/img/carousel-1.png'] as $candidate) {
            $host = parse_url($candidate ?? '', PHP_URL_HOST);

            if ($host && $host !== parse_url(config('app.url'), PHP_URL_HOST)) {
                continue;
            }

            $path = ltrim((string) parse_url($candidate ?? '', PHP_URL_PATH), '/');
            $root = realpath(public_path());
            $file = realpath(public_path($path));

            if (! $root || ! $file || ! str_starts_with($file, $root.DIRECTORY_SEPARATOR)
                || ! is_file($file) || in_array(basename($file), ['favicon.ico', 'favicon.svg', 'apple-touch-icon.png'], true)) {
                continue;
            }

            $size = @getimagesize($file);

            if (! $size || ! in_array($size['mime'], ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true)) {
                continue;
            }

            return [
                'url' => preg_replace('/^http:/', 'https:', CanonicalUrl::to('/'.$path)).'?v='.substr(hash_file('sha256', $file), 0, 12),
                'type' => $size['mime'],
                'width' => $size[0],
                'height' => $size[1],
            ];
        }

        throw new \RuntimeException('The Golden Eye social image is missing or invalid.');
    }
}
