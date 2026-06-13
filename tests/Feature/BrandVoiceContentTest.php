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
            ->assertSee('Established academy in Pokhara since 2008', false)
            ->assertDontSee('Study Abroad, Language &amp; Computer Courses in Pokhara', false)
            ->assertDontSee('GoldenEye Academy', false)
            ->getContent();

        $this->assertStringContainsString('practical classes', $html);
    }

    public function test_international_preparation_page_avoids_consultancy_positioning(): void
    {
        $this->get(route('study-abroad-guidance'))
            ->assertOk()
            ->assertSee('IELTS, PTE and Language Preparation', false)
            ->assertSee('Preparing for international study goals?', false)
            ->assertSee('academic preparation', false)
            ->assertDontSee('Study Abroad Guidance', false)
            ->assertDontSee('documentation preparation', false)
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
            'description' => 'IELTS, PTE, SAT, language, and destination-readiness programs for students planning global education or migration.',
        ]);
        Course::factory()->create([
            'description' => 'Best for students who want to understand their test path before paying for applications or documents.',
            'instructor' => 'Course Help Team',
        ]);
        FAQ::factory()->create([
            'question' => 'What courses does GoldenEye Academy offer?',
            'answer' => 'GoldenEye Academy offers course guidance before enrollment for study abroad applicants.',
        ]);
        ServicePillar::factory()->create([
            'title' => 'Languages and Test Preparation',
            'slug' => 'languages-and-test-preparation',
            'summary' => 'Language, IELTS, PTE, Japanese, Korean, and study-abroad preparation for global education goals.',
            'bullets' => ['Timeline Guidance: Match test prep with destination, application, and intake planning.'],
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
