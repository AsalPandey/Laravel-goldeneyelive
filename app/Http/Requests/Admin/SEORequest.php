<?php

namespace App\Http\Requests\Admin;

use App\Support\PublicCtaContract;
use Illuminate\Foundation\Http\FormRequest;

class SEORequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    protected function prepareForValidation(): void
    {
        $fields = [
            'meta_title',
            'meta_keywords',
            'meta_description',
            'aeo_summary',
            'site_name',
        ];

        $normalized = [];

        foreach ($fields as $field) {
            if ($this->has($field)) {
                $normalized[$field] = PublicCtaContract::normalizeBrandText((string) $this->input($field));
            }
        }

        if ($normalized !== []) {
            $this->merge($normalized);
        }
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
            'aeo_summary' => ['nullable', 'string'],
            'geo_latitude' => ['nullable', 'string', 'max:50'],
            'geo_longitude' => ['nullable', 'string', 'max:50'],
            'site_name' => ['nullable', 'string', 'max:255'],
            'site_name_suffix' => ['nullable', 'string', 'max:255'],
            'founding_year' => ['nullable', 'digits:4', 'integer', 'min:1900', 'max:'.now()->year],
        ];
    }
}
