<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBlogVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_blog_with_category_shows_publicly(): void
    {
        BlogPost::factory()->create([
            'title' => 'Scholarship Planning Guide',
            'slug' => 'scholarship-planning-guide',
            'author' => 'GoldenEye Editorial Desk',
            'category' => 'Scholarships',
            'status' => 'published',
        ]);

        $response = $this->get('/blog');

        $response->assertStatus(200);
        $response->assertSee('Scholarship Planning Guide');
        $response->assertSee('Scholarships');
        $response->assertSee('Ask for Course Help');

        $this->get(route('blog-detail', 'scholarship-planning-guide'))
            ->assertOk()
            ->assertSee('GoldenEye Editorial Desk')
            ->assertSee('Scholarships');
    }

    public function test_draft_blog_stays_hidden_publicly(): void
    {
        BlogPost::factory()->draft()->create([
            'title' => 'Draft Launch Plan',
            'slug' => 'draft-launch-plan',
        ]);

        $response = $this->get('/blog');

        $response->assertStatus(200);
        $response->assertDontSee('Draft Launch Plan');
    }
}
