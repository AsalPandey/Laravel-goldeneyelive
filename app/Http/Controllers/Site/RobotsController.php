<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\CanonicalUrl;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $robots = trim((string) SiteSetting::getValue(
            'robots_txt',
            "User-agent: *\nDisallow: /admin\nDisallow: /login",
        ));
        $sitemapDirective = 'Sitemap: '.CanonicalUrl::route('sitemap');
        $robots = trim(preg_replace('/^Sitemap\s*:.*(?:\R|$)/mi', '', $robots) ?? $robots);
        $robots .= "\n\n".$sitemapDirective;

        return response($robots."\n", 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
