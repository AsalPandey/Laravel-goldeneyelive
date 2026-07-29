<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $fillable = ['title', 'slug', 'content', 'image', 'author', 'category', 'status', 'published_at', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('slug')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
