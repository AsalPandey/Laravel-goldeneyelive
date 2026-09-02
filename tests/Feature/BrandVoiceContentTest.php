<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\ServicePillar;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandVoiceContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_uses_academy_brand_and_established_positioning(): void
    {
        SiteSetting::insert([
            ['key' => 'site_name', 'value' => 'GoldenEye', 'type' => 'text'],
            ['key' => 'site_name_suffix', 'value' => 'Academy', 'type' => 'text'],
            ['key' => 'schema_markup', 'value' => '{"@context":"https://schema.org","@type":"EducationalOrganization","name":"GoldenEye Academy"}', 'type' => 'text'],
        ]);

        $html = $this->get(route('home'))
            ->assertOk()
            ->assertSee('Golden Eye Academy', false)
            ->assertSee('Build practical skills for study, work and what comes next.', false)
            ->assertDontSee('Study Abroad, Language &amp; Computer Courses in Pokhara', false)
            ->assertDontSee('GoldenEye Academy', false)
            ->getContent();

        $this->assertStringContainsString('practical skills', $html);
    }

    public function test_international_preparation_page_keeps_consulting_context_secondary(): void
    {
        $this->get(route('study-abroad-guidance'))
            ->assertOk()
            ->assertSee('Build the language and test skills your plan requires.', false)
            ->assertSee('Focus on the skills you need to practise.', false)
            ->assertSee('Brilliant Education Pokhara', false)
            ->assertDontSee('legal and billing operator', false)
            ->assertDontSee('migration', false);
    }

    public function test_organization_schema_normalizes_old_compact_brand(): void
    {
        SiteSetting::insert([
            ['key' => 'site_name', 'value' => 'GoldenEye', 'type' => 'text'],
            ['key' => 'site_name_suffix', 'value' => 'Academy', 'type' => 'text'],
            ['key' => 'schema_markup', 'value' => '{"@context":"https://schema.org","@type":"EducationalOrganization","name":"GoldenEye Academy","telephone":"061-572599"}', 'type' => 'text'],
        ]);

        $nodes = $this->jsonLdNodes($this->get(route('home'))->assertOk()->getContent());
        $organization = collect($nodes)->first(fn (array $node): bool => ($node['@type'] ?? null) === 'EducationalOrganization');

        $this->assertIsArray($organization);
        $this->assertSame('Golden Eye Academy', $organization['name'] ?? null);
    }

    public function test_course_detail_normalizes_old_brand_in_dynamic_metadata(): void
    {
        $course = Course::factory()->create([
            'name' => 'IELTS Masterclass',
            'slug' => 'ielts-masterclass',
            'status' => 'active',
            'meta_title' => 'IELTS Masterclass | GoldenEye Academy',
            'meta_description' => 'GoldenEye Academy IELTS class with mock tests and feedback.',
        ]);

        $this->get(route('courses-detail', $course->slug))
            ->assertOk()
            ->assertSee('<title>IELTS Masterclass | Golden Eye Academy</title>', false)
            ->assertSee('Golden Eye Academy IELTS class with mock tests and feedback.', false)
            ->assertDontSee('GoldenEye Academy', false)
            ->assertDontSee('GoldenEye Academy - Golden Eye Academy', false);
    }

    public function test_brand_copy_normalization_command_is_dry_run_idempotent_and_scoped(): void
    {
        SiteSetting::create([
            'key' => 'site_name',
            'value' => 'GoldenEye',
            'type' => 'text',
        ]);
        CourseCategory::factory()->create([
            'name' => 'Study Abroad Test Prep',
            'description' => 'GoldenEye language and exam-preparation classes with old brand wording.',
        ]);
        Course::factory()->create([
            'description' => 'GoldenEye course information with old brand wording.',
            'instructor' => 'Course Help Team',
        ]);
        FAQ::factory()->create([
            'question' => 'What courses does GoldenEye Academy offer?',
            'answer' => 'GoldenEye Academy offers course guidance before enrollment.',
        ]);
        ServicePillar::factory()->create([
            'title' => 'Languages and Test Preparation',
            'slug' => 'languages-and-test-preparation',
            'summary' => 'GoldenEye language, IELTS, PTE, Japanese and Korean preparation classes.',
            'bullets' => ['Compare the current language and test-preparation classes.'],
        ]);
        $contact = Contact::factory()->create(['subject' => 'Original contact subject']);

        $this->artisan('goldeneye:normalize-brand-copy', ['--dry-run' => true])
            ->expectsOutputToContain('would change')
            ->expectsOutputToContain('Dry-run total changes:')
            ->assertExitCode(0);

        $this->assertDatabaseHas(SiteSetting::class, ['key' => 'site_name', 'value' => 'GoldenEye']);

        $this->artisan('goldeneye:normalize-brand-copy')
            ->expectsOutputToContain('Total changes:')
            ->assertExitCode(0);

        $this->assertDatabaseHas(SiteSetting::class, ['key' => 'site_name', 'value' => 'Golden Eye']);
        $this->assertDatabaseHas(CourseCategory::class, ['name' => 'IELTS, PTE and Language Preparation']);
        $this->assertDatabaseHas(Course::class, ['instructor' => 'Academic Support Team']);
        $this->assertDatabaseHas(FAQ::class, ['question' => 'What courses does Golden Eye Academy offer?']);
        $this->assertDatabaseHas(Contact::class, ['id' => $contact->id, 'subject' => 'Original contact subject']);

        $this->artisan('goldeneye:normalize-brand-copy')
            ->expectsOutputToContain('Total changes: 0')
            ->assertExitCode(0);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function jsonLdNodes(string $html): array
    {
        preg_match_all('/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches);

        $nodes = [];

        foreach ($matches[1] as $json) {
            $decoded = json_decode(html_entity_decode(trim($json)), true);

            $this->assertIsArray($decoded);

            if (isset($decoded['@graph']) && is_array($decoded['@graph'])) {
                foreach ($decoded['@graph'] as $graphNode) {
                    if (is_array($graphNode)) {
                        $nodes[] = $graphNode;
                    }
                }

                continue;
            }

            $nodes[] = $decoded;
        }

        return $nodes;
    }
}
