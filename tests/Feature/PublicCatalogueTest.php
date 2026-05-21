<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogueTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalogue_renders_seeded_services_categories_courses_and_conversion_ctas(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(url('/catelogue'), route('catalogue'));

        $this->get('/catelogue')
            ->assertOk()
            ->assertSee('Full Catalogue', false)
            ->assertSee('Service Catalogue', false)
            ->assertSee('Course Catalogue', false)
            ->assertSee('Flexible Learning Support', false)
            ->assertSee('Study Abroad Test Prep', false)
            ->assertSee('IELTS Preparation for Band 7 Goal', false)
            ->assertSee('Message on WhatsApp', false)
            ->assertSee('Ask for Course Help', false)
            ->assertSee('View Course Details', false)
            ->assertDontSee('Ask What Fits Me', false);

        $this->get('/catalogue')
            ->assertStatus(301)
            ->assertRedirect(route('catalogue'));
    }

    public function test_catalogue_is_hidden_from_homepage_navigation_and_footer(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('/catelogue', false)
            ->assertDontSee('Full Catalogue', false)
            ->assertDontSee('Guided Catalogue', false);
    }
}
