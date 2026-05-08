<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\JoinNowQuery;
use App\Models\SiteSetting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicCtaRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_sales_ctas_and_whatsapp_chat_prompt(): void
    {
        $this->seed(DatabaseSeeder::class);

        SiteSetting::where('key', 'whatsapp_number')->update(['value' => '+977 980-000-0000']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Ask for Course Help', false)
            ->assertSee('Message us on WhatsApp', false)
            ->assertSee('Casual questions. Quick reply.', false)
            ->assertSee('https://wa.me/9779800000000?text=', false)
            ->assertSee('data-cta="hero-course-help"', false)
            ->assertDontSee('data-cta="mobile-sticky-quick-help"', false)
            ->assertDontSee('Text us your goal.', false)
            ->assertSee('Start with the path that sounds like you.', false)
            ->assertSee('Students', false)
            ->assertSee('Study Abroad', false)
            ->assertSee('Job Seekers', false)
            ->assertSee('Parents', false)
            ->assertSee('Need something specific?', false)
            ->assertSee('Ask What Fits Me', false)
            ->assertSee('Popular courses', false)
            ->assertSee('Ask About This Course', false)
            ->assertDontSee('Book Free Counseling', false)
            ->assertDontSee('Free course guidance', false)
            ->assertDontSee('data-cta="homepage-fallback-course-counseling"', false)
            ->assertDontSee('data-cta="fallback-category-counseling"', false);
    }

    public function test_join_now_page_uses_guidance_first_conversion_copy(): void
    {
        Course::factory()->create([
            'name' => 'IELTS Masterclass',
            'slug' => 'ielts-masterclass',
            'status' => 'active',
        ]);

        $this->get(route('join-now', ['course' => 'ielts-masterclass']))
            ->assertOk()
            ->assertSee('Ask for Course Help', false)
            ->assertSee('Find the right course before you enroll', false)
            ->assertSee('Ask for Course Help', false)
            ->assertSee('I need help choosing the right program', false)
            ->assertSee('data-cta="join-now-form-submit"', false);
    }

    public function test_undecided_course_help_request_can_capture_a_lead(): void
    {
        Mail::fake();

        $this->withoutMiddleware();

        $this->from(route('join-now', ['course' => 'undecided']))
            ->post(route('join-now-submit'), [
                'firstName' => 'Asha',
                'lastName' => 'Sharma',
                'email' => 'asha@example.com',
                'phone' => '9800000000',
                'course' => 'undecided',
                'lead_source' => 'audience-students',
                'landing_page' => route('home'),
                'cta_id' => 'audience-students',
            ])
            ->assertRedirect(route('join-now', ['course' => 'undecided']));

        $this->assertDatabaseHas(JoinNowQuery::class, [
            'email' => 'asha@example.com',
            'course' => 'Need help choosing a program',
            'course_id' => null,
            'course_slug' => null,
            'lead_source' => 'audience-students',
            'cta_id' => 'audience-students',
        ]);
    }
}
