<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;

class TeacherRequest extends CMSRequest
{
    public function rules(): array
    {
        $imageLimit = SiteSetting::getValue('image_size_limit', 2048);

        return array_merge(parent::rules(), [
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', "max:{$imageLimit}"],
            'photo_path' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'is_featured' => ['nullable', 'boolean'],
        ]);
    }
}
