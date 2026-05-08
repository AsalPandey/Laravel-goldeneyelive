<?php

namespace App\Models;

use Database\Factories\ServicePillarFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePillar extends Model
{
    /** @use HasFactory<ServicePillarFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'icon',
        'slug',
        'summary',
        'bullets',
        'cta_label',
        'cta_url',
        'is_featured',
        'status',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'aeo_summary',
        'schema_markup',
    ];

    protected function casts(): array
    {
        return [
            'bullets' => 'array',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
