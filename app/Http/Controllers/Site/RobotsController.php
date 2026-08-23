<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Support\CanonicalUrl;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        return response(self::policy(), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public static function policy(): string
    {
        return implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /dashboard',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            '',
            'Sitemap: '.CanonicalUrl::route('sitemap'),
            '',
        ]);
    }
}
