<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Support\CmsContentSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsSanitizerTest extends TestCase
{
    use RefreshDatabase;

    public function test_safe_cms_rich_text_structure_and_links_survive(): void
    {
        $html = <<<'HTML'
<h2>Choose a course</h2>
<h3>Compare your options</h3>
<h4>Plan your next step</h4>
<p>पहिले आफ्नो लक्ष्य बुझ्नुहोस्।<br><strong>Compare</strong> the <em>current options</em> with <b>clear facts</b> and <i>current details</i>.</p>
<ul><li>Course focus</li><li>Current batch</li></ul>
<ol><li>Read the course page</li><li>Ask a useful question</li></ol>
<blockquote>Choose from clear information.</blockquote>
<p><a href="/courses-all">View courses</a> or <a href="/join-now?source_page=blog">Ask for Course Help</a>.</p>
<p><a href="https://goldeneye.edu.np/about" target="_blank">Academy information</a> · <a href="mailto:info@goldeneye.edu.np">Email</a> · <a href="tel:+97761555555">Call</a></p>
HTML;

        $sanitized = CmsContentSanitizer::html($html);

        $this->assertStringContainsString('<h2>Choose a course</h2>', $sanitized);
        $this->assertStringContainsString('<h3>Compare your options</h3>', $sanitized);
        $this->assertStringContainsString('<h4>Plan your next step</h4>', $sanitized);
        $this->assertStringContainsString('पहिले आफ्नो लक्ष्य बुझ्नुहोस्।', html_entity_decode($sanitized, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $this->assertStringContainsString('<br><strong>Compare</strong> the <em>current options</em> with <b>clear facts</b> and <i>current details</i>.</p>', $sanitized);
        $this->assertStringContainsString('<ul><li>Course focus</li><li>Current batch</li></ul>', $sanitized);
        $this->assertStringContainsString('<ol><li>Read the course page</li><li>Ask a useful question</li></ol>', $sanitized);
        $this->assertStringContainsString('<blockquote>Choose from clear information.</blockquote>', $sanitized);
        $this->assertStringContainsString('href="/courses-all"', $sanitized);
        $this->assertStringContainsString('href="/join-now?source_page=blog"', $sanitized);
        $this->assertStringContainsString('href="https://goldeneye.edu.np/about"', $sanitized);
        $this->assertStringContainsString('href="mailto:info@goldeneye.edu.np"', $sanitized);
        $this->assertStringContainsString('href="tel:+97761555555"', $sanitized);
        $this->assertStringContainsString('rel="noopener noreferrer"', $sanitized);
        $this->assertStringContainsString("</p>\n<p>", $sanitized);
        $this->assertStringContainsString("details</i>.</p>\n<ul>", $sanitized);
    }

    public function test_dangerous_elements_attributes_and_protocols_are_rejected(): void
    {
        $html = <<<'HTML'
<p id="unsafe" style="color:red" onclick="alert(1)">Safe text</p>
<script id="cms-root">window.injected = true;</script>
<style>body { display: none; }</style>
<iframe src="https://example.com">frame fallback</iframe>
<object data="https://example.com">object fallback</object>
<a href="java&#x0A;script:alert(1)" onmouseover="alert(1)">Bad link</a>
<a href="data:text/html,&lt;script&gt;alert(1)&lt;/script&gt;">Data link</a>
<a href="http://example.com">Insecure external link</a>
<a href="//example.com">Protocol-relative link</a>
<img src="data:image/svg+xml,&lt;svg onload='alert(1)'&gt;" onerror="alert(1)">
</div><script>window.breakout = true;</script><div>
HTML;

        $sanitized = CmsContentSanitizer::html($html);

        $this->assertStringContainsString('<p>Safe text</p>', $sanitized);
        $this->assertStringNotContainsString('<script', $sanitized);
        $this->assertStringNotContainsString('window.injected', $sanitized);
        $this->assertStringNotContainsString('<style', $sanitized);
        $this->assertStringNotContainsString('display: none', $sanitized);
        $this->assertStringNotContainsString('<iframe', $sanitized);
        $this->assertStringNotContainsString('frame fallback', $sanitized);
        $this->assertStringNotContainsString('<object', $sanitized);
        $this->assertStringNotContainsString('object fallback', $sanitized);
        $this->assertStringNotContainsString('onclick=', $sanitized);
        $this->assertStringNotContainsString('onmouseover=', $sanitized);
        $this->assertStringNotContainsString('onerror=', $sanitized);
        $this->assertStringNotContainsString('style=', $sanitized);
        $this->assertStringNotContainsString('id=', $sanitized);
        $this->assertStringNotContainsString('href="java', $sanitized);
        $this->assertStringNotContainsString('href="data:', $sanitized);
        $this->assertStringNotContainsString('href="http:', $sanitized);
        $this->assertStringNotContainsString('href="//', $sanitized);
        $this->assertStringNotContainsString('src="data:', $sanitized);
        $this->assertStringNotContainsString('window.breakout', $sanitized);
    }

    public function test_malformed_cms_html_fails_closed_without_concatenating_safe_blocks(): void
    {
        $sanitized = CmsContentSanitizer::html(
            '<p>First block</p><unsupported><strong>Second block<script>alert(1)</script></strong></unsupported><p>नेपाली पाठ',
        );

        $this->assertStringContainsString('<p>First block</p>', $sanitized);
        $this->assertStringContainsString('<strong>Second block</strong>', $sanitized);
        $this->assertStringContainsString('<p>नेपाली पाठ</p>', $sanitized);
        $this->assertStringNotContainsString('<unsupported', $sanitized);
        $this->assertStringNotContainsString('<script', $sanitized);
        $this->assertStringNotContainsString('alert(1)', $sanitized);
        $this->assertStringContainsString('</p><strong>', $sanitized);
        $this->assertStringContainsString('</strong><p>', $sanitized);
    }

    public function test_public_cms_html_removes_dangerous_attributes_and_urls(): void
    {
        cache()->flush();

        SiteSetting::create([
            'key' => 'about_content',
            'value' => '<p onclick="alert(1)">Safe <strong>copy</strong></p><a href="javascript:alert(1)">bad link</a><img src="javascript:alert(1)" onerror="alert(1)">',
            'type' => 'text',
        ]);

        $response = $this->get(route('about'));

        $response->assertOk();
        $response->assertSeeText('Safe copy');
        $response->assertDontSee('onclick="alert(1)"', false);
        $response->assertDontSee('javascript:alert(1)', false);
        $response->assertDontSee('onerror="alert(1)"', false);
    }

    public function test_invalid_cms_schema_markup_is_not_rendered(): void
    {
        cache()->flush();

        SiteSetting::create([
            'key' => 'schema_markup',
            'value' => '<script>alert(1)</script>',
            'type' => 'text',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
