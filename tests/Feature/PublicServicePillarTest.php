<?php

namespace Tests\Feature;

use App\Models\ServicePillar;
use Database\Seeders\ServicePillarSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicServicePillarTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_compact_hook_and_seeded_service_pillars(): void
    {
        $this->seed(ServicePillarSeeder::class);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee("Don't just study. Build your competitive edge.");
        $response->assertSee('Confused about what to study next? Message GoldenEye Academy with your goal.');
        $response->assertSee('Learn Your Way: Digital-First Flexibility');
        $response->assertSee('The Network: Your Unfair Advantage');
        $response->assertSee('Career and College Blueprint: Zero Guesswork');
        $response->assertSee('Flexible online learning with in-person support when students need focus and collaboration.');
        $response->assertSee('Corporate access, industry exposure, and community events that help students move faster.');
        $response->assertDontSee('Online When You Want: Premium, interactive virtual classrooms that fit your schedule. Learn from our experts anywhere, anytime.');
        $response->assertDontSee('Events: Learn, Meet, Move');
    }

    public function test_service_pillars_render_in_admin_defined_order_and_only_when_active(): void
    {
        ServicePillar::factory()->create([
            'title' => 'Third Pillar',
            'slug' => 'third-pillar',
            'sort_order' => 30,
        ]);

        ServicePillar::factory()->create([
            'title' => 'First Pillar',
            'slug' => 'first-pillar',
            'sort_order' => 10,
        ]);

        ServicePillar::factory()->create([
            'title' => 'Hidden Pillar',
            'slug' => 'hidden-pillar',
            'status' => 'inactive',
            'sort_order' => 1,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeInOrder(['First Pillar', 'Third Pillar']);
        $response->assertDontSee('Hidden Pillar');
    }
}
