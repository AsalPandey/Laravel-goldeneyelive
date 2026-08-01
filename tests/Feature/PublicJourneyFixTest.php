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
            ->assertSee('<details class="site-mobile-course-menu"', false)
            ->assertSee('<summary class="nav-item nav-link', false)
            ->assertSee('site-mobile-course-options', false)
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

    public function test_mobile_course_categories_are_grouped_in_a_dropdown(): void
    {
        $css = file_get_contents(public_path('site/css/style.css'));

        $this->assertStringContainsString('.site-mobile-course-menu summary', $css);
        $this->assertStringContainsString('.site-mobile-course-menu[open] summary .fa-chevron-down', $css);
        $this->assertStringContainsString('.site-mobile-course-options', $css);
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

    public function test_faq_reveal_control_uses_cms_labels_and_accessible_native_button_markup(): void
    {
        SiteSetting::insert([
            [
                'key' => 'faq_btn_text',
                'value' => 'Reveal the Remaining Answers',
                'type' => 'text',
            ],
            [
                'key' => 'faq_btn_text_expanded',
                'value' => 'Hide the Additional Answers',
                'type' => 'text',
            ],
        ]);
        FAQ::factory()->count(12)->create(['status' => 'active']);
        cache()->forget('site_shared_data');

        $response = $this->get(route('faq'))
            ->assertOk()
            ->assertSee('id="additionalFaqs" hidden', false)
            ->assertSee('readMoreButton.addEventListener(\'click\'', false)
            ->assertSee('expanded ? expandedLabel : collapsedLabel', false)
            ->assertDontSee('onclick="toggleFAQs()"', false);

        $xpath = $this->xpath($response->getContent());
        $button = $xpath->query('//*[@id="readMoreBtn"]')->item(0);

        $this->assertNotNull($button);
        $this->assertSame('button', $button->nodeName);
        $this->assertSame('false', $button->attributes->getNamedItem('aria-expanded')?->nodeValue);
        $this->assertSame('additionalFaqs', $button->attributes->getNamedItem('aria-controls')?->nodeValue);
        $this->assertSame('Reveal the Remaining Answers', $button->attributes->getNamedItem('data-collapsed-label')?->nodeValue);
        $this->assertSame('Hide the Additional Answers', $button->attributes->getNamedItem('data-expanded-label')?->nodeValue);
        $this->assertSame('Reveal the Remaining Answers', trim($xpath->query('./span', $button)->item(0)?->textContent ?? ''));
    }

    public function test_faq_reveal_control_uses_safe_defaults_when_cms_labels_are_empty(): void
    {
        SiteSetting::insert([
            ['key' => 'faq_btn_text', 'value' => '', 'type' => 'text'],
            ['key' => 'faq_btn_text_expanded', 'value' => '', 'type' => 'text'],
        ]);
        FAQ::factory()->count(11)->create(['status' => 'active']);
        cache()->forget('site_shared_data');

        $xpath = $this->xpath($this->get(route('faq'))->assertOk()->getContent());
        $button = $xpath->query('//*[@id="readMoreBtn"]')->item(0);

        $this->assertNotNull($button);
        $this->assertSame('Show More FAQs', $button->attributes->getNamedItem('data-collapsed-label')?->nodeValue);
        $this->assertSame('Show Fewer FAQs', $button->attributes->getNamedItem('data-expanded-label')?->nodeValue);
        $this->assertSame('Show More FAQs', trim($xpath->query('./span', $button)->item(0)?->textContent ?? ''));
    }

    public function test_public_accordions_and_faq_icons_have_visible_expanded_states(): void
    {
        $css = file_get_contents(public_path('site/css/style.css'));

        $this->assertMatchesRegularExpression(
            '/\.accordion\s+\.accordion-collapse\s*\{[^}]*visibility:\s*visible;/s',
            $css,
        );
        $this->assertStringContainsString('.faq-premium-btn:not(.collapsed) .fa-plus::before', $css);
        $this->assertStringContainsString('#readMoreBtn.active .fa-chevron-down', $css);
    }

    public function test_policy_fallback_accordions_have_valid_second_heading_references(): void
    {
        foreach (['privacyPolicy.blade.php', 'termsAndConditions.blade.php'] as $view) {
            $contents = file_get_contents(resource_path("views/site/others/{$view}"));

            $this->assertStringContainsString('<h2 class="accordion-header" id="heading2">', $contents);
            $this->assertStringContainsString('aria-labelledby="heading2"', $contents);
        }
    }

    public function test_faq_reveal_labels_do_not_change_public_status_or_priority_ordering(): void
    {
        FAQ::factory()->create([
            'question' => 'First visible priority question',
            'status' => 'active',
            'order_priority' => 1,
        ]);
        FAQ::factory()->create([
            'question' => 'Second visible priority question',
            'status' => 'active',
            'order_priority' => 2,
        ]);
        FAQ::factory()->count(9)->create([
            'status' => 'active',
            'order_priority' => 10,
        ]);
        FAQ::factory()->create([
            'question' => 'Inactive private question',
            'status' => 'inactive',
            'order_priority' => 0,
        ]);

        $response = $this->get(route('faq'))
            ->assertOk()
            ->assertSee('First visible priority question')
            ->assertSee('Second visible priority question')
            ->assertDontSee('Inactive private question');
        $html = $response->getContent();

        $this->assertLessThan(
            strpos($html, 'Second visible priority question'),
            strpos($html, 'First visible priority question'),
        );
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
