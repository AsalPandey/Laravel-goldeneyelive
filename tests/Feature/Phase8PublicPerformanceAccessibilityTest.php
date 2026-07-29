<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\Notice;
use App\Models\Teacher;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase8PublicPerformanceAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_public_controls_and_landmarks_have_accessible_names_and_heading_semantics(): void
    {
        $response = $this->get(route('home'))->assertOk();
        $xpath = $this->xpath($response->getContent());

        $this->assertSame(0, $xpath->query('//nav//h1 | //nav//h2 | //nav//h3 | //nav//h4 | //nav//h5 | //nav//h6')->length);

        $newsletterInput = $xpath->query('//*[@id="newsletter_email"]')->item(0);
        $newsletterLabel = $xpath->query('//label[@for="newsletter_email"]')->item(0);
        $backToTop = $xpath->query('//a[contains(concat(" ", normalize-space(@class), " "), " back-to-top ")]')->item(0);

        $this->assertInstanceOf(DOMElement::class, $newsletterInput);
        $this->assertInstanceOf(DOMElement::class, $newsletterLabel);
        $this->assertSame('Email address for academy updates', trim($newsletterLabel->textContent));
        $this->assertInstanceOf(DOMElement::class, $backToTop);
        $this->assertSame('Back to top', $backToTop->getAttribute('aria-label'));
        $this->assertSame('true', $xpath->query('.//i', $backToTop)->item(0)?->attributes?->getNamedItem('aria-hidden')?->nodeValue);
    }

    public function test_campaign_popup_has_an_accessible_name_and_description_without_changing_its_controls(): void
    {
        Notice::factory()->create([
            'title' => 'Accessible Course Notice',
            'subtitle' => 'Ask about current course timing before enrollment.',
            'display_type' => 'popup',
            'status' => 'active',
        ]);
        cache()->forget('site_shared_data');

        $response = $this->get(route('home'))->assertOk();
        $xpath = $this->xpath($response->getContent());
        $popup = $xpath->query('//*[@id="siteNoticePopup"]')->item(0);

        $this->assertInstanceOf(DOMElement::class, $popup);
        $this->assertSame('siteNoticePopupTitle', $popup->getAttribute('aria-labelledby'));
        $this->assertSame('siteNoticePopupDescription', $popup->getAttribute('aria-describedby'));
        $this->assertSame('Accessible Course Notice', trim($xpath->query('//*[@id="siteNoticePopupTitle"]')->item(0)?->textContent ?? ''));
        $this->assertSame('Ask about current course timing before enrollment.', trim($xpath->query('//*[@id="siteNoticePopupDescription"]')->item(0)?->textContent ?? ''));
        $this->assertSame(2, $xpath->query('//*[@id="siteNoticePopup"]//button[@data-bs-dismiss="modal"]')->length);
        $response
            ->assertSee('openSiteNoticePopup', false)
            ->assertSee('popupReturnFocus = options.link instanceof HTMLElement ? options.link : null', false)
            ->assertSee("addEventListener('hidden.bs.modal'", false)
            ->assertSee('popupReturnFocus.focus()', false)
            ->assertSee('data-notice-id="notice_', false);
    }

    public function test_contact_form_preserves_the_selected_pathway_and_map_is_named_and_lazy_loaded(): void
    {
        $response = $this
            ->withSession(['_old_input' => ['subject' => 'General Inquiry']])
            ->get(route('contact'))
            ->assertOk();
        $xpath = $this->xpath($response->getContent());
        $selectedOptions = $xpath->query('//*[@id="subject"]/option[@selected]');
        $map = $xpath->query('//iframe[contains(@src, "google.com/maps")]')->item(0);

        $this->assertSame(1, $selectedOptions->length);
        $this->assertSame('General Inquiry', $selectedOptions->item(0)?->attributes?->getNamedItem('value')?->nodeValue);
        $this->assertInstanceOf(DOMElement::class, $map);
        $this->assertSame('Map showing Golden Eye Academy in Pokhara', $map->getAttribute('title'));
        $this->assertSame('lazy', $map->getAttribute('loading'));
    }

    public function test_public_card_images_are_lazy_and_article_hero_image_is_prioritized_with_dimensions(): void
    {
        $category = CourseCategory::factory()->create();
        Course::factory()->create([
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
        ]);
        $post = BlogPost::factory()->published()->create();
        cache()->forget('site_shared_data');

        foreach ([route('catalogue'), route('blog')] as $url) {
            $xpath = $this->xpath($this->get($url)->assertOk()->getContent());
            $images = $xpath->query('//main//img');

            $this->assertGreaterThan(0, $images->length);

            foreach ($images as $image) {
                $this->assertSame('lazy', $image->getAttribute('loading'));
                $this->assertSame('async', $image->getAttribute('decoding'));
                $this->assertNotSame('', $image->getAttribute('width'));
                $this->assertNotSame('', $image->getAttribute('height'));
            }
        }

        $articleResponse = $this->get(route('blog-detail', $post->slug))->assertOk();
        $articleXPath = $this->xpath($articleResponse->getContent());
        $articleImage = $articleXPath->query('//main//article/img')->item(0);
        $preload = $articleXPath->query('//head/link[@rel="preload"][@as="image"][@fetchpriority="high"]')->item(0);

        $this->assertInstanceOf(DOMElement::class, $articleImage);
        $this->assertSame('eager', $articleImage->getAttribute('loading'));
        $this->assertSame('async', $articleImage->getAttribute('decoding'));
        $this->assertSame('high', $articleImage->getAttribute('fetchpriority'));
        $this->assertSame('1200', $articleImage->getAttribute('width'));
        $this->assertSame('675', $articleImage->getAttribute('height'));
        $this->assertInstanceOf(DOMElement::class, $preload);
    }

    public function test_audience_hero_is_preloaded_and_long_cms_badges_have_a_narrow_viewport_wrap_guard(): void
    {
        $response = $this->get(route('study-abroad-guidance'))->assertOk();
        $xpath = $this->xpath($response->getContent());

        $this->assertSame(1, $xpath->query('//head/link[@rel="preload"][@as="image"][@fetchpriority="high"]')->length);
        $this->assertSame(1, $xpath->query('//main//*[contains(concat(" ", normalize-space(@class), " "), " responsive-cms-badge ")]')->length);

        $css = file_get_contents(public_path('site/css/style.css'));

        $this->assertStringContainsString('.responsive-cms-badge', $css);
        $this->assertStringContainsString('white-space: normal', $css);
        $this->assertStringContainsString('overflow-wrap: anywhere', $css);
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
        $this->assertStringContainsString('animation-duration: 0.01ms', $css);
        $this->assertStringContainsString('.home-hero-copy .hero-hook-body', $css);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee("const actionSelector = 'main [data-cta], main button[type=\"submit\"], main input[type=\"submit\"]'", false)
            ->assertSee('scheduleWhatsappPositionUpdate', false)
            ->assertSee('container.style.transform = `translateY(', false)
            ->assertDontSee('is-hidden-over-hero', false);
    }

    public function test_representative_public_pages_have_one_h1_and_do_not_skip_heading_levels(): void
    {
        $category = CourseCategory::factory()->create();
        $course = Course::factory()->create([
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
        ]);
        $post = BlogPost::factory()->published()->create();
        Teacher::factory()->create();
        FAQ::factory()->count(12)->create();
        cache()->forget('site_shared_data');

        $urls = [
            route('home'),
            route('about'),
            route('catalogue'),
            route('courses-all'),
            route('courses-detail', $course->slug),
            route('blog'),
            route('blog-detail', $post->slug),
            route('faq'),
            route('contact'),
            route('join-now'),
            route('for-students'),
            route('for-parents'),
            route('study-abroad-guidance'),
            route('job-computer-skills'),
            url('/phase-8-missing-page'),
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);
            $this->assertContains($response->getStatusCode(), [200, 404], $url);

            $headings = $this->xpath($response->getContent())->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');
            $this->assertSame(1, $this->xpath($response->getContent())->query('//h1')->length, $url);

            $previousLevel = null;

            foreach ($headings as $heading) {
                $currentLevel = (int) substr($heading->nodeName, 1);

                if ($previousLevel !== null) {
                    $this->assertLessThanOrEqual(
                        $previousLevel + 1,
                        $currentLevel,
                        sprintf('%s skips from h%d to h%d at "%s".', $url, $previousLevel, $currentLevel, trim($heading->textContent)),
                    );
                }

                $previousLevel = $currentLevel;
            }
        }
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument;
        @$document->loadHTML($html);

        return new DOMXPath($document);
    }
}
