<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAudienceLandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_audience_landing_pages_render_with_course_help_context(): void
    {
        $pages = [
            [
                'route' => 'for-students',
                'headline' => 'Not sure which course to choose after school or college?',
                'source_page' => 'for_students',
                'audience_type' => 'student',
                'inquiry_intent' => 'course_selection_help',
            ],
            [
                'route' => 'for-parents',
                'headline' => 'Need clear course guidance for your child?',
                'source_page' => 'for_parents',
                'audience_type' => 'parent',
                'inquiry_intent' => 'parent_course_guidance',
            ],
            [
                'route' => 'study-abroad-guidance',
                'headline' => 'Planning to study abroad from Pokhara?',
                'source_page' => 'study_abroad_guidance',
                'audience_type' => 'study_abroad_applicant',
                'inquiry_intent' => 'study_abroad_course_guidance',
            ],
            [
                'route' => 'job-computer-skills',
                'headline' => 'Want practical computer or job-ready skills?',
                'source_page' => 'job_computer_skills',
                'audience_type' => 'job_skill_learner',
                'inquiry_intent' => 'computer_skill_guidance',
            ],
        ];

        foreach ($pages as $page) {
            $this->get(route($page['route']))
                ->assertOk()
                ->assertSee($page['headline'], false)
                ->assertSee('Ask for Course Help', false)
                ->assertSee('data-cta="audience-landing-course-help"', false)
                ->assertSee('data-source-page="'.$page['source_page'].'"', false)
                ->assertSee('data-audience-type="'.$page['audience_type'].'"', false)
                ->assertSee('data-inquiry-intent="'.$page['inquiry_intent'].'"', false)
                ->assertSee('source_page='.$page['source_page'], false)
                ->assertSee('audience_type='.$page['audience_type'], false)
                ->assertSee('inquiry_intent='.$page['inquiry_intent'], false)
                ->assertDontSee('Ask for Course Guidance', false);
        }
    }

    public function test_homepage_audience_cards_link_to_their_landing_pages(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('for-students'), false)
            ->assertSee(route('for-parents'), false)
            ->assertSee(route('study-abroad-guidance'), false)
            ->assertSee(route('job-computer-skills'), false)
            ->assertSee('audience-card-link-wrapper', false)
            ->assertSee('View Course Details', false)
            ->assertDontSee('href="'.route('join-now', ['source_section' => 'audience-student']), false);
    }

    public function test_mobile_sticky_navbar_contract_does_not_duplicate_navigation(): void
    {
        $this->seed(DatabaseSeeder::class);

        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'site-desktop-nav'));
        $this->assertSame(1, substr_count($html, 'site-mobile-nav'));
        $this->assertSame(1, substr_count($html, 'data-cta="navbar-course-help"'));
        $this->assertStringContainsString('Ask for Course Help', $html);

        $css = file_get_contents(public_path('site/css/style.css'));

        $this->assertStringContainsString('@media (max-width: 991.98px)', $css);
        $this->assertStringContainsString('position: sticky !important', $css);
        $this->assertStringContainsString('z-index: 1040', $css);
    }
}
