<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\BlogSeeder;
use Database\Seeders\CourseCategorySeeder;
use Database\Seeders\CourseSeeder;
use Database\Seeders\FAQSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ServicePillarSeeder;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase6VerifiedContentTrustTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        cache()->flush();
        $this->seed(RoleSeeder::class);

        $this->staff = User::factory()->create();
        $this->staff->assignRole('Staff');
    }

    public function test_staff_can_manage_safe_batch_and_course_confirmation_copy_end_to_end(): void
    {
        $course = Course::factory()->create([
            'name' => 'CMS Confirmation Course',
            'slug' => 'cms-confirmation-course',
            'status' => 'active',
        ]);

        $this->actingAs($this->staff)
            ->get(route('admin.branding.index'))
            ->assertOk()
            ->assertSee('name="home_courses_batch_note"', false)
            ->assertSee('name="course_confirmation_note"', false);

        $values = [
            'home_courses_batch_note' => 'CMS6 confirm the current homepage batch',
            'course_confirmation_note' => 'CMS6 confirm schedule, availability, and instructor',
        ];

        $this->actingAs($this->staff)
            ->post(route('admin.branding.update'), $values)
            ->assertRedirect();

        foreach ($values as $key => $value) {
            $this->assertDatabaseHas(SiteSetting::class, [
                'key' => $key,
                'value' => $value,
            ]);
        }

        cache()->flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText($values['home_courses_batch_note']);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSeeText($values['course_confirmation_note']);

        $this->actingAs($this->staff)
            ->post(route('admin.branding.update'), [
                'home_courses_batch_note' => str_repeat('a', 1001),
                'course_confirmation_note' => str_repeat('b', 501),
            ])
            ->assertSessionHasErrors([
                'home_courses_batch_note',
                'course_confirmation_note',
            ]);
    }

    public function test_review_sections_require_actual_external_evidence(): void
    {
        $course = Course::factory()->create([
            'name' => 'External Evidence Course',
            'slug' => 'external-evidence-course',
            'status' => 'active',
        ]);

        SiteSetting::query()->create([
            'key' => 'external_review_proof_note',
            'value' => 'A note without linked evidence must stay hidden.',
            'type' => 'text',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Verified external reviews')
            ->assertDontSee('A note without linked evidence must stay hidden.');

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertDontSee('Verified external reviews')
            ->assertDontSee('A note without linked evidence must stay hidden.');

        SiteSetting::query()->create([
            'key' => 'google_business_profile_url',
            'value' => 'https://example.com/verified-academy-profile',
            'type' => 'text',
        ]);
        cache()->flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Verified external reviews')
            ->assertSee('A note without linked evidence must stay hidden.')
            ->assertSee('https://example.com/verified-academy-profile', false);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('Verified external reviews')
            ->assertSee('A note without linked evidence must stay hidden.')
            ->assertSee('https://example.com/verified-academy-profile', false);
    }

    public function test_safe_brand_defaults_do_not_publish_unverified_experience_claims(): void
    {
        $this->seed(SiteSettingSeeder::class);

        $this->assertSame(
            'Golden Eye Academy | Courses and Classes in Pokhara',
            SiteSetting::getValue('meta_title'),
        );
        $this->assertSame(
            'Golden Eye Academy in Pokhara provides IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT classes.',
            SiteSetting::getValue('aeo_summary'),
        );
        $this->assertSame('Pokhara, Nepal', SiteSetting::getValue('logo_subtitle'));
        $this->assertSame('', SiteSetting::getValue('youtube_url'));

        $response = $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('since 2008', false)
            ->assertDontSee('Established in 2008', false)
            ->assertDontSee('Est. 2008', false)
            ->assertDontSee('foundingDate', false);

        $this->assertStringContainsString(
            'Golden Eye Academy | Courses and Classes in Pokhara',
            $response->getContent(),
        );
    }

    public function test_only_active_testimonials_with_exact_public_course_names_are_rendered(): void
    {
        $course = Course::factory()->create([
            'name' => 'Exact Published Course',
            'slug' => 'exact-published-course',
            'status' => 'active',
        ]);

        Testimonial::factory()->create([
            'student_name' => 'Exact Linked Learner',
            'course_name' => $course->name,
            'content' => 'Exact linked feedback content.',
            'status' => 'active',
        ]);
        Testimonial::factory()->create([
            'student_name' => 'Unmatched Learner',
            'course_name' => 'Unmatched Course Name',
            'content' => 'Unmatched feedback must not appear.',
            'status' => 'active',
        ]);
        Testimonial::factory()->create([
            'student_name' => 'Inactive Learner',
            'course_name' => $course->name,
            'content' => 'Inactive feedback must not appear.',
            'status' => 'inactive',
        ]);

        cache()->flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Exact Linked Learner')
            ->assertDontSee('Unmatched Learner')
            ->assertDontSee('Inactive Learner');

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('Exact Linked Learner')
            ->assertDontSee('Unmatched Learner')
            ->assertDontSee('Inactive Learner');
    }

    public function test_course_page_does_not_generate_missing_outline_schedule_or_proof(): void
    {
        $course = Course::factory()->create([
            'name' => 'Course With Missing Public Facts',
            'slug' => 'course-with-missing-public-facts',
            'description' => '',
            'course_outline' => '',
            'capacity' => '99',
            'instructor' => 'Unmatched Faculty Name',
            'status' => 'active',
        ]);

        SiteSetting::query()->create([
            'key' => 'course_confirmation_note',
            'value' => 'Confirm current facts directly with the academy.',
            'type' => 'text',
        ]);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('Confirm current facts directly with the academy.')
            ->assertSee('Quick facts')
            ->assertSee('Student View')
            ->assertSee('Parent View')
            ->assertSee('Confirm schedule before enrollment')
            ->assertDontSee('Best for:')
            ->assertDontSee('Published course outline')
            ->assertDontSee('Week-by-week curriculum')
            ->assertDontSee('Outline item 1')
            ->assertDontSee('Morning, day, and evening')
            ->assertDontSee('99 Seats')
            ->assertDontSee('Instructor profile')
            ->assertDontSee('Published student feedback')
            ->assertDontSee('Verified external reviews');
    }

    public function test_safe_blog_baselines_are_structured_and_preserve_all_existing_slugs_and_statuses(): void
    {
        $this->seed(BlogSeeder::class);

        $expectedSlugs = [
            'which-course-should-i-choose-after-see-or-plus-two',
            'ielts-or-pte-how-to-choose-the-right-test',
            'why-office-skills-still-matter-for-job-seekers',
            'how-web-development-builds-a-career-portfolio',
            'korean-eps-topik-preparation-what-beginners-should-know',
            'japanese-jlpt-n5-a-practical-starting-plan',
            'parents-guide-how-to-evaluate-a-training-institute',
            'why-you-should-ask-before-enrollment',
        ];

        $posts = BlogPost::query()->orderBy('id')->get();

        $this->assertCount(8, $posts);
        $this->assertSame($expectedSlugs, $posts->pluck('slug')->all());
        $this->assertSame(['published'], $posts->pluck('status')->unique()->values()->all());
        $this->assertSame(['Golden Eye Academy'], $posts->pluck('author')->unique()->values()->all());

        foreach ($posts as $post) {
            $this->assertGreaterThanOrEqual(3, substr_count(strtolower($post->content), '<h2>'));
            $this->assertStringContainsString('<ul>', $post->content);
            $this->assertStringNotContainsString('training center', strtolower($post->content));
        }
    }

    public function test_faq_baselines_preserve_order_and_use_confirmation_language_for_changeable_facts(): void
    {
        $this->seed(FAQSeeder::class);

        $faqs = FAQ::query()->orderBy('order_priority')->get();

        $this->assertCount(20, $faqs);
        $this->assertSame(range(10, 200, 10), $faqs->pluck('order_priority')->all());
        $this->assertSame(['active'], $faqs->pluck('status')->unique()->values()->all());
        $this->assertSame(
            'Certificate availability and completion requirements can vary by course. Ask the academy to confirm the current certificate details before enrollment.',
            $faqs->firstWhere('question', 'Do you provide certificates?')?->answer,
        );
        $this->assertSame(
            'Class timing depends on the current course and batch. Contact the academy to confirm which schedules are currently available.',
            $faqs->firstWhere('question', 'Are flexible class timings available?')?->answer,
        );
        $this->assertSame(
            'Workshop and event availability changes over time. Check the current notices or contact the academy for verified event details.',
            $faqs->firstWhere('question', 'Do you run events and workshops?')?->answer,
        );
    }

    public function test_content_updates_leave_protected_products_and_public_urls_identical(): void
    {
        $this->seed([
            CourseCategorySeeder::class,
            CourseSeeder::class,
            ServicePillarSeeder::class,
        ]);

        $coursesBefore = Course::query()->orderBy('id')->get()->makeHidden(['created_at', 'updated_at'])->toArray();
        $categoriesBefore = CourseCategory::query()->orderBy('id')->get()->makeHidden(['created_at', 'updated_at'])->toArray();
        $pillarsBefore = ServicePillar::query()->orderBy('id')->get()->makeHidden(['created_at', 'updated_at'])->toArray();
        $urlsBefore = Course::query()
            ->orderBy('id')
            ->pluck('slug')
            ->map(fn (string $slug): string => route('courses-detail', $slug))
            ->all();

        $this->seed([
            SiteSettingSeeder::class,
            FAQSeeder::class,
            BlogSeeder::class,
        ]);

        $this->assertCount(13, $coursesBefore);
        $this->assertCount(5, $categoriesBefore);
        $this->assertCount(7, $pillarsBefore);
        $this->assertSame($coursesBefore, Course::query()->orderBy('id')->get()->makeHidden(['created_at', 'updated_at'])->toArray());
        $this->assertSame($categoriesBefore, CourseCategory::query()->orderBy('id')->get()->makeHidden(['created_at', 'updated_at'])->toArray());
        $this->assertSame($pillarsBefore, ServicePillar::query()->orderBy('id')->get()->makeHidden(['created_at', 'updated_at'])->toArray());
        $this->assertSame(
            $urlsBefore,
            Course::query()
                ->orderBy('id')
                ->pluck('slug')
                ->map(fn (string $slug): string => route('courses-detail', $slug))
                ->all(),
        );

        foreach ($urlsBefore as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_popup_notice_banner_and_primary_public_journeys_remain_available(): void
    {
        SiteSetting::query()->upsert([
            ['key' => 'popup_status', 'value' => 'active', 'type' => 'text'],
            ['key' => 'popup_title', 'value' => 'Phase 6 Campaign Popup', 'type' => 'text'],
        ], ['key'], ['value', 'type']);

        Notice::query()->create([
            'title' => 'Phase 6 Announcement Banner',
            'status' => 'active',
            'display_type' => 'bar',
        ]);

        cache()->flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Phase 6 Campaign Popup')
            ->assertSee('Phase 6 Announcement Banner')
            ->assertSee('home-hero', false)
            ->assertSee('siteNoticeStrip', false)
            ->assertSee('data-cta="navbar-course-help"', false)
            ->assertSee('data-cta="homepage-final-whatsapp"', false);

        Notice::query()->create([
            'title' => 'Phase 6 Popup Notice',
            'status' => 'active',
            'display_type' => 'popup',
        ]);

        cache()->flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Phase 6 Announcement Banner')
            ->assertSee('Phase 6 Popup Notice')
            ->assertSee('home-hero', false)
            ->assertSee('siteNoticeStrip', false)
            ->assertSee('siteNoticePopup', false)
            ->assertSee('data-cta="navbar-course-help"', false)
            ->assertSee('data-cta="homepage-final-whatsapp"', false);

        foreach (['about', 'courses-all', 'blog', 'faq', 'contact', 'join-now'] as $routeName) {
            $this->get(route($routeName))->assertOk();
        }
    }
}
