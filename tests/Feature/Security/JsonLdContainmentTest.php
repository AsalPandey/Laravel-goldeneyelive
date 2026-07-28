<?php

namespace Tests\Feature\Security;

use App\Models\BlogPost;
use App\Models\SiteSetting;
use App\Support\CmsContentSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JsonLdContainmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_json_ld_with_a_script_closing_sequence_is_encoded_safely(): void
    {
        $value = '</script><script id="phase-one-injected">window.phaseOneInjected=true</script>';
        $json = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Thing',
            'name' => $value,
        ], JSON_THROW_ON_ERROR);

        $sanitized = CmsContentSanitizer::jsonLd($json);

        $this->assertNotNull($sanitized);
        $this->assertStringNotContainsString('</script', $sanitized);
        $this->assertStringContainsString('\u003C\/script\u003E', $sanitized);
        $this->assertSame($value, json_decode($sanitized, true, 512, JSON_THROW_ON_ERROR)['name']);
    }

    public function test_rendered_cms_json_ld_cannot_create_a_second_script_element(): void
    {
        cache()->flush();

        SiteSetting::create([
            'key' => 'schema_markup',
            'value' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Thing',
                'name' => '</script><script id="phase-one-injected">window.phaseOneInjected=true</script>',
            ], JSON_THROW_ON_ERROR),
            'type' => 'text',
        ]);

        $content = $this->get(route('home'))
            ->assertOk()
            ->content();

        $this->assertStringNotContainsString('<script id="phase-one-injected">', $content);
        $this->assertStringNotContainsString('window.phaseOneInjected=true</script>', $content);
        $this->assertStringContainsString('\u003C\/script\u003E', $content);
    }

    public function test_blog_cms_schema_is_emitted_once(): void
    {
        $post = BlogPost::factory()->create([
            'status' => 'published',
            'schema_markup' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'phaseMarker' => 'single-emission',
            ], JSON_THROW_ON_ERROR),
        ]);

        $content = $this->get(route('blog-detail', $post->slug))
            ->assertOk()
            ->content();

        $this->assertSame(1, substr_count($content, '"phaseMarker":"single-emission"'));
    }
}
