<?php

namespace Tests\Feature\Admin;

use App\Models\BlogPost;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsSynchronizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_author_input_is_persisted_from_admin_cms(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->post(route('admin.blog.store'), [
            'title' => 'Career Planning Update',
            'slug' => 'career-planning-update',
            'author' => 'Golden Eye Editorial Desk',
            'category' => 'Academy News',
            'content' => '<p>Public blog content.</p>',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.blog.index'));

        $this->assertDatabaseHas(BlogPost::class, [
            'slug' => 'career-planning-update',
            'author' => 'Golden Eye Editorial Desk',
            'category' => 'Academy News',
            'status' => 'published',
        ]);
    }

    public function test_branding_whatsapp_fields_sync_to_public_cta(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->post(route('admin.branding.update'), [
            'whatsapp_number' => '9779800000000',
            'whatsapp_cta_text' => 'Message on WhatsApp',
            'whatsapp_cta_subtext' => '',
            'whatsapp_prefill_message' => 'Hi GoldenEye Academy, I have a quick question. Can you help me choose the right course?',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'whatsapp_cta_text',
            'value' => 'Message on WhatsApp',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Message on WhatsApp', false)
            ->assertDontSee('Message us on WhatsApp', false)
            ->assertDontSee('Casual questions. Quick reply.', false)
            ->assertSee('Hi%20Golden%20Eye%20Academy%2C%20I%20have%20a%20quick%20question.%20Can%20you%20help%20me%20choose%20the%20right%20course%3F', false);
    }
}
