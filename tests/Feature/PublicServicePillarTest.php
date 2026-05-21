<?php

namespace Tests\Feature;

use App\Models\ServicePillar;
use Database\Seeders\ServicePillarSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicServicePillarTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_uses_conversion_arc_instead_of_dense_service_pillar_copy(): void
    {
        $this->seed(ServicePillarSeeder::class);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Study Abroad, Language &amp; Computer Courses in Pokhara', false);
        $response->assertSee('Not sure which course to choose? Tell us your goal. We will help you compare study abroad, language, computer, and IT options before enrollment.');
        $response->assertSee('I am a Student');
        $response->assertSee('I am a Parent');
        $response->assertSee('I am planning Study Abroad');
        $response->assertSee('I want Job/Computer Skills');
        $response->assertDontSee("Don't just study. Build your competitive edge.");
        $response->assertDontSee('Learn Your Way: Digital-First Flexibility');
        $response->assertDontSee('The Network: Your Unfair Advantage');
        $response->assertDontSee('Career and College Blueprint: Zero Guesswork');
        $response->assertDontSee('Online When You Want: Premium, interactive virtual classrooms that fit your schedule. Learn from our experts anywhere, anytime.');
        $response->assertDontSee('Events: Learn, Meet, Move');
    }

    public function test_service_pillars_do_not_leak_into_homepage_conversion_flow(): void
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
        $response->assertSee('Study Abroad, Language &amp; Computer Courses in Pokhara', false);
        $response->assertSeeInOrder(['I am a Student', 'I am a Parent', 'I am planning Study Abroad', 'I want Job/Computer Skills']);
        $response->assertDontSee('First Pillar');
        $response->assertDontSee('Third Pillar');
        $response->assertDontSee('Hidden Pillar');
    }
}
