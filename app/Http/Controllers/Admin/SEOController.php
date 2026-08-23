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
        'aeo_summary',
        'geo_latitude', 'geo_longitude', 'site_name', 'site_name_suffix', 'founding_year',
    ];

    /**
     * Display the SEO Authority Center
     */
    public function index(): View
    {
        $settings = SiteSetting::all()->pluck('value', 'key');

        return view('admin.seo.index', compact('settings'));
    }

    /**
     * Update SEO Settings
     */
    public function update(SEORequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $data = [];
        foreach (self::SEO_KEYS as $key) {
            if (array_key_exists($key, $validated)) {
                $value = $validated[$key] ?? '';

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
