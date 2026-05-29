<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SEORequest;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use RealRashid\SweetAlert\Facades\Alert;

class SEOController extends Controller
{
    /**
     * SEO Center Keys
     */
    const SEO_KEYS = [
        'meta_title', 'meta_keywords', 'meta_description',
        'google_analytics_id', 'google_search_console_id', 'bing_webmaster_id',
        'robots_txt', 'aeo_summary', 'speakable_selectors', 'schema_markup',
        'geo_latitude', 'geo_longitude', 'site_name', 'site_name_suffix',
    ];

    /**
     * Display the SEO Authority Center
     */
    public function index(): View
    {
        $settings = SiteSetting::all()->pluck('value', 'key');
        $robotsTxtWarning = SEORequest::ROBOTS_FULL_SITE_BLOCK_WARNING;

        // Ensure robots.txt default if not set
        if (! isset($settings['robots_txt'])) {
            $settings['robots_txt'] = "User-agent: *\nDisallow: /admin\nDisallow: /login\n\nSitemap: ".url('/sitemap.xml');
        }

        return view('admin.seo.index', compact('settings', 'robotsTxtWarning'));
    }

    /**
     * Update SEO Settings
     */
    public function update(SEORequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $data = [];
        foreach (self::SEO_KEYS as $key) {
            if ($request->has($key)) {
                $value = $request->input($key) ?? '';

                // JSON-LD Validation for Schema Markup
                if ($key === 'schema_markup' && $value) {
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        return back()->withErrors(['schema_markup' => 'The schema_markup must be a valid JSON-LD string.'])->withInput();
                    }
                }

                $data[] = [
                    'key' => $key,
                    'value' => $value,
                    'type' => 'text',
                ];
            }
        }

        if (! empty($data)) {
            SiteSetting::upsert($data, ['key'], ['value', 'type']);
        }

        // Clear cache
        cache()->forget('site_settings');
        foreach (self::SEO_KEYS as $key) {
            cache()->forget("setting_{$key}");
        }
        $this->clearSiteCache();

        Alert::success('SEO Strategy Updated', 'The SEO, AEO, and GEO settings have been synchronized site-wide.');

        return back();
    }
}
