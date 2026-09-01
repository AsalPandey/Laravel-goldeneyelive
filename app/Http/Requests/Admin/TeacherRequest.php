<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;
use App\Rules\PublicMediaPath;

class TeacherRequest extends CMSRequest
{
    public function rules(): array
    {
        $imageLimit = SiteSetting::getValue('image_size_limit', 2048);

        return array_merge(parent::rules(), [
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:10000'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', "max:{$imageLimit}"],
            'photo_path' => ['exclude_if:remove_photo,1', 'nullable', 'string', 'max:255', new PublicMediaPath],
            'remove_photo' => ['nullable', 'boolean'],
            'status' => ['required', 'in:active,inactive'],
            'is_featured' => ['nullable', 'boolean'],
        ]);
    }
}
