<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_renders_static_and_dynamic_urls_with_last_modified_dates(): void
    {
        $category = CourseCategory::factory()->create([
            'slug' => 'computer-classes',
            'status' => 'active',
        ]);

        Course::factory()->create([
            'slug' => 'professional-web-development',
            'status' => 'active',
            'category_id' => $category->id,
        ]);

        BlogPost::factory()->create([
            'slug' => 'career-guide',
            'status' => 'published',
        ]);

        cache()->forget('sitemap_xml');

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('home'), false)
            ->assertSee(route('for-students'), false)
            ->assertSee(route('for-parents'), false)
            ->assertSee(route('study-abroad-guidance'), false)
            ->assertSee(route('job-computer-skills'), false)
            ->assertSee(route('courses-detail', 'professional-web-development'), false)
            ->assertSee(route('courses-all'), false)
            ->assertSee(route('blog-detail', 'career-guide'), false)
            ->assertDontSee('courses-all?category=', false)
            ->assertDontSee('<loc>'.route('courses').'</loc>', false)
            ->assertDontSee('<loc>'.route('catalogue').'</loc>', false)
            ->assertSee('<lastmod>', false);
    }
}
