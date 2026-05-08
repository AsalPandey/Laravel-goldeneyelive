<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class RobotsController extends Controller
{
    public function __invoke(Request $request)
    {
        $robots = SiteSetting::getValue('robots_txt', "User-agent: *\nDisallow: /admin\nDisallow: /login\n\nSitemap: ".url('/sitemap.xml'));

        return response($robots, 200)->header('Content-Type', 'text/plain');
    }
}
