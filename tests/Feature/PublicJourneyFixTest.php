<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\SiteSetting;
use App\Models\Teacher;
use App\Support\ContactPhones;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicJourneyFixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        cache()->flush();
    }

    public function test_catalogue_is_canonical_and_legacy_misspelling_redirects_permanently_without_a_loop(): void
    {
        $this->assertSame(url('/catalogue'), route('catalogue'));

        $this->get('/catalogue')->assertOk();

        $this->get('/catelogue')
            ->assertStatus(301)
            ->assertRedirect(route('catalogue'));
    }

    public function test_desktop_and_mobile_navigation_include_each_essential_destination_and_current_course_categories(): void
    {
        $category = CourseCategory::factory()->create([
            'name' => 'Current Language Courses',
            'slug' => 'current-language-courses',
            'status' => 'active',
        ]);
        Course::factory()->create([
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
            'status' => 'active',
        ]);

        cache()->forget('site_shared_data');

        $response = $this->get(route('home'))
            ->assertOk()
            ->assertSee('aria-label="Primary navigation"', false)
            ->assertSee('aria-label="Open navigation menu"', false)
            ->assertSee('data-bs-target="#navbarCollapse"', false)
            ->assertSee(route('courses-all'), false)
            ->assertSee(route('about'), false)
            ->assertSee(route('blog'), false)
            ->assertSee(route('faq'), false)
            ->assertSee(route('contact'), false)
            ->assertSee('data-cta="mobile-menu-course-help"', false)
            ->assertSee('data-cta="mobile-menu-whatsapp"', false)
            ->assertSee('if (window.bootstrap)', false)
            ->assertSee('navigation.classList.toggle(\'show\')', false)
            ->assertSee('event.key !== \'Escape\'', false);

        $categoryUrl = e(route('courses-all', ['category' => $category->slug]));

        $this->assertGreaterThanOrEqual(2, substr_count($response->getContent(), $categoryUrl));
    }

    public function test_multiple_cms_phone_numbers_render_as_separate_correct_actions(): void
    {
        SiteSetting::create([
            'key' => 'site_phone',
            'value' => '061-572599, +977 9856058599; 9800000001',
            'type' => 'text',
        ]);
        cache()->forget('site_shared_data');

        $response = $this->get(route('contact'))->assertOk();
        $phoneLinks = $this->linksWithScheme($response->getContent(), 'tel:');

        $this->assertContains(['href' => 'tel:061572599', 'text' => '061-572599'], $phoneLinks);
        $this->assertContains(['href' => 'tel:+9779856058599', 'text' => '+977 9856058599'], $phoneLinks);
        $this->assertContains(['href' => 'tel:9800000001', 'text' => '9800000001'], $phoneLinks);
    }

    public function test_one_cms_phone_number_keeps_one_number_behavior(): void
    {
        $this->assertSame([
            ['display' => '061-572599', 'href' => '061572599'],
        ], ContactPhones::parse('061-572599'));
    }

    public function test_whatsapp_uses_the_authoritative_number_and_is_not_hidden_over_the_home_hero(): void
    {
        SiteSetting::create([
            'key' => 'whatsapp_number',
            'value' => '+977 980-111-2222',
            'type' => 'text',
        ]);
        SiteSetting::create([
            'key' => 'whatsapp_prefill_message',
            'value' => 'Please help me choose a current course.',
            'type' => 'text',
        ]);
        cache()->forget('site_shared_data');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('https://wa.me/9779801112222?text=Please%20help%20me%20choose%20a%20current%20course.', false)
            ->assertDontSee('is-hidden-over-hero', false);

        $css = file_get_contents(public_path('site/css/style.css'));
        $this->assertStringNotContainsString('.whatsapp-btn-container.is-hidden-over-hero', $css);
    }

    public function test_faq_reveal_control_has_accurate_state_and_keyboard_native_markup(): void
    {
        FAQ::factory()->count(12)->create(['status' => 'active']);
        cache()->forget('site_shared_data');

        $this->get(route('faq'))
            ->assertOk()
            ->assertSee('id="additionalFaqs" hidden', false)
            ->assertSee('id="readMoreBtn" type="button" aria-expanded="false" aria-controls="additionalFaqs"', false)
            ->assertSee('<span>Show More FAQs</span>', false)
            ->assertSee('Show Fewer FAQs', false)
            ->assertSee('readMoreButton.addEventListener(\'click\'', false)
            ->assertDontSee('onclick="toggleFAQs()"', false);
    }

    public function test_404_has_one_heading_an_accurate_title_and_recovery_actions(): void
    {
        $response = $this->get('/missing-phase-three-page')->assertNotFound();
        $html = $response->getContent();
        $xpath = $this->xpath($html);

        $this->assertSame(1, $xpath->query('//h1')->length);
        $this->assertSame('Page Not Found', trim($xpath->query('//h1')->item(0)->textContent));
        $this->assertStringContainsString('<title>Page Not Found - Golden Eye Academy</title>', $html);
        $response
            ->assertSee(route('courses-all'), false)
            ->assertSee(route('contact'), false)
            ->assertSee('https://wa.me/', false);
    }

    public function test_public_layout_has_a_functional_skip_link_and_main_landmark(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<a class="skip-link" href="#main-content">Skip to main content</a>', false)
            ->assertSee('<main id="main-content" tabindex="-1">', false);
    }

    public function test_about_faculty_bio_is_visible_without_hover_only_interaction(): void
    {
        Teacher::factory()->create([
            'name' => 'Asha Gurung',
            'designation' => 'IELTS Faculty',
            'bio' => 'Asha supports learners with practical mock-test feedback.',
            'status' => 'active',
        ]);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Asha supports learners with practical mock-test feedback.', false)
            ->assertSee('<h3 class="h6 mt-2 mb-1', false)
            ->assertDontSee('group-hover:opacity-100', false);
    }

    public function test_narrow_contact_layout_has_scoped_padding_and_overflow_guards(): void
    {
        $css = file_get_contents(public_path('site/css/style.css'));

        $this->assertStringContainsString('@media (max-width: 575.98px)', $css);
        $this->assertStringContainsString('.contact-section > .container', $css);
        $this->assertStringContainsString('.contact-form-card', $css);
        $this->assertStringContainsString('overflow-wrap: anywhere', $css);
        $this->assertStringContainsString('@media (max-width: 339.98px)', $css);
        $this->assertStringContainsString('.recaptcha-field .g-recaptcha', $css);
    }

    /**
     * @return array<int, array{href: string, text: string}>
     */
    private function linksWithScheme(string $html, string $scheme): array
    {
        $links = [];

        foreach ($this->xpath($html)->query('//a[starts-with(@href, "'.$scheme.'")]') as $link) {
            $links[] = [
                'href' => $link->getAttribute('href'),
                'text' => trim($link->textContent),
            ];
        }

        return $links;
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument;
        @$document->loadHTML($html);

        return new DOMXPath($document);
    }
}
