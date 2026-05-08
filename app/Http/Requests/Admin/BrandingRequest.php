<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;
use Illuminate\Foundation\Http\FormRequest;

class BrandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('Admin');
    }

    public function rules(): array
    {
        $imageLimit = SiteSetting::getValue('image_size_limit', 2048);

        return [
            'site_name' => ['nullable', 'string', 'max:255'],
            'site_name_suffix' => ['nullable', 'string', 'max:255'],
            'site_email' => ['nullable', 'email', 'max:255'],
            'site_phone' => ['nullable', 'string', 'max:255'],
            'site_address' => ['nullable', 'string', 'max:500'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'google_analytics_id' => ['nullable', 'string', 'regex:/^G-[a-zA-Z0-9-]+$/'],
            'recaptcha_site_key' => ['nullable', 'string', 'max:255'],
            'recaptcha_secret_key' => ['nullable', 'string', 'max:255'],
            'opening_hours' => ['nullable', 'string', 'max:255'],
            'geo_latitude' => ['nullable', 'string', 'max:255'],
            'geo_longitude' => ['nullable', 'string', 'max:255'],
            'google_maps_embed' => ['nullable', 'string'],
            'schema_markup' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'image_size_limit' => ['nullable', 'integer', 'min:512', 'max:10240'],

            // Image paths (vault selection)
            'site_logo_path' => ['nullable', 'string', 'max:255'],
            'site_favicon_path' => ['nullable', 'string', 'max:255'],
            'site_footer_logo_path' => ['nullable', 'string', 'max:255'],
            'breadcrumb_bg_path' => ['nullable', 'string', 'max:255'],
            'cta_bg_path' => ['nullable', 'string', 'max:255'],
            'hero_image_path' => ['nullable', 'string', 'max:255'],
            'about_image_path' => ['nullable', 'string', 'max:255'],
            'founder_image_path' => ['nullable', 'string', 'max:255'],
            'popup_image_path' => ['nullable', 'string', 'max:255'],

            // Image uploads
            'site_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'site_favicon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'site_footer_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'breadcrumb_bg' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'cta_bg' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'hero_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'about_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'founder_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
            'popup_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', "max:{$imageLimit}"],
        ];
    }
}
