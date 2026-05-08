<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $fillable = [
        'name', 'badge_text', 'slug', 'category', 'category_slug', 'category_id', 'price', 'duration',
        'instructor', 'capacity', 'description', 'course_outline', 'photo',
        'rating_star', 'rating_count', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup', 'status', 'is_featured', 'display_order',
    ];

    public function courseCategory(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('status', 'active')
            ->whereNotNull('slug')
            ->where(function (Builder $query): void {
                $query->whereNull('category_id')
                    ->orWhereHas('courseCategory', function (Builder $categoryQuery): void {
                        $categoryQuery->where('status', 'active');
                    });
            });
    }

    public function scopeSalesOrdered(Builder $query): Builder
    {
        return $query
            ->orderByDesc('is_featured')
            ->orderBy('display_order')
            ->latest();
    }
}
