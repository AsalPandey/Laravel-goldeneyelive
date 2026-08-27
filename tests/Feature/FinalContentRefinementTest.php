<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\ServicePillar;
use App\Support\ApprovedCourseFaqDeploymentData;
use Database\Seeders\LiveSiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalContentRefinementTest extends TestCase
{
    use RefreshDatabase;

    public function test_fresh_baseline_excludes_sat_and_unsupported_public_claims(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $publicCopy = collect([
            ...CourseCategory::query()->get(['description', 'meta_title', 'meta_description', 'aeo_summary'])->toArray(),
            ...Course::query()->get(['description', 'course_outline', 'meta_title', 'meta_description', 'aeo_summary'])->toArray(),
            ...FAQ::query()->get(['question', 'answer', 'meta_title', 'meta_description', 'aeo_summary'])->toArray(),
            ...ServicePillar::query()->get(['title', 'summary', 'bullets', 'meta_title', 'meta_description', 'aeo_summary'])->toArray(),
            ...BlogPost::query()->get(['title', 'content', 'meta_title', 'meta_description', 'aeo_summary'])->toArray(),
        ])->flatten()->filter(fn (mixed $value): bool => is_scalar($value))->implode(' ');

        $this->assertDoesNotMatchRegularExpression('/\bSAT\b/i', $publicCopy);
        $this->assertDoesNotMatchRegularExpression('/job placement|employment guarantee|employer partnership|internship guarantee|job-ready/i', $publicCopy);
    }

    public function test_verified_certificate_and_web_course_facts_are_published_in_relevant_content(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $this->assertSame(
            ApprovedCourseFaqDeploymentData::CERTIFICATE_ANSWER,
            FAQ::query()->findOrFail(10)->answer,
        );

        $webDescription = Course::query()->where('slug', 'professional-web-development')->value('description');
        foreach (['practical project work', 'Laravel', 'API integration', 'deployment concepts'] as $fact) {
            $this->assertStringContainsString($fact, $webDescription);
        }

        $officeDescription = Course::query()->where('slug', 'advanced-computer-diploma')->value('description');
        $this->assertStringNotContainsString('Laravel', $officeDescription);
        $this->assertStringNotContainsString('API', $officeDescription);
        $this->assertStringNotContainsString('deployment', $officeDescription);
    }

    public function test_course_hero_uses_a_complete_summary_and_only_explicit_best_for_copy(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $course = Course::query()->where('slug', 'professional-web-development')->firstOrFail();
        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('Build web-development foundations through practical project work covering HTML, CSS, responsive interfaces, Laravel, databases, API integration and deployment concepts.', false)
            ->assertDontSee('Best for:', false);

        $course->update(['description' => 'Build strong web foundations through guided project work. Best for learners who already use a computer confidently.']);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('Build strong web foundations through guided project work', false)
            ->assertSee('Best for: Learners who already use a computer confidently', false);
    }

    public function test_catalogue_and_course_search_use_destination_accurate_ctas(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $catalogue = $this->get(route('catalogue'));
        $catalogue->assertOk()
            ->assertSee('View Course Details', false)
            ->assertDontSee('data-cta="catalogue-service-guidance"', false);
        $this->assertSame(1, substr_count($catalogue->getContent(), 'data-cta="catalogue-course-guidance"'));

        $this->get(route('courses-all'))
            ->assertOk()
            ->assertSee('data-cta-label="Apply Filters"', false)
            ->assertSee('>Apply Filters</button>', false)
            ->assertDontSee('data-cta="popular-course-guidance"', false)
            ->assertDontSee('data-cta="course-card-course-guidance"', false);
    }

    public function test_ielts_pte_article_compares_stable_learner_facing_differences(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $article = BlogPost::query()->where('slug', 'ielts-or-pte-how-to-choose-the-right-test')->firstOrFail();
        $this->assertSame('published', $article->status);
        $this->assertSame('Golden Eye Academy', $article->author);

        foreach (['live conversation with an examiner', 'recorded through a microphone', 'Typing comfort', 'rather than popularity', "organization's current requirement"] as $copy) {
            $this->assertStringContainsString($copy, $article->content);
        }
    }
}
