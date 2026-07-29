<?php

namespace App\Http\Controllers;

use App\Support\CanonicalUrl;

abstract class Controller
{
    /**
     * Clear cached public-site data after CMS changes.
     *
     * @param  array<int, string>  $extraKeys
     */
    protected function clearSiteCache(array $extraKeys = []): void
    {
        $keys = array_unique(array_merge([
            'homepage_data',
            'homepage_data_v2',
            'homepage_categories',
            'category_counts',
            'site_active_notice',
            'site_footer_faqs',
            'site_faqs',
            'about_teachers',
            'site_used_assets',
            'site_settings',
            'site_active_categories',
            'sitemap_xml',
            'sitemap_xml_'.hash('xxh128', CanonicalUrl::baseUrl()),
            'site_shared_data',
        ], $extraKeys));

        foreach ($keys as $key) {
            cache()->forget($key);
        }
    }
}
