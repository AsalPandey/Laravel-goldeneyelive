<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Course;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = cache()->remember('sitemap_xml', 86400, function () {
            $courses = Course::publiclyVisible()->select('slug', 'updated_at')->get();
            $posts = BlogPost::where('status', 'published')->whereNotNull('slug')->select('slug', 'updated_at')->get();

            $entries = collect([
                route('home'),
                route('about'),
                route('for-students'),
                route('for-parents'),
                route('study-abroad-guidance'),
                route('job-computer-skills'),
                route('faq'),
                route('contact'),
                route('courses-all'),
                route('blog'),
                route('terms-and-conditions'),
                route('privacy-policy'),
            ])->map(fn (string $url): array => [
                'loc' => $url,
                'lastmod' => null,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ]);

            $courseEntries = $courses->map(fn (Course $course): array => [
                'loc' => route('courses-detail', $course->slug),
                'lastmod' => Carbon::parse($course->updated_at)->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ]);

            $postEntries = $posts->map(fn (BlogPost $post): array => [
                'loc' => route('blog-detail', $post->slug),
                'lastmod' => Carbon::parse($post->updated_at)->toW3cString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);

            $entries = $entries
                ->concat($courseEntries)
                ->concat($postEntries);

            return view('site.sitemap', compact('entries'))->render();
        });

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
