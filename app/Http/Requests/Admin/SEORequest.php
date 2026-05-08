<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SEORequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    public function rules(): array
    {
        return [
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
            'google_analytics_id' => ['nullable', 'string', 'regex:/^G-[a-zA-Z0-9-]+$/'],
            'google_search_console_id' => ['nullable', 'string', 'max:255'],
            'bing_webmaster_id' => ['nullable', 'string', 'max:255'],
            'robots_txt' => ['nullable', 'string'],
            'aeo_summary' => ['nullable', 'string'],
            'speakable_selectors' => ['nullable', 'string'],
            'geo_latitude' => ['nullable', 'string', 'max:50'],
            'geo_longitude' => ['nullable', 'string', 'max:50'],
            'site_name' => ['nullable', 'string', 'max:255'],
            'site_name_suffix' => ['nullable', 'string', 'max:255'],
            'schema_markup' => ['nullable', 'string'],
        ];
    }
}
