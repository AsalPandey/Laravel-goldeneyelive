<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\FAQ;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\GoldenEyeArticleBaseline;
use App\Support\GoldenEyeContentBaseline;
use App\Support\StructuredData;
use Database\Seeders\LiveSiteSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoldenEyeContentBaselineTest extends TestCase
{
    use RefreshDatabase;

    public function test_fresh_checkpoint_renders_owner_approved_identity_and_homepage_copy(): void
    {
        $this->seed(LiveSiteSeeder::class);
        cache()->flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Established in Pokhara since 2008')
            ->assertSeeText('Build practical skills for study, work and what comes next.')
            ->assertSeeText('Explore computer, language, test-preparation and academic-support classes, with clear guidance to help you choose a suitable course and current batch.')
            ->assertSeeText('Clear course information. Practical learning. Guidance before enrollment.')
            ->assertSeeText('Ask for Course Help')
            ->assertSeeText('View Course Details')
            ->assertSeeText('Message on WhatsApp')
            ->assertDontSeeText('Brilliant Education Pokhara');

        $this->assertSame('2008', SiteSetting::getValue('founding_year'));
        $this->assertSame(
            GoldenEyeContentBaseline::settingValue('hero_title'),
            SiteSetting::getValue('hero_title'),
        );
    }

    public function test_partner_name_is_confined_to_the_study_abroad_context(): void
    {
        $this->seed(LiveSiteSeeder::class);
        cache()->flush();

        foreach (['home', 'about', 'catalogue', 'courses-all', 'blog', 'faq', 'contact', 'for-students', 'for-parents', 'job-computer-skills'] as $routeName) {
            $this->get(route($routeName))
                ->assertOk()
                ->assertSeeText('Golden Eye Academy')
                ->assertDontSeeText('Brilliant Education Pokhara');
        }

        $this->get(route('study-abroad-guidance'))
            ->assertOk()
            ->assertSeeText('Golden Eye Academy provides courses, classes and academic support. For education-consulting guidance, Golden Eye Academy works with its partner, Brilliant Education Pokhara.')
            ->assertSeeText('Ask for Course Help');
    }

    public function test_all_faqs_and_articles_are_substantial_cms_records_with_protected_urls(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $faqs = FAQ::query()->orderBy('id')->get();
        $this->assertCount(20, $faqs);
        $this->assertSame(range(1, 20), $faqs->pluck('id')->all());
        $this->assertSame(range(10, 200, 10), $faqs->pluck('order_priority')->all());
        $this->assertSame(['active'], $faqs->pluck('status')->unique()->values()->all());

        $expectedArticles = collect(GoldenEyeArticleBaseline::articles())->keyBy('slug');
        $posts = BlogPost::query()->orderBy('id')->get();
        $this->assertCount(8, $posts);
        $this->assertSame($expectedArticles->keys()->all(), $posts->pluck('slug')->all());

        foreach ($posts as $post) {
            $this->assertGreaterThanOrEqual(700, str_word_count(strip_tags($post->content)), $post->slug);
            $this->assertGreaterThanOrEqual(5, substr_count(strtolower($post->content), '<h2>'));
            $this->assertStringContainsString('href="/', $post->content);
            $this->assertSame('Golden Eye Academy', $post->author);
            $this->assertSame('published', $post->status);
            $this->get(route('blog-detail', $post->slug))->assertOk()->assertSeeText($post->title);
        }

        $visibleContent = strtolower($posts->pluck('content')->implode(' ').$posts->pluck('title')->implode(' '));
        $this->assertStringNotContainsString('training institute', $visibleContent);
        $this->assertStringNotContainsString('training center', $visibleContent);
        $this->assertStringNotContainsString('brilliant education', $visibleContent);
    }

    public function test_course_identities_and_relationship_fields_match_the_protected_inventory(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $courses = Course::query()->orderBy('id')->get();
        $this->assertCount(13, $courses);
        $this->assertSame(array_keys(GoldenEyeContentBaseline::courseOverrides()), $courses->pluck('slug')->all());

        foreach (GoldenEyeContentBaseline::courseOverrides() as $slug => $expected) {
            $course = $courses->firstWhere('slug', $slug);
            $this->assertNotNull($course);
            $this->assertSame($expected['name'], $course->name);
            $this->assertSame($expected['badge_text'], $course->badge_text);
            $this->assertSame($expected['description'], $course->description);
            $this->assertNotNull($course->category_id);
            $this->get(route('courses-detail', $slug))->assertOk();
        }
    }

    public function test_founding_date_and_organizational_article_authorship_are_safe_structured_data(): void
    {
        $this->seed(LiveSiteSeeder::class);
        $settings = SiteSetting::query()->pluck('value', 'key')->all();
        $siteGraph = StructuredData::siteGraph($settings);

        $this->assertSame('Golden Eye Academy', $siteGraph['@graph'][0]['name']);
        $this->assertSame('2008', $siteGraph['@graph'][0]['foundingDate']);
        $this->assertStringNotContainsString('Brilliant', json_encode($siteGraph, JSON_THROW_ON_ERROR));

        $post = BlogPost::query()->firstOrFail();
        $articleSchema = StructuredData::articleSchema($post, $settings);
        $this->assertSame(StructuredData::organizationId(), $articleSchema['author']['@id']);
        $this->assertSame(StructuredData::organizationId(), $articleSchema['publisher']['@id']);
    }

    public function test_staff_can_edit_new_catalogue_and_contact_copy_while_established_year_stays_admin_only(): void
    {
        $this->seed([LiveSiteSeeder::class, RoleSeeder::class]);
        $staff = User::factory()->create();
        $staff->assignRole('Staff');

        $this->actingAs($staff)
            ->get(route('admin.branding.index'))
            ->assertOk()
            ->assertSee('name="catalogue_title"', false)
            ->assertSee('name="catalogue_meta_description"', false)
            ->assertSee('name="contact_header_subtitle"', false)
            ->assertSee('name="contact_form_title"', false)
            ->assertDontSee('name="founding_year"', false);

        $this->actingAs($staff)->post(route('admin.branding.update'), [
            'catalogue_title' => 'A staff-edited catalogue heading',
            'contact_form_title' => 'A staff-edited contact heading',
            'founding_year' => '1999',
        ])->assertRedirect();

        cache()->flush();
        $this->get(route('catalogue'))->assertOk()->assertSeeText('A staff-edited catalogue heading');
        $this->get(route('contact'))->assertOk()->assertSeeText('A staff-edited contact heading');
        $this->assertSame('2008', SiteSetting::getValue('founding_year'));
    }
}
