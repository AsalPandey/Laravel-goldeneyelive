<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogMetadataStaffTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_editor_exposes_safe_metadata_fields_with_guidance_but_not_schema(): void
    {
        $this->seed(RoleSeeder::class);
        $staff = User::factory()->create();
        $staff->assignRole('Staff');

        $this->actingAs($staff)
            ->get(route('admin.blog.create'))
            ->assertOk()
            ->assertSee('name="meta_title"', false)
            ->assertSee('maxlength="70"', false)
            ->assertSee('name="meta_description"', false)
            ->assertSee('maxlength="160"', false)
            ->assertSee('name="aeo_summary"', false)
            ->assertSee('maxlength="300"', false)
            ->assertSee('Leave fields empty to use the public fallback.', false)
            ->assertDontSee('name="schema_markup"', false);
    }

    public function test_staff_can_save_and_edit_ordinary_blog_metadata(): void
    {
        $this->seed(RoleSeeder::class);
        $staff = User::factory()->create();
        $staff->assignRole('Staff');

        $this->actingAs($staff)
            ->post(route('admin.blog.store'), [
                'title' => 'A Staff Managed Article',
                'content' => '<p>Visible factual article content.</p>',
                'status' => 'draft',
                'meta_title' => 'Staff Search Title',
                'meta_description' => 'A factual description for search previews.',
                'meta_keywords' => 'academy, course choice, Pokhara',
                'aeo_summary' => 'This article helps learners compare a course using visible facts.',
            ])
            ->assertRedirect(route('admin.blog.index'));

        $post = BlogPost::query()->where('title', 'A Staff Managed Article')->firstOrFail();
        $this->assertSame('Staff Search Title', $post->meta_title);
        $this->assertSame('A factual description for search previews.', $post->meta_description);
        $this->assertSame('This article helps learners compare a course using visible facts.', $post->aeo_summary);

        $this->actingAs($staff)
            ->get(route('admin.blog.edit', $post))
            ->assertOk()
            ->assertSee('value="Staff Search Title"', false)
            ->assertDontSee('name="schema_markup"', false);
    }

    public function test_blog_metadata_limits_return_clear_field_errors(): void
    {
        $this->seed(RoleSeeder::class);
        $staff = User::factory()->create();
        $staff->assignRole('Staff');

        $this->actingAs($staff)
            ->post(route('admin.blog.store'), [
                'title' => 'Metadata Limits',
                'content' => '<p>Visible factual article content.</p>',
                'status' => 'draft',
                'meta_title' => str_repeat('T', 71),
                'meta_description' => str_repeat('D', 161),
                'meta_keywords' => str_repeat('K', 501),
                'aeo_summary' => str_repeat('A', 301),
            ])
            ->assertSessionHasErrors(['meta_title', 'meta_description', 'meta_keywords', 'aeo_summary']);

        $this->assertDatabaseMissing(BlogPost::class, ['title' => 'Metadata Limits']);
    }

    public function test_empty_metadata_uses_existing_public_fallbacks(): void
    {
        $post = BlogPost::factory()->create([
            'title' => 'Fallback Article Title',
            'slug' => 'fallback-article-title',
            'content' => '<p>Visible article content provides the fallback description for this published guide.</p>',
            'status' => 'published',
            'published_at' => now()->subMinute(),
            'meta_title' => null,
            'meta_description' => null,
            'aeo_summary' => null,
        ]);

        $this->get(route('blog-detail', $post->slug))
            ->assertOk()
            ->assertSee('<title>Fallback Article Title - Golden Eye Academy</title>', false)
            ->assertSee('Visible article content provides the fallback description', false);
    }
}
