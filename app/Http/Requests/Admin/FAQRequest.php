<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class FAQRequest extends CMSRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->routeIs('admin.faq.store')) {
            $this->mergeIfMissing(['status' => 'inactive']);
        }
    }

    public function rules(): array
    {
        $faqId = $this->route('faq');

        if (is_object($faqId) && method_exists($faqId, 'getKey')) {
            $faqId = $faqId->getKey();
        }

        return array_merge(parent::rules(), [
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:10000'],
            'status' => ['required', 'in:active,inactive'],
            'order_priority' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'courses' => ['nullable', 'array'],
            'courses.*' => [
                'integer',
                'distinct',
                Rule::exists('courses', 'id')->where(function ($query) use ($faqId) {
                    $query->where(function ($subQuery) use ($faqId) {
                        $subQuery->where('status', 'active');
                        if ($faqId) {
                            $subQuery->orWhereIn('id', function ($pivotQuery) use ($faqId) {
                                $pivotQuery->select('course_id')->from('course_faq')->where('faq_id', $faqId);
                            });
                        }
                    });
                }),
            ],
        ]);
    }
}
