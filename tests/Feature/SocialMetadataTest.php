<?php

namespace Tests\Feature;

use App\Support\SocialImage;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class SocialMetadataTest extends TestCase
{
    public function test_existing_social_preview_assertions_accept_versioned_homepage_images(): void
    {
        config(['app.url' => 'https://goldeneye.edu.np']);
        $image = SocialImage::resolve('site/img/carousel-1.png');
        $response = TestResponse::fromBaseResponse(response(
            '<meta property="og:url" content="https://goldeneye.edu.np/">'
            .'<meta property="og:image" content="'.$image['url'].'">'
            .'<meta name="twitter:image" content="'.$image['url'].'">',
        ));
        $assertion = new \ReflectionMethod(SocialPreviewRefinementTest::class, 'assertSocialImage');
        $assertion->invoke(new SocialPreviewRefinementTest(__FUNCTION__), $response, 'site/img/carousel-1.png');
    }

    public function test_homepage_metadata_preserves_copy_and_canonical_with_complete_image_details(): void
    {
        config(['app.url' => 'https://goldeneye.edu.np']);
        $request = Request::create('https://goldeneye.edu.np/?share=test');
        $request->setRouteResolver(fn () => (new Route('GET', '/', fn () => ''))->name('home'));
        $this->app->instance('request', $request);
        cache()->put('site_shared_data', ['settings' => [
            'meta_title' => 'Golden Eye Academy | Approved title',
            'meta_description' => 'Approved description & details.',
            'hero_image' => 'site/img/carousel-1.png',
        ]]);

        $image = SocialImage::resolve('site/img/carousel-1.png');
        $this->view('site.layout.header')
            ->assertSee('property="og:title" content="Golden Eye Academy | Approved title"', false)
            ->assertSee('property="og:description" content="Approved description &amp; details."', false)
            ->assertSee('property="og:image" content="'.$image['url'].'"', false)
            ->assertSee('property="og:image:secure_url" content="'.$image['url'].'"', false)
            ->assertSee('property="og:image:type" content="image/png"', false)
            ->assertSee('property="og:image:width" content="1001"', false)
            ->assertSee('property="og:image:height" content="561"', false)
            ->assertSee('property="og:image:alt" content="Golden Eye Academy"', false)
            ->assertSee('name="twitter:image" content="'.$image['url'].'"', false)
            ->assertSee('rel="canonical" href="https://goldeneye.edu.np/"', false)
            ->assertSee('rel="apple-touch-icon"', false);
    }

    public function test_social_image_priority_and_invalid_candidates(): void
    {
        config(['app.url' => 'https://goldeneye.edu.np']);
        $preferred = SocialImage::resolve('site/img/premium.png', 'site/img/carousel-1.png');
        $this->assertStringContainsString('/site/img/premium.png?v=', $preferred['url']);
        $fallback = SocialImage::resolve();
        foreach ([null, '', 'missing.png', 'favicon.svg', 'favicon.ico', 'apple-touch-icon.png', '../.env', 'https://invalid.example/image.png', 'site/css/style.css'] as $invalid) {
            $this->assertSame($fallback, SocialImage::resolve($invalid, 'site/img/carousel-1.png'));
        }
        $this->assertSame($fallback, SocialImage::resolve('https://goldeneye.edu.np/site/img/carousel-1.png?old=1'));
        $this->assertStringEndsWith('?v='.substr(hash_file('sha256', public_path('site/img/carousel-1.png')), 0, 12), $fallback['url']);
    }

    public function test_public_icons_are_derived_from_the_approved_logo(): void
    {
        $svg = file_get_contents(public_path('favicon.svg'));
        $this->assertStringContainsString('<title>Golden Eye Academy</title>', $svg);
        $this->assertStringNotContainsString('#FF2D20', $svg);
        preg_match('/base64,([^" ]+)/', $svg, $matches);
        $this->assertSame(file_get_contents(public_path('apple-touch-icon.png')), base64_decode($matches[1]));
        $this->assertSame([180, 180], array_slice(getimagesize(public_path('apple-touch-icon.png')), 0, 2));
        $this->assertSame("\x00\x00\x01\x00", substr(file_get_contents(public_path('favicon.ico')), 0, 4));
    }
}
