<?php

namespace App\Support;

use Illuminate\Support\Str;

final class CanonicalUrl
{
    public static function current(bool $includePage = true): string
    {
        $path = request()->getPathInfo();
        $query = [];

        if ($includePage && request()->integer('page') > 1) {
            $query['page'] = request()->integer('page');
        }

        return self::to($path, $query);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function route(string $name, array $parameters = []): string
    {
        return self::to(route($name, $parameters, false));
    }

    /**
     * @param  array<string, mixed>  $query
     */
    public static function to(string $path = '/', array $query = []): string
    {
        $baseUrl = rtrim((string) config('app.url', 'http://localhost'), '/');
        $parsedPath = parse_url($path, PHP_URL_PATH);
        $normalizedPath = '/'.ltrim(is_string($parsedPath) ? $parsedPath : '/', '/');
        $url = $baseUrl.($normalizedPath === '/' ? '' : $normalizedPath);

        if ($query !== []) {
            $url .= '?'.http_build_query($query);
        }

        return $url;
    }

    public static function normalize(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);

        return self::to(is_string($path) ? $path : '/');
    }

    public static function baseUrl(): string
    {
        return Str::finish(rtrim((string) config('app.url', 'http://localhost'), '/'), '/');
    }
}
