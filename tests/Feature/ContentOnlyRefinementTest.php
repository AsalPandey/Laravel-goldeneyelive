<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\FAQ;
use App\Models\SiteSetting;
use App\Support\ApprovedCourseFaqDeploymentData;
use Database\Seeders\LiveSiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ContentOnlyRefinementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_approved_course_facts_and_relationships_remain_intact(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $expectedCourses = [
            ...ApprovedCourseFaqDeploymentData::courseMetadata(),
            'free-course-roadmap-help' => [
                'name' => 'Free Course Roadmap Help',
                'instructor' => 'Academic Support Team',
                'price' => 'Free',
                'duration' => '30 Minutes',
            ],
        ];

        $this->assertCount(13, $expectedCourses);

        foreach ($expectedCourses as $slug => $expected) {
            $course = Course::query()->where('slug', $slug)->firstOrFail();

            foreach ($expected as $field => $value) {
                $this->assertSame($value, $course->{$field}, "{$slug} has an unexpected {$field}.");
            }
        }

        $this->assertSame('Rs. 14,000', Course::query()->where('slug', 'advanced-computer-diploma')->value('price'));
        $this->assertSame(68, DB::table('course_faq')->count());
        $this->assertSame(
            ApprovedCourseFaqDeploymentData::CERTIFICATE_ANSWER,
            FAQ::query()->where('question', ApprovedCourseFaqDeploymentData::CERTIFICATE_QUESTION)->value('answer'),
        );
    }

    public function test_public_copy_is_natural_and_keeps_each_cta_in_its_intended_context(): void
    {
        $this->seed(LiveSiteSeeder::class);
        cache()->flush();

        $routes = [
            'home',
            'courses-all',
            'for-students',
            'for-parents',
            'study-abroad-guidance',
            'job-computer-skills',
            'contact',
            'join-now',
            'faq',
            'blog',
        ];
        $html = '';

        foreach ($routes as $routeName) {
            $html .= $this->get(route($routeName))->assertOk()->getContent();
        }

        foreach (['ielts-masterclass', 'professional-web-development', 'jlpt-n5-elite', 'professional-korean-eps', 'advanced-computer-diploma'] as $slug) {
            $html .= $this->get(route('courses-detail', $slug))->assertOk()->getContent();
        }

        foreach (['Listed instructor', 'Current course details', 'Published outline', 'Published course outline', 'Listed learning areas', 'Outline item 1', 'Published student feedback', 'Course listed:', 'learnerâ€™s', 'academyâ€™s'] as $unwantedCopy) {
            $this->assertStringNotContainsString($unwantedCopy, $html);
        }

        $this->assertStringContainsString('Practical courses for your next step.', $html);
        $this->assertStringContainsString('What you’ll learn', $html);
        $this->assertStringContainsString('Send Inquiry', $html);
        $this->assertStringContainsString('Ask for Course Help', $html);
        $this->assertStringContainsString('Golden Eye Academy provides a certificate after completion of each course.', $html);
        $this->assertStringContainsString('does not guarantee a particular score', $html);
        $this->assertStringContainsString('does not guarantee a job or placement', $html);
        $this->assertStringContainsString('Neither service can guarantee a score, admission or visa outcome.', $html);
        $this->assertSame('https://www.tiktok.com/@goldeneye.academy', SiteSetting::getValue('tiktok_url'));
    }
}
