<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Course;
use App\Models\SiteSetting;
use App\Support\CanonicalUrl;
use App\Support\CmsPublicContent;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $cacheKey = 'sitemap_xml_'.hash('xxh128', CanonicalUrl::baseUrl());
        $renderSitemap = function (): string {
            $courses = Course::publiclyVisible()->select('slug', 'updated_at')->get();
            $posts = BlogPost::publiclyVisible()->select('slug', 'updated_at')->get();
            $settings = SiteSetting::pluck('value', 'key')->toArray();
            $settingsUpdatedAt = SiteSetting::max('updated_at');
            $staticLastModified = $settingsUpdatedAt
                ? Carbon::parse($settingsUpdatedAt)->toW3cString()
                : null;

            $entries = collect([
                'home',
                'about',
                'catalogue',
                'faq',
                'contact',
                'courses-all',
                'blog',
                'terms-and-conditions',
                'privacy-policy',
            ])->map(fn (string $url): array => [
                'loc' => CanonicalUrl::route($url),
                'lastmod' => $staticLastModified,
            ]);

            $audienceEntries = collect(CmsPublicContent::audiencePages($settings))
                ->filter(fn (array $page): bool => $page['is_active'])
                ->map(fn (array $page): array => [
                    'loc' => CanonicalUrl::route($page['route']),
                    'lastmod' => $staticLastModified,
                ]);

            $courseEntries = $courses->map(fn (Course $course): array => [
                'loc' => CanonicalUrl::route('courses-detail', ['slug' => $course->slug]),
                'lastmod' => Carbon::parse($course->updated_at)->toW3cString(),
            ]);

            $postEntries = $posts->map(fn (BlogPost $post): array => [
                'loc' => CanonicalUrl::route('blog-detail', ['slug' => $post->slug]),
                'lastmod' => Carbon::parse($post->updated_at)->toW3cString(),
            ]);

            $entries = $entries
                ->concat($audienceEntries)
                ->concat($courseEntries)
                ->concat($postEntries);

            return view('site.sitemap', compact('entries'))->render();
        };
        $xml = app()->environment('testing')
            ? $renderSitemap()
            : cache()->remember($cacheKey, 86400, $renderSitemap);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
