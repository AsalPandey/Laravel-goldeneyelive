<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;

class CourseRequest extends CMSRequest
{
    public function rules(): array
    {
        $imageLimit = SiteSetting::getValue('image_size_limit', 2048);

        return array_merge(parent::rules(), [
            'name' => ['required', 'string', 'max:255'],
            'badge_text' => ['nullable', 'string', 'max:50'],
            'slug' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:course_categories,id'],
            'price' => ['required', 'string'],
            'duration' => ['required', 'string'],
            'instructor' => ['required', 'string'],
            'capacity' => ['required', 'string'],
            'description' => ['required', 'string'],
            'course_outline' => ['required', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', "max:{$imageLimit}"],
            'photo_path' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'is_featured' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
    }
}
