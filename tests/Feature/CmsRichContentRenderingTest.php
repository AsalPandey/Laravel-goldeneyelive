<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\FAQ;
use App\Models\SiteSetting;
use App\Support\GoldenEyeArticleBaseline;
use Database\Seeders\LiveSiteSeeder;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsRichContentRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_eight_approved_articles_render_their_structured_content_and_safe_links(): void
    {
        $this->seed(LiveSiteSeeder::class);
        cache()->flush();

        $expectedArticles = collect(GoldenEyeArticleBaseline::articles())->keyBy('slug');
        $originalRecords = BlogPost::query()
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'status', 'published_at', 'author', 'content'])
            ->map->getAttributes()
            ->all();

        $this->assertCount(8, $originalRecords);

        foreach (BlogPost::query()->orderBy('id')->get() as $post) {
            $expected = $expectedArticles->get($post->slug);
            $this->assertNotNull($expected, $post->slug);

            $response = $this->get(route('blog-detail', $post->slug))->assertOk();
            $xpath = $this->xpath($response->content());
            $content = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " blog-content ")]')?->item(0);

            $this->assertInstanceOf(DOMElement::class, $content, $post->slug);
            $this->assertSame($this->tagCount($post->content, 'h2'), $xpath->query('.//h2', $content)?->length, $post->slug);
            $this->assertSame($this->tagCount($post->content, 'h3'), $xpath->query('.//h3', $content)?->length, $post->slug);
            $this->assertSame($this->tagCount($post->content, 'p'), $xpath->query('.//p', $content)?->length, $post->slug);
            $this->assertSame($this->tagCount($post->content, 'ul'), $xpath->query('.//ul', $content)?->length, $post->slug);
            $this->assertSame($this->tagCount($post->content, 'ol'), $xpath->query('.//ol', $content)?->length, $post->slug);
            $this->assertSame($this->tagCount($post->content, 'li'), $xpath->query('.//li', $content)?->length, $post->slug);
            $this->assertSame($this->tagCount($post->content, 'a'), $xpath->query('.//a', $content)?->length, $post->slug);
            $this->assertGreaterThan(0, $xpath->query('.//h2', $content)?->length, $post->slug);
            $this->assertGreaterThan(0, $xpath->query('.//p', $content)?->length, $post->slug);
            $this->assertGreaterThan(0, $xpath->query('.//li', $content)?->length, $post->slug);
            $this->assertGreaterThan(0, $xpath->query('.//a[starts-with(@href, "/")]', $content)?->length, $post->slug);
            $this->assertSame(1, $xpath->query('.//a[contains(normalize-space(.), "Ask for Course Help")]', $content)?->length, $post->slug);
            $this->assertSame(0, $xpath->query('.//script|.//style|.//iframe|.//object|.//embed', $content)?->length, $post->slug);
            $this->assertSame('Golden Eye Academy', $post->author);
            $this->assertSame('published', $post->status);
            $this->assertStringNotContainsString('Brilliant', $content->textContent, $post->slug);
            $this->assertValidArticleSchema($xpath, $post->slug);
        }

        $currentRecords = BlogPost::query()
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'status', 'published_at', 'author', 'content'])
            ->map->getAttributes()
            ->all();

        $this->assertSame($originalRecords, $currentRecords);
    }

    public function test_rich_content_surfaces_preserve_blocks_lists_links_and_unicode(): void
    {
        $this->seed(LiveSiteSeeder::class);

        $surfaceMarkup = [
            'about_page_content' => '<h2>About structure</h2><p>पहिलो अनुच्छेद।</p><p>Second paragraph.</p><ul><li>About item</li></ul><a href="/courses-all">About courses</a>',
            'contact_page_content' => '<h3>Contact structure</h3><p>Contact paragraph one.</p><p>Contact paragraph two.</p><ul><li>Contact item</li></ul><a href="/join-now">Contact Course Help</a>',
            'privacy_policy_content' => '<h2>Privacy structure</h2><p>Privacy paragraph one.</p><p>Privacy paragraph two.</p><ul><li>Privacy item</li></ul><a href="/contact">Privacy contact</a>',
            'terms_and_conditions_content' => '<h2>Terms structure</h2><p>Terms paragraph one.</p><p>Terms paragraph two.</p><ol><li>Terms item</li></ol><a href="/contact">Terms contact</a>',
            'faq_page_content' => '<h2>FAQ structure</h2><p>FAQ introduction one.</p><p>FAQ introduction two.</p><ul><li>FAQ introduction item</li></ul><a href="/courses-all">FAQ courses</a>',
        ];

        foreach ($surfaceMarkup as $key => $value) {
            SiteSetting::query()->where('key', $key)->update(['value' => $value]);
        }

        $faq = FAQ::query()->orderBy('order_priority')->firstOrFail();
        $faq->update([
            'answer' => '<p>FAQ answer one.</p><p>FAQ answer two.</p><ul><li>FAQ answer item</li></ul><a href="/join-now">FAQ Course Help</a>',
        ]);

        cache()->flush();

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('<h2>About structure</h2><p>पहिलो अनुच्छेद।</p><p>Second paragraph.</p><ul><li>About item</li></ul><a href="/courses-all" rel="noopener noreferrer">About courses</a>', false);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('<h3>Contact structure</h3><p>Contact paragraph one.</p><p>Contact paragraph two.</p><ul><li>Contact item</li></ul><a href="/join-now" rel="noopener noreferrer">Contact Course Help</a>', false);

        $this->get(route('privacy-policy'))
            ->assertOk()
            ->assertSee('<h2>Privacy structure</h2><p>Privacy paragraph one.</p><p>Privacy paragraph two.</p><ul><li>Privacy item</li></ul><a href="/contact" rel="noopener noreferrer">Privacy contact</a>', false);

        $this->get(route('terms-and-conditions'))
            ->assertOk()
            ->assertSee('<h2>Terms structure</h2><p>Terms paragraph one.</p><p>Terms paragraph two.</p><ol><li>Terms item</li></ol><a href="/contact" rel="noopener noreferrer">Terms contact</a>', false);

        $this->get(route('faq'))
            ->assertOk()
            ->assertSee('<h2>FAQ structure</h2><p>FAQ introduction one.</p><p>FAQ introduction two.</p><ul><li>FAQ introduction item</li></ul><a href="/courses-all" rel="noopener noreferrer">FAQ courses</a>', false)
            ->assertSee('<p>FAQ answer one.</p><p>FAQ answer two.</p><ul><li>FAQ answer item</li></ul><a href="/join-now" rel="noopener noreferrer">FAQ Course Help</a>', false);
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return new DOMXPath($document);
    }

    private function tagCount(string $html, string $tag): int
    {
        preg_match_all('/<'.preg_quote($tag, '/').'\b/i', $html, $matches);

        return count($matches[0]);
    }

    private function assertValidArticleSchema(DOMXPath $xpath, string $slug): void
    {
        $schemas = $xpath->query('//script[@type="application/ld+json"]');
        $matched = false;

        foreach ($schemas ?: [] as $schema) {
            $decoded = json_decode($schema->textContent, true);

            if (is_array($decoded) && ($decoded['@type'] ?? null) === 'BlogPosting') {
                $this->assertSame('Golden Eye Academy', $decoded['author']['name'] ?? null, $slug);
                $matched = true;
            }
        }

        $this->assertTrue($matched, $slug.' must contain valid BlogPosting JSON-LD.');
    }
}
