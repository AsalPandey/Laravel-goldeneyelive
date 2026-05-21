<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandingRequest;
use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Notice;
use App\Models\SiteSetting;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Traits\InteractsWithAssets;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class BrandingController extends Controller
{
    use InteractsWithAssets;

    const IMAGE_KEYS = [
        'founder_image',
        'about_image',
        'popup_image',
        'hero_image',
        'external_review_screenshot',
        'site_logo',
        'site_favicon',
        'site_footer_logo',
        'breadcrumb_bg',
        'cta_bg',
    ];

    const TEXT_KEYS = [
        'about_content',
        'about_content_title',
        'about_feat_1_desc',
        'about_feat_1_title',
        'about_feat_2_desc',
        'about_feat_2_title',
        'about_feat_3_desc',
        'about_feat_3_title',
        'about_feat_4_desc',
        'about_feat_4_title',
        'about_header_title',
        'about_page_content',
        'about_point_1',
        'about_point_2',
        'about_point_3',
        'about_point_4',
        'about_point_5',
        'about_point_6',
        'about_point_7',
        'about_point_8',
        'about_point_9',
        'about_section_tagline',
        'about_section_title',
        'about_text',
        'about_title',
        'aeo_facts_title',
        'aeo_summary',
        'bing_webmaster_id',
        'blog_cta_btn',
        'blog_cta_desc',
        'blog_cta_title',
        'blog_header_title',
        'blog_section_title',
        'blog_subtitle',
        'blog_tagline',
        'blog_title',
        'career_highlight_1',
        'career_highlight_2',
        'career_highlight_3',
        'career_highlight_4',
        'category_header_badge',
        'category_tagline',
        'category_title_prefix',
        'contact_header_title',
        'contact_page_content',
        'contact_success_message',
        'courses_all_tagline',
        'courses_all_title',
        'courses_header_title',
        'courses_subtitle',
        'courses_title',
        'enroll_header_title',
        'enroll_section_title',
        'enroll_success_message',
        'facebook_url',
        'faq_btn_text',
        'faq_header_title',
        'faq_lead_title',
        'faq_page_content',
        'footer_about_text',
        'footer_contact_title',
        'footer_faq_title',
        'footer_newsletter_desc',
        'footer_quick_link_title',
        'footer_social_title',
        'external_review_proof_note',
        'founder_message',
        'founder_name',
        'founder_position',
        'founder_section_tagline',
        'founder_section_title',
        'geo_latitude',
        'geo_longitude',
        'google_analytics_id',
        'google_business_profile_url',
        'google_maps_embed',
        'google_search_console_id',
        'hero_badge_text',
        'hero_cta_1_text',
        'hero_cta_2_text',
        'hero_cta_text',
        'hero_hook_body',
        'hero_hook_headline',
        'hero_subtitle',
        'hero_title',
        'image_size_limit',
        'inquiry_subtitle',
        'inquiry_tab_text',
        'inquiry_title',
        'instagram_url',
        'linkedin_url',
        'logo_subtitle',
        'meta_description',
        'meta_keywords',
        'meta_title',
        'navbar_menu_label',
        'newsletter_success_message',
        'notice_badge_text',
        'notice_dismiss_text',
        'opening_hours',
        'pathway_tagline',
        'pathway_title',
        'popup_button_text',
        'popup_register_link',
        'popup_status',
        'popup_subtitle',
        'popup_title',
        'privacy_header_title',
        'privacy_policy_content',
        'recaptcha_secret_key',
        'recaptcha_site_key',
        'recent_posts_title',
        'robots_txt',
        'schema_markup',
        'site_address',
        'site_email',
        'site_name',
        'site_name_suffix',
        'site_phone',
        'speakable_selectors',
        'stat_1_lab',
        'stat_1_val',
        'stat_2_lab',
        'stat_2_val',
        'stat_3_lab',
        'stat_3_val',
        'stat_4_lab',
        'stat_4_val',
        'sticky_cta_badge',
        'sticky_cta_desc',
        'sticky_cta_text',
        'teachers_subtitle',
        'teachers_title',
        'terms_and_conditions_content',
        'terms_header_title',
        'testimonials_title',
        'twitter_url',
        'whatsapp_button_text',
        'whatsapp_cta_subtext',
        'whatsapp_cta_text',
        'whatsapp_number',
        'whatsapp_prefill_message',
        'youtube_url',
    ];

    /**
     * Settings that only Admins can modify (Security & SEO Infrastructure)
     */
    const SENSITIVE_KEYS = [
        'google_analytics_id', 'google_maps_embed', 'google_search_console_id',
        'recaptcha_site_key', 'recaptcha_secret_key', 'robots_txt',
        'image_size_limit', 'geo_latitude', 'geo_longitude',
        'schema_markup', 'aeo_summary', 'site_name', 'site_name_suffix',
    ];

    /**
     * Unified Branding Hub (Tabs: Homepage, Asset Vault, Marketing, Contact)
     */
    public function index(): View|JsonResponse
    {
        // 1. Fetch Text/Image Settings
        $settings = SiteSetting::all()->pluck('value', 'key');
        $isAdmin = auth()->user()->hasRole('Admin');

        // 2. Fetch Assets for the Vault (Optimized: Check usage in bulk)
        $usedAssets = $this->getUsedAssets();

        $path = public_path('site/img');
        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
        $files = File::allFiles($path);

        $images = [];
        foreach ($files as $file) {
            $relativePath = str_replace(public_path().DIRECTORY_SEPARATOR, '', $file->getRealPath());
            $relativePath = str_replace('\\', '/', $relativePath);
            $filename = $file->getFilename();
            $images[] = [
                'name' => $filename,
                'path' => $relativePath,
                'size' => number_format($file->getSize() / 1024, 2).' KB',
                'is_unused' => ! isset($usedAssets[$filename]) && ! isset($usedAssets[$relativePath]),
            ];
        }

        // 3. Calculate Brand Completeness (Dynamic Analytics)
        $expectedKeys = [
            'hero_title', 'hero_subtitle', 'hero_cta_text', 'hero_image',
            'site_logo', 'site_favicon', 'site_email', 'site_phone', 'site_address',
            'about_title', 'about_text', 'founder_name', 'founder_image',
            'facebook_url', 'instagram_url', 'meta_title', 'meta_description',
        ];
        $filledCount = 0;
        foreach ($expectedKeys as $key) {
            if ($settings->has($key) && ! empty($settings[$key])) {
                $filledCount++;
            }
        }
        $brandCompleteness = count($expectedKeys) > 0 ? round(($filledCount / count($expectedKeys)) * 100) : 0;

        if (request()->has('json')) {
            return response()->json([
                'images' => $images,
                'usedAssets' => $usedAssets,
            ]);
        }

        $activeNotice = Notice::where('status', 'active')->first();
        $isAdmin = auth()->user()->hasRole('Admin');

        return view('admin.branding.index', compact('settings', 'images', 'brandCompleteness', 'usedAssets', 'activeNotice', 'isAdmin'));
    }

    /**
     * Save Branding Settings (Text + Images)
     */
    public function update(BrandingRequest $request): RedirectResponse
    {
        $imageKeys = self::IMAGE_KEYS;
        $isAdmin = auth()->user()->hasRole('Admin');

        // Validation handled by BrandingRequest
        $validated = $request->validated();

        $textKeys = self::TEXT_KEYS;

        if ($isAdmin) {
            $textKeys = array_merge($textKeys, [
                'recaptcha_site_key', 'recaptcha_secret_key',
            ]);
        }

        // Optimize: Batch prepare text settings
        $settingsData = [];
        foreach ($textKeys as $key) {
            if ($request->has($key)) {
                // Security Check: Skip sensitive keys for non-admins
                if (! $isAdmin && in_array($key, self::SENSITIVE_KEYS)) {
                    continue;
                }

                // Sanitize Google Maps: Extract src if user pastes full iframe
                $value = $request->input($key) ?? '';
                if ($key === 'google_maps_embed' && str_contains($value, '<iframe')) {
                    preg_match('/src=["\']([^"\']+)["\']/', $value, $match);
                    $value = $match[1] ?? $value;
                }

                // JSON-LD Validation for Schema Markup
                if ($key === 'schema_markup' && $value) {
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        return back()->withErrors(['schema_markup' => 'The schema_markup must be a valid JSON-LD string.'])->withInput();
                    }
                }

                $settingsData[] = [
                    'key' => $key,
                    'value' => $value,
                    'type' => 'text',
                ];
            }
        }

        // Batch prepare image settings
        foreach ($imageKeys as $imageKey) {
            $pathKey = $imageKey.'_path';
            if ($request->hasFile($imageKey)) {
                $settingsData[] = [
                    'key' => $imageKey,
                    'value' => $this->uploadAsset($request->file($imageKey)),
                    'type' => 'image',
                ];
            } elseif ($request->filled($pathKey)) {
                $path = ltrim($request->input($pathKey), '/');
                // Ensure manual paths start with site/img/ if they are intended to be local
                if (! str_starts_with($path, 'http') && ! str_starts_with($path, 'site/')) {
                    $path = 'site/img/'.$path;
                }
                $settingsData[] = [
                    'key' => $imageKey,
                    'value' => $path,
                    'type' => 'image',
                ];
            }
        }

        // Perform Upsert (requires unique key on 'key' column)
        if (! empty($settingsData)) {
            $oldImagePaths = [];
            foreach ($imageKeys as $imageKey) {
                if ($request->hasFile($imageKey)) {
                    $oldImagePaths[] = SiteSetting::where('key', $imageKey)->value('value');
                }
            }

            SiteSetting::upsert($settingsData, ['key'], ['value', 'type']);

            // Cleanup replaced assets AFTER database is updated
            foreach ($oldImagePaths as $oldPath) {
                if ($oldPath) {
                    $this->deleteAsset($oldPath);
                }
            }
        }

        // Cache clear for site settings, asset vault, and homepage
        cache()->forget('site_settings');

        // Clear granular 'setting_{key}' caches for all whitelisted keys
        foreach (array_merge($textKeys, $imageKeys) as $key) {
            cache()->forget("setting_{$key}");
        }

        $this->clearSiteCache();

        // Audit Log: Track administrative changes
        logger()->info('Branding Updated by '.auth()->user()->email, [
            'ip' => request()->ip(),
            'keys_updated' => array_keys($validated),
        ]);

        Alert::success('Branding Updated', 'Website branding and settings have been synchronized successfully.');

        return back();
    }

    /**
     * Asset Vault Management (Upload/Replace/Delete)
     */
    public function storeAsset(Request $request)
    {
        $imageLimit = (int) SiteSetting::getValue('image_size_limit', 2048);
        $request->validate(['image' => "required|image|mimes:jpeg,png,jpg,gif|max:{$imageLimit}"]);
        $file = $request->file('image');

        if ($request->has('replace_path') && $request->replace_path) {
            $newPath = $this->uploadAsset($file, 'site/img', $request->replace_path);

            // Synchronize: Update all settings and models that were using this old asset path
            SiteSetting::where('value', $request->replace_path)->update(['value' => $newPath]);
            Course::where('photo', $request->replace_path)->update(['photo' => $newPath]);
            Teacher::where('photo', $request->replace_path)->update(['photo' => $newPath]);
            BlogPost::where('image', $request->replace_path)->update(['image' => $newPath]);
            Testimonial::where('photo', $request->replace_path)->update(['photo' => $newPath]);
            Notice::where('image', $request->replace_path)->update(['image' => $newPath]);
            CourseCategory::where('image', $request->replace_path)->update(['image' => $newPath]);

            $this->clearSiteCache();
            Alert::success('Asset Replaced', 'The resource has been updated site-wide across all related modules and settings.');

            return back();
        }

        $this->uploadAsset($file);

        $this->clearSiteCache();
        Alert::success('Asset Added', 'New branding resource is now available in the vault.');

        return back();
    }

    public function destroyAsset(Request $request)
    {
        $requestedPath = $request->path;

        // Security: Prevent path traversal by ensuring the path is within site/img
        if (str_contains($requestedPath, '..') || ! str_starts_with($requestedPath, 'site/img/')) {
            Alert::error('Security Warning', 'Invalid asset path provided.');

            return back();
        }

        // Safety: Prevent deleting assets currently in use by core site settings
        $inUse = SiteSetting::whereIn('key', ['site_logo', 'site_favicon', 'hero_image', 'about_image', 'founder_image'])
            ->where('value', $requestedPath)
            ->exists();

        if ($inUse) {
            Alert::error('Protected Asset', 'Cannot delete this file. It is currently set as an active Branding asset (Logo, Hero, or Founder Photo). Change the setting before deleting.');

            return back();
        }

        if ($this->deleteAsset($requestedPath)) {
            $this->clearSiteCache();
            Alert::success('Asset Removed', 'The file was deleted from the server.');

            return back();
        }

        return back()->with('error', 'File not found or protected.');
    }

    public function purgeAssets(Request $request)
    {
        $this->clearSiteCache();
        $usedAssets = $this->getUsedAssets();
        $path = public_path('site/img');
        $files = File::allFiles($path);
        $purgedCount = 0;

        foreach ($files as $file) {
            // Get the full relative path from the public directory to support recursive structures
            $fullPath = $file->getRealPath();
            $publicPath = public_path();
            $relativePath = ltrim(str_replace($publicPath, '', $fullPath), DIRECTORY_SEPARATOR);
            $relativePath = str_replace('\\', '/', $relativePath); // Normalize to web paths

            $filename = $file->getFilename();

            if (! isset($usedAssets[$filename]) && ! isset($usedAssets[$relativePath]) && ! $this->isProtectedAsset($relativePath)) {
                $this->deleteAsset($relativePath);
                $purgedCount++;
            }
        }

        cache()->forget('site_used_assets');
        Alert::success('Purge Complete', "Successfully removed {$purgedCount} unused assets from the server.");

        return back();
    }

    /**
     * Optimized: Batch check asset usage across all models
     */
    private function getUsedAssets()
    {
        return cache()->remember('site_used_assets', 300, function () {
            $allPaths = [];

            // 1. Site Branding Settings
            $allPaths = array_merge($allPaths, SiteSetting::pluck('value')->toArray());

            // 2. Primary Asset Fields
            $allPaths = array_merge($allPaths, Course::pluck('photo')->toArray());
            $allPaths = array_merge($allPaths, Teacher::pluck('photo')->toArray());
            $allPaths = array_merge($allPaths, BlogPost::pluck('image')->toArray());
            $allPaths = array_merge($allPaths, Testimonial::pluck('photo')->toArray());
            $allPaths = array_merge($allPaths, Notice::pluck('image')->toArray());
            $allPaths = array_merge($allPaths, CourseCategory::pluck('image')->toArray());

            $filenames = [];
            foreach ($allPaths as $path) {
                if ($path) {
                    $filenames[ltrim($path, '/')] = true;
                }
            }

            // 3. Scan rich text fields for embedded images
            $richTextFields = [
                'Course' => ['description', 'course_outline'],
                'BlogPost' => ['content'],
                'FAQ' => ['answer'],
                'CourseCategory' => ['description'],
                'Notice' => ['subtitle'],
                'Teacher' => ['bio'],
                'SiteSetting' => ['value'],
            ];

            foreach ($richTextFields as $modelName => $fields) {
                $modelClass = 'App\\Models\\'.$modelName;
                if (! class_exists($modelClass)) {
                    continue;
                }

                foreach ($fields as $field) {
                    // Use cursor to process large HTML contents memory-efficiently
                    foreach ($modelClass::select($field)->cursor() as $record) {
                        $content = $record->{$field};
                        if ($content) {
                            if (str_contains($content, '<') && str_contains($content, '>')) {
                                preg_match_all('/site\/img\/[a-zA-Z0-9_\-\.\/]+/', $content, $matches);
                                if (! empty($matches[0])) {
                                    foreach ($matches[0] as $match) {
                                        $filenames[ltrim($match, '/')] = true;
                                    }
                                }
                            } else {
                                $filenames[ltrim($content, '/')] = true;
                            }
                        }
                    }
                }
            }

            return $filenames;
        });
    }
}
