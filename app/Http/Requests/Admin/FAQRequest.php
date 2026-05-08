<?php

namespace App\Http\Requests\Admin;

class FAQRequest extends CMSRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
            'order_priority' => ['nullable', 'integer'],
        ]);
    }
}
