<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class ServicePillarRequest extends CMSRequest
{
    public function rules(): array
    {
        $pillarId = $this->route('service_pillar')?->id ?? $this->route('servicePillar')?->id;

        return array_merge(parent::rules(), [
            'title' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:80'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('service_pillars', 'slug')->ignore($pillarId)],
            'summary' => ['nullable', 'string', 'max:1000'],
            'bullets' => ['nullable', 'array'],
            'bullets.*' => ['nullable', 'string', 'max:500'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'string', 'max:500'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', 'in:active,inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
