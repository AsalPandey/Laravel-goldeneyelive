<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use Database\Seeders\BlogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase7WordingCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_guide_uses_academy_wording_and_preserves_legacy_identity(): void
    {
        $this->seed(BlogSeeder::class);

        $post = BlogPost::query()
            ->where('slug', 'parents-guide-how-to-evaluate-a-training-institute')
            ->firstOrFail();

        $this->assertSame('A Parent’s Checklist for Choosing an Academy', $post->title);
        $this->assertSame('published', $post->status);
        $this->assertStringContainsString('academy', strtolower($post->content));
        $this->assertStringNotContainsString('training institute', strtolower($post->title));
        $this->assertStringNotContainsString('training institute', strtolower($post->content));

        $this->get(route('blog-detail', $post->slug))
            ->assertOk()
            ->assertSeeText('A Parent’s Checklist for Choosing an Academy')
            ->assertDontSeeText('Training Institute')
            ->assertSee('href="'.route('blog-detail', $post->slug).'"', false);
    }
}
