<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;
use Illuminate\Validation\Rule;

class BlogRequest extends CMSRequest
{
    public function rules(): array
    {
        $imageLimit = SiteSetting::getValue('image_size_limit', 2048);
        $blogId = $this->route('blog');

        if (is_object($blogId) && method_exists($blogId, 'getKey')) {
            $blogId = $blogId->getKey();
        }

        return array_merge(parent::rules(), [
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'aeo_summary' => ['nullable', 'string', 'max:300'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', "max:{$imageLimit}"],
            'image_path' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date', 'after_or_equal:1970-01-01', 'before_or_equal:2038-01-18 23:59:59'],
            'courses_present' => ['sometimes', 'accepted'],
            'courses' => ['nullable', 'array'],
            'courses.*' => [
                'integer',
                'distinct',
                Rule::exists('courses', 'id')->where(function ($query) use ($blogId) {
                    $query->where(function ($courseQuery) use ($blogId) {
                        $courseQuery->where('status', 'active');

                        if ($blogId) {
                            $courseQuery->orWhereIn('id', function ($pivotQuery) use ($blogId) {
                                $pivotQuery->select('course_id')
                                    ->from('blog_course')
                                    ->where('blog_id', $blogId);
                            });
                        }
                    });
                }),
            ],
        ]);
    }
}
