<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;

class TestimonialRequest extends CMSRequest
{
    public function rules(): array
    {
        $imageLimit = SiteSetting::getValue('image_size_limit', 2048);

        return array_merge(parent::rules(), [
            'student_name' => ['required', 'string', 'max:255'],
            'course_name' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', "max:{$imageLimit}"],
            'photo_path' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'is_featured' => ['nullable', 'boolean'],
        ]);
    }
}
