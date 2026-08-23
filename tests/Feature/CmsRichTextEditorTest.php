<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Tests\TestCase;

class CmsRichTextEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_jodit_is_exactly_pinned_to_the_mit_community_package_without_transitive_dependencies(): void
    {
        $package = json_decode((string) file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR);
        $lock = json_decode((string) file_get_contents(base_path('package-lock.json')), true, flags: JSON_THROW_ON_ERROR);
        $installedPackage = $lock['packages']['node_modules/jodit'];

        $this->assertSame('4.13.23', $package['dependencies']['jodit']);
        $this->assertSame('4.13.23', $installedPackage['version']);
        $this->assertSame('MIT', $installedPackage['license']);
        $this->assertArrayNotHasKey('dependencies', $installedPackage);
        $this->assertSame(
            'sha512-RP1K2S6aGzAeRHOIqB7ZV6Ii3AxIbdMLE8wTQSKmCDCyFNBkpmvIiqN2pOCkQcWSnVyXTCW9GWqDBOrFLrtm4g==',
            $installedPackage['integrity'],
        );
    }

    public function test_every_intended_cms_field_uses_the_shared_editor_marker(): void
    {
        $expectedFields = [
            'resources/views/admin/blog/create.blade.php' => ['content'],
            'resources/views/admin/blog/edit.blade.php' => ['content'],
            'resources/views/admin/faq/create.blade.php' => ['answer'],
            'resources/views/admin/faq/edit.blade.php' => ['answer'],
            'resources/views/admin/courses/create.blade.php' => ['description', 'course_outline'],
            'resources/views/admin/courses/edit.blade.php' => ['description', 'course_outline'],
            'resources/views/admin/teachers/create.blade.php' => ['bio'],
            'resources/views/admin/teachers/edit.blade.php' => ['bio'],
            'resources/views/admin/branding/index.blade.php' => [
                'hero_subtitle',
                'about_content',
                'founder_message',
                'about_page_content',
                'courses_subtitle',
                'blog_subtitle',
                'faq_page_content',
                'contact_page_content',
                'privacy_policy_content',
                'terms_and_conditions_content',
                'footer_about_text',
            ],
        ];

        foreach ($expectedFields as $path => $fieldNames) {
            $blade = (string) file_get_contents(base_path($path));

            foreach ($fieldNames as $fieldName) {
                $this->assertMatchesRegularExpression(
                    '/<textarea(?=[^>]*name="'.preg_quote($fieldName, '/').'")(?=[^>]*data-cms-rich-text)[^>]*>/',
                    $blade,
                    "{$path} must mark {$fieldName} for the shared rich-text editor.",
                );
            }
        }
    }

    public function test_legacy_editor_is_absent_from_cms_source_and_configuration(): void
    {
        $legacyEditorName = 'ck'.'editor';

        foreach (['app', 'config', 'resources', 'tests'] as $directory) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(base_path($directory), \FilesystemIterator::SKIP_DOTS),
            );

            foreach ($iterator as $file) {
                if (! $file->isFile()) {
                    continue;
                }

                $contents = (string) file_get_contents($file->getPathname());
                $this->assertStringNotContainsString($legacyEditorName, strtolower($contents), $file->getPathname());
            }
        }
    }

    public function test_shared_editor_uses_registry_sync_livewire_lifecycle_and_managed_images(): void
    {
        $script = (string) file_get_contents(resource_path('js/admin/rich-text-editor.js'));
        $compactScript = preg_replace('/\s+/', '', $script);

        $this->assertIsString($compactScript);
        $this->assertStringContainsString("import{Jodit}from'jodit'", $compactScript);
        $this->assertStringContainsString('newMap()', $compactScript);
        $this->assertStringContainsString('textarea[data-cms-rich-text]', $script);
        $this->assertStringContainsString("document.addEventListener('livewire:navigating'", $script);
        $this->assertStringContainsString("document.addEventListener('livewire:navigated'", $script);
        $this->assertStringContainsString('editor.synchronizeValues()', $script);
        $this->assertStringContainsString('editor.destruct()', $script);
        $this->assertStringContainsString('openMediaVaultForRichText', $script);
        $this->assertStringContainsString('insertImageAsBase64URI: false', $script);
        $this->assertStringContainsString('showTabInFileSelector: false', $script);

        foreach (['paragraph', 'bold', 'italic', 'underline', 'ul', 'ol', 'link', 'table', 'goldenEyeImage', 'undo', 'redo'] as $button) {
            $this->assertContains($button, $this->toolbarButtons($script));
        }

        foreach (['p', 'h2', 'h3', 'blockquote'] as $format) {
            $this->assertStringContainsString("{$format}:", $script);
        }

        foreach (['source', 'iframe', 'filebrowser'] as $unsupportedControl) {
            $this->assertNotContains($unsupportedControl, $this->toolbarButtons($script));
        }
    }

    public function test_branding_rich_text_round_trips_through_the_public_sanitizer(): void
    {
        cache()->flush();

        foreach ([
            'footer_about_text' => '<p><strong>Safe footer</strong><script>alert(1)</script></p>',
            'courses_subtitle' => '<p><em>Safe course subtitle</em><img src="javascript:alert(1)" onerror="alert(1)"></p>',
            'blog_subtitle' => '<p><u>Safe blog subtitle</u><iframe src="https://example.com"></iframe></p>',
        ] as $key => $value) {
            SiteSetting::create(['key' => $key, 'value' => $value, 'type' => 'text']);
        }

        $home = $this->get(route('home'));
        $home->assertOk()
            ->assertSee('<strong>Safe footer</strong>', false)
            ->assertDontSee('alert(1)', false);

        $courses = $this->get(route('courses-all'));
        $courses->assertOk()
            ->assertSee('<em>Safe course subtitle</em>', false)
            ->assertDontSee('src="javascript:alert(1)"', false)
            ->assertDontSee('onerror="alert(1)"', false);

        $blog = $this->get(route('blog'));
        $blog->assertOk()
            ->assertSee('<u>Safe blog subtitle</u>', false)
            ->assertDontSee('src="https://example.com"', false);
    }

    /**
     * @return array<int, string>
     */
    private function toolbarButtons(string $script): array
    {
        preg_match('/const toolbarButtons = \[(.*?)\];/s', $script, $matches);
        preg_match_all("/'([^']+)'/", Arr::get($matches, 1, ''), $buttons);

        return $buttons[1];
    }
}
