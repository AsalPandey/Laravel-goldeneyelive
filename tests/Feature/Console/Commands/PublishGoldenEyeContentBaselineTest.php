<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\PublishGoldenEyeContentBaseline;
use App\Models\BlogPost;
use App\Models\Course;
use App\Models\FAQ;
use App\Models\SiteSetting;
use App\Support\GoldenEyeArticleBaseline;
use App\Support\GoldenEyeContentBaseline;
use Database\Seeders\CourseCategorySeeder;
use Database\Seeders\CourseSeeder;
use Database\Seeders\ServicePillarSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use ReflectionMethod;
use Tests\TestCase;

class PublishGoldenEyeContentBaselineTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_is_a_dry_run_by_default(): void
    {
        $this->seedLegacyOperationalContent();

        $exitCode = Artisan::call('goldeneye:publish-content-baseline');
        $output = Artisan::output();
        $this->assertSame(0, $exitCode, $output);
        $this->assertStringContainsString('Dry run complete', $output);

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'hero_title',
            'value' => GoldenEyeContentBaseline::legacySiteSettings()['hero_title'],
        ]);
        $this->assertDatabaseMissing(SiteSetting::class, ['key' => 'founding_year']);
    }

    public function test_apply_is_transactional_preserves_identities_and_is_idempotent(): void
    {
        $this->seedLegacyOperationalContent();
        $manifestPath = storage_path('framework/testing/golden-eye-content-rollout.json');
        File::delete($manifestPath);

        $protectedCourses = Course::query()->orderBy('id')->get()->map(fn (Course $course): array => [
            'id' => $course->id,
            'name' => $course->name,
            'slug' => $course->slug,
            'status' => $course->status,
            'category' => $course->category,
            'category_slug' => $course->category_slug,
            'category_id' => $course->category_id,
            'price' => $course->price,
            'duration' => $course->duration,
            'instructor' => $course->instructor,
            'course_outline' => $course->course_outline,
        ])->all();
        $faqStatuses = FAQ::query()->orderBy('id')->pluck('status', 'id')->all();
        $blogStatuses = BlogPost::query()->orderBy('id')->pluck('status', 'slug')->all();

        $exitCode = Artisan::call('goldeneye:publish-content-baseline', [
            '--apply' => true,
            '--manifest' => $manifestPath,
        ]);
        $this->assertSame(0, $exitCode, Artisan::output());

        $this->assertFileExists($manifestPath);
        $manifest = json_decode(File::get($manifestPath), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('golden-eye-content-baseline', $manifest['rollout']);
        $this->assertNotEmpty($manifest['changes']);
        $this->assertSame(
            GoldenEyeContentBaseline::settingValue('hero_title'),
            SiteSetting::getValue('hero_title'),
        );
        $this->assertSame('2008', SiteSetting::getValue('founding_year'));
        $this->assertSame(GoldenEyeContentBaseline::faqs()[0]['question'], FAQ::findOrFail(1)->question);
        $this->assertSame(GoldenEyeArticleBaseline::articles()[0]['title'], BlogPost::query()->where('slug', GoldenEyeArticleBaseline::articles()[0]['slug'])->value('title'));
        $articleFields = new ReflectionMethod(PublishGoldenEyeContentBaseline::class, 'articleFields');
        foreach (GoldenEyeArticleBaseline::articles() as $article) {
            $post = BlogPost::query()->where('slug', $article['slug'])->firstOrFail();
            foreach ($articleFields->invoke(app(PublishGoldenEyeContentBaseline::class), $article) as $field => $value) {
                $this->assertSame($value, $post->getAttribute($field), $article['slug'].': '.$field);
            }
        }
        $this->assertSame($faqStatuses, FAQ::query()->orderBy('id')->pluck('status', 'id')->all());
        $this->assertSame($blogStatuses, BlogPost::query()->orderBy('id')->pluck('status', 'slug')->all());
        $this->assertSame($protectedCourses, Course::query()->orderBy('id')->get()->map(fn (Course $course): array => [
            'id' => $course->id,
            'name' => $course->name,
            'slug' => $course->slug,
            'status' => $course->status,
            'category' => $course->category,
            'category_slug' => $course->category_slug,
            'category_id' => $course->category_id,
            'price' => $course->price,
            'duration' => $course->duration,
            'instructor' => $course->instructor,
            'course_outline' => $course->course_outline,
        ])->all());

        $databaseFingerprint = $this->contentFingerprint();
        $exitCode = Artisan::call('goldeneye:publish-content-baseline', [
            '--apply' => true,
            '--manifest' => $manifestPath,
        ]);
        $output = Artisan::output();
        $this->assertSame(0, $exitCode, $output);
        $this->assertStringContainsString('0 guarded record change(s) applied.', $output);
        $this->assertSame($databaseFingerprint, $this->contentFingerprint());

        File::delete($manifestPath);
    }

    public function test_unexpected_owner_value_blocks_the_entire_apply(): void
    {
        $this->seedLegacyOperationalContent();
        SiteSetting::query()->where('key', 'hero_title')->update(['value' => 'Owner-authored hero that must not be overwritten']);
        $legacyFaq = FAQ::findOrFail(1)->question;

        $this->artisan('goldeneye:publish-content-baseline', ['--apply' => true])
            ->expectsOutputToContain('unexpected value(s) require owner review')
            ->assertFailed();

        $this->assertSame('Owner-authored hero that must not be overwritten', SiteSetting::getValue('hero_title'));
        $this->assertSame($legacyFaq, FAQ::findOrFail(1)->question);
        $this->assertDatabaseMissing(SiteSetting::class, ['key' => 'founding_year']);
    }

    public function test_production_apply_requires_explicit_backup_confirmation(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('goldeneye:publish-content-baseline', ['--apply' => true])
            ->expectsOutputToContain('Production apply refused')
            ->assertFailed();
    }

    private function seedLegacyOperationalContent(): void
    {
        $this->seed([
            CourseCategorySeeder::class,
            CourseSeeder::class,
            ServicePillarSeeder::class,
        ]);

        foreach (GoldenEyeContentBaseline::legacySiteSettings() as $key => $value) {
            if (! array_key_exists($key, GoldenEyeContentBaseline::siteSettings())) {
                continue;
            }

            SiteSetting::query()->create(['key' => $key, 'value' => $value, 'type' => 'text']);
        }

        foreach (GoldenEyeContentBaseline::legacyFaqs() as $faq) {
            FAQ::query()->create([
                ...$faq,
                'status' => $faq['id'] === 2 ? 'inactive' : 'active',
            ]);
        }

        $articles = collect(GoldenEyeArticleBaseline::articles())->keyBy('slug');
        foreach (GoldenEyeArticleBaseline::legacyArticles() as $slug => $article) {
            BlogPost::query()->create([
                ...$article,
                'slug' => $slug,
                'image' => $articles[$slug]['image'],
                'status' => $slug === 'why-you-should-ask-before-enrollment' ? 'draft' : 'published',
                'published_at' => now()->subDay(),
            ]);
        }

        cache()->flush();
    }

    private function contentFingerprint(): string
    {
        return hash('sha256', json_encode([
            SiteSetting::query()->orderBy('id')->get()->toArray(),
            FAQ::query()->orderBy('id')->get()->toArray(),
            BlogPost::query()->orderBy('id')->get()->toArray(),
            Course::query()->orderBy('id')->get()->toArray(),
        ], JSON_THROW_ON_ERROR));
    }
}
