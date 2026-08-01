<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\FAQ;
use App\Models\ServicePillar;
use App\Models\SiteSetting;
use App\Support\GoldenEyeArticleBaseline;
use App\Support\GoldenEyeContentBaseline;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

#[Signature('goldeneye:publish-content-baseline
    {--apply : Apply the reviewed baseline; the command is a dry run without this flag}
    {--backup-confirmed : Confirm a current database backup exists before a production apply}
    {--manifest= : Write the applied-change manifest to this path}')]
#[Description('Preview or publish the guarded Golden Eye Academy CMS content baseline.')]
class PublishGoldenEyeContentBaseline extends Command
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $operations = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $audit = [];

    private int $unexpectedCount = 0;

    public function handle(): int
    {
        $this->operations = [];
        $this->audit = [];
        $this->unexpectedCount = 0;

        $apply = (bool) $this->option('apply');

        if ($apply && app()->environment('production') && ! $this->option('backup-confirmed')) {
            $this->error('Production apply refused: pass --backup-confirmed only after verifying a current database backup.');

            return self::FAILURE;
        }

        $this->inspectSiteSettings();
        $this->inspectFaqs();
        $this->inspectArticles();
        $this->inspectCourses();
        $this->inspectServicePillars();

        $this->renderAuditTable($apply);

        if ($this->unexpectedCount > 0) {
            $this->error("{$this->unexpectedCount} unexpected value(s) require owner review. No changes were applied.");

            return self::FAILURE;
        }

        if (! $apply) {
            $this->info('Dry run complete. Re-run with --apply after reviewing every proposed change.');

            return self::SUCCESS;
        }

        try {
            DB::transaction(function (): void {
                foreach ($this->operations as $operation) {
                    $this->applyOperation($operation);
                }
            });
        } catch (Throwable $exception) {
            report($exception);
            $this->error('The transaction failed. No database changes were committed.');

            return self::FAILURE;
        }

        $this->clearAffectedCaches();

        try {
            $manifestPath = $this->writeManifest();
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Content was applied, but the required manifest could not be written. Review logs immediately.');

            return self::FAILURE;
        }

        $this->info(count($this->operations).' guarded record change(s) applied.');
        $this->info("Manifest: {$manifestPath}");

        return self::SUCCESS;
    }

    private function inspectSiteSettings(): void
    {
        $legacyValues = GoldenEyeContentBaseline::legacySiteSettings();

        foreach (GoldenEyeContentBaseline::siteSettings() as $key => $after) {
            $setting = SiteSetting::query()->where('key', $key)->first();

            if (! $setting) {
                if (array_key_exists($key, $legacyValues)) {
                    $this->recordUnexpected('site_settings', $key, null, $after, 'expected legacy setting is missing');

                    continue;
                }

                $this->recordChange('site_settings', $key, null, $after, SiteSetting::class, null, ['key' => $key, ...$after]);

                continue;
            }

            $before = ['value' => (string) ($setting->value ?? ''), 'type' => (string) ($setting->type ?? 'text')];
            if ($before === $after) {
                $this->recordUnchanged('site_settings', $key, $before, $after);

                continue;
            }

            if (! array_key_exists($key, $legacyValues) || $before['value'] !== $legacyValues[$key]) {
                $this->recordUnexpected('site_settings', $key, $before, $after, 'value differs from the diagnosed legacy baseline');

                continue;
            }

            $this->recordChange('site_settings', $key, $before, $after, SiteSetting::class, $setting->getKey(), $after);
        }
    }

    private function inspectFaqs(): void
    {
        $legacy = collect(GoldenEyeContentBaseline::legacyFaqs())->keyBy('id');

        foreach (GoldenEyeContentBaseline::faqs() as $afterBaseline) {
            $faq = FAQ::query()->find($afterBaseline['id']);
            $identity = (string) $afterBaseline['id'];
            if (! $faq) {
                $this->recordUnexpected('f_a_q_s', $identity, null, $afterBaseline, 'protected FAQ ID is missing');

                continue;
            }

            $legacyFaq = $legacy->get($afterBaseline['id']);
            $beforeGuard = [
                'question' => (string) $faq->question,
                'answer' => (string) $faq->answer,
                'order_priority' => (int) $faq->order_priority,
            ];
            $after = $this->faqFields($afterBaseline);
            $afterGuard = array_intersect_key($after, $beforeGuard);

            if ($beforeGuard === $afterGuard && $this->modelMatches($faq, $after)) {
                $this->recordUnchanged('f_a_q_s', $identity, $this->attributes($faq, array_keys($after)), $after);

                continue;
            }

            if (! $legacyFaq || $beforeGuard !== [
                'question' => $legacyFaq['question'],
                'answer' => $legacyFaq['answer'],
                'order_priority' => $legacyFaq['order_priority'],
            ]) {
                $this->recordUnexpected('f_a_q_s', $identity, $beforeGuard, $afterGuard, 'FAQ content or order differs from the diagnosed legacy baseline');

                continue;
            }

            $this->recordChange('f_a_q_s', $identity, $this->attributes($faq, array_keys($after)), $after, FAQ::class, $faq->getKey(), $after);
        }
    }

    private function inspectArticles(): void
    {
        $legacy = GoldenEyeArticleBaseline::legacyArticles();

        foreach (GoldenEyeArticleBaseline::articles() as $article) {
            $post = BlogPost::query()->where('slug', $article['slug'])->first();
            $identity = $article['slug'];
            if (! $post) {
                $this->recordUnexpected('blog_posts', $identity, null, $article, 'protected article slug is missing');

                continue;
            }

            $guardFields = ['title', 'content', 'category', 'author'];
            $beforeGuard = $this->attributes($post, $guardFields);
            $after = $this->articleFields($article);
            $afterGuard = array_combine(
                $guardFields,
                array_map(fn (string $field): mixed => $after[$field], $guardFields),
            );

            if ($beforeGuard === $afterGuard) {
                if ($this->modelMatches($post, $after)) {
                    $this->recordUnchanged('blog_posts', $identity, $this->attributes($post, array_keys($after)), $after);
                } else {
                    $mismatchedFields = array_keys(array_filter(
                        $after,
                        fn (mixed $value, string $field): bool => $post->getAttribute($field) !== $value,
                        ARRAY_FILTER_USE_BOTH,
                    ));
                    $this->recordUnexpected('blog_posts', $identity, $this->attributes($post, $mismatchedFields), array_intersect_key($after, array_flip($mismatchedFields)), 'approved article metadata differs in '.implode(', ', $mismatchedFields));
                }

                continue;
            }

            if (! isset($legacy[$identity]) || $beforeGuard !== $legacy[$identity]) {
                $this->recordUnexpected('blog_posts', $identity, $beforeGuard, $afterGuard, 'article content differs from the diagnosed legacy baseline');

                continue;
            }

            $this->recordChange('blog_posts', $identity, $this->attributes($post, array_keys($after)), $after, BlogPost::class, $post->getKey(), $after);
        }
    }

    private function inspectCourses(): void
    {
        foreach (GoldenEyeContentBaseline::courseOverrides() as $slug => $override) {
            $course = Course::query()->where('slug', $slug)->first();
            if (! $course) {
                $this->recordUnexpected('courses', $slug, null, $override, 'protected course slug is missing');

                continue;
            }

            if ((string) $course->name !== $override['name']) {
                $this->recordUnexpected('courses', $slug, ['name' => $course->name], ['name' => $override['name']], 'protected course identity differs');

                continue;
            }

            $after = $this->courseFields($course, $override);
            if ($this->modelMatches($course, $after)) {
                $this->recordUnchanged('courses', $slug, $this->attributes($course, array_keys($after)), $after);

                continue;
            }

            $signature = $this->signature([(string) ($course->badge_text ?? ''), (string) ($course->description ?? '')]);
            $approvedSignature = $this->signature([$override['badge_text'], $override['description']]);
            if ($signature === $approvedSignature) {
                $this->recordChange('courses', $slug, $this->attributes($course, array_keys($after)), $after, Course::class, $course->getKey(), $after);

                continue;
            }

            if ($signature !== GoldenEyeContentBaseline::legacyCourseSignatures()[$slug]) {
                $this->recordUnexpected('courses', $slug, ['signature' => $signature], ['signature' => GoldenEyeContentBaseline::legacyCourseSignatures()[$slug]], 'protected course copy differs from the diagnosed legacy baseline');

                continue;
            }

            $this->recordChange('courses', $slug, $this->attributes($course, array_keys($after)), $after, Course::class, $course->getKey(), $after);
        }
    }

    private function inspectServicePillars(): void
    {
        foreach (GoldenEyeContentBaseline::servicePillarUpdates() as $slug => $baseline) {
            $pillar = ServicePillar::query()->where('slug', $slug)->first();
            if (! $pillar) {
                $this->recordUnexpected('service_pillars', $slug, null, $baseline, 'protected service-pillar slug is missing');

                continue;
            }

            $after = $this->servicePillarFields($baseline);
            if ($this->modelMatches($pillar, $after)) {
                $this->recordUnchanged('service_pillars', $slug, $this->attributes($pillar, array_keys($after)), $after);

                continue;
            }

            $signature = $this->signature([(string) $pillar->title, (string) ($pillar->summary ?? ''), $pillar->bullets]);
            if ($signature !== GoldenEyeContentBaseline::legacyServicePillarSignatures()[$slug]) {
                $this->recordUnexpected('service_pillars', $slug, ['signature' => $signature], ['signature' => GoldenEyeContentBaseline::legacyServicePillarSignatures()[$slug]], 'service-pillar copy differs from the diagnosed legacy baseline');

                continue;
            }

            $this->recordChange('service_pillars', $slug, $this->attributes($pillar, array_keys($after)), $after, ServicePillar::class, $pillar->getKey(), $after);
        }
    }

    /**
     * @param  array{id: int, question: string, answer: string, order_priority: int}  $faq
     * @return array<string, mixed>
     */
    private function faqFields(array $faq): array
    {
        return [
            'question' => $faq['question'],
            'answer' => $faq['answer'],
            'order_priority' => $faq['order_priority'],
            'meta_title' => $faq['question'].' | Golden Eye Academy FAQ',
            'meta_description' => Str::limit($faq['answer'], 155, ''),
            'meta_keywords' => 'Golden Eye Academy FAQ, courses in Pokhara',
            'aeo_summary' => $faq['answer'],
            'schema_markup' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ];
    }

    /**
     * @param  array<string, string>  $article
     * @return array<string, mixed>
     */
    private function articleFields(array $article): array
    {
        $summary = Str::squish(strip_tags($article['content']));

        return [
            'title' => $article['title'],
            'content' => $article['content'],
            'image' => $article['image'],
            'author' => $article['author'],
            'category' => $article['category'],
            'meta_title' => $article['title'].' | Golden Eye Academy',
            'meta_description' => Str::limit($summary, 155, ''),
            'meta_keywords' => $article['meta_keywords'],
            'aeo_summary' => Str::limit($summary, 240, ''),
            'schema_markup' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $article['title'],
                'author' => ['@type' => 'Organization', 'name' => 'Golden Eye Academy'],
                'publisher' => ['@type' => 'EducationalOrganization', 'name' => 'Golden Eye Academy'],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ];
    }

    /**
     * @param  array{name: string, badge_text: string, description: string}  $override
     * @return array<string, mixed>
     */
    private function courseFields(Course $course, array $override): array
    {
        return [
            'badge_text' => $override['badge_text'],
            'description' => $override['description'],
            'meta_title' => $course->name.' | Golden Eye Academy',
            'meta_description' => Str::limit($override['description'], 500, ''),
            'aeo_summary' => $override['description'],
            'schema_markup' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Course',
                'name' => $course->name,
                'description' => $override['description'],
                'provider' => ['@type' => 'EducationalOrganization', 'name' => 'Golden Eye Academy'],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ];
    }

    /**
     * @param  array{title: string, summary: string, bullets: array<int, string>}  $baseline
     * @return array<string, mixed>
     */
    private function servicePillarFields(array $baseline): array
    {
        return [
            ...$baseline,
            'meta_title' => $baseline['title'].' | Golden Eye Academy',
            'meta_description' => $baseline['summary'],
            'meta_keywords' => 'Golden Eye Academy, courses and classes in Pokhara',
            'aeo_summary' => $baseline['summary'],
            'schema_markup' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                'name' => $baseline['title'],
                'description' => $baseline['summary'],
                'provider' => ['@type' => 'EducationalOrganization', 'name' => 'Golden Eye Academy'],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ];
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @param  array<string, mixed>  $fields
     */
    private function recordChange(string $table, string $identity, ?array $before, array $after, string $modelClass, int|string|null $modelId, array $fields): void
    {
        $status = $before === null ? 'would create' : 'would update';
        $this->audit[] = compact('table', 'identity', 'before', 'after', 'status');
        $this->operations[] = compact('table', 'identity', 'modelClass', 'modelId', 'fields');
    }

    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>  $after
     */
    private function recordUnexpected(string $table, string $identity, ?array $before, array $after, string $reason): void
    {
        $status = 'unexpected: '.$reason;
        $this->audit[] = compact('table', 'identity', 'before', 'after', 'status');
        $this->unexpectedCount++;
    }

    /**
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     */
    private function recordUnchanged(string $table, string $identity, array $before, array $after): void
    {
        $status = 'unchanged';
        $this->audit[] = compact('table', 'identity', 'before', 'after', 'status');
    }

    /**
     * @param  array<string, mixed>  $operation
     */
    private function applyOperation(array $operation): void
    {
        if ($operation['modelClass'] === SiteSetting::class && $operation['modelId'] === null) {
            SiteSetting::query()->create($operation['fields']);

            return;
        }

        /** @var Model|null $model */
        $model = $operation['modelClass']::query()->find($operation['modelId']);
        if (! $model) {
            throw new \RuntimeException("Guarded record disappeared during apply: {$operation['table']} {$operation['identity']}");
        }

        $model->forceFill($operation['fields'])->save();
    }

    private function renderAuditTable(bool $apply): void
    {
        $rows = array_map(fn (array $entry): array => [
            $entry['table'],
            $entry['identity'],
            $this->displayValue($entry['before']),
            $this->displayValue($entry['after']),
            $apply && str_starts_with($entry['status'], 'would ') ? str_replace('would ', '', $entry['status']) : $entry['status'],
        ], $this->audit);

        $this->table(['Table', 'Identity', 'Before', 'After', 'Status'], $rows);
    }

    private function writeManifest(): string
    {
        $path = trim((string) $this->option('manifest'));
        if ($path === '') {
            $path = storage_path('app/content-rollouts/golden-eye-content-baseline-'.now()->format('Ymd-His').'.json');
        } elseif (! Str::startsWith($path, ['/', '\\']) && ! preg_match('/^[A-Za-z]:[\\\\\/]/', $path)) {
            $path = base_path($path);
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, json_encode([
            'rollout' => 'golden-eye-content-baseline',
            'applied_at' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'database_connection' => DB::connection()->getName(),
            'changes' => array_values(array_filter(
                $this->audit,
                fn (array $entry): bool => str_starts_with($entry['status'], 'would '),
            )),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL);

        return $path;
    }

    private function clearAffectedCaches(): void
    {
        foreach (array_keys(GoldenEyeContentBaseline::siteSettings()) as $key) {
            Cache::forget("setting_{$key}");
        }

        foreach (['homepage_data', 'homepage_data_v2', 'site_settings', 'site_shared_data', 'service_pillars', 'sitemap_xml'] as $key) {
            Cache::forget($key);
        }
    }

    /**
     * @param  array<int, string>  $fields
     * @return array<string, mixed>
     */
    private function attributes(Model $model, array $fields): array
    {
        $values = [];
        foreach ($fields as $field) {
            $values[$field] = $model->getAttribute($field);
        }

        return $values;
    }

    /**
     * @param  array<string, mixed>  $fields
     */
    private function modelMatches(Model $model, array $fields): bool
    {
        return $this->attributes($model, array_keys($fields)) === $fields;
    }

    /**
     * @param  array<int, mixed>  $values
     */
    private function signature(array $values): string
    {
        return hash('sha256', json_encode($values, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }

    private function displayValue(mixed $value): string
    {
        if ($value === null) {
            return '(missing)';
        }

        $encoded = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return Str::limit($encoded === false ? '(unavailable)' : $encoded, 80, '...');
    }
}
