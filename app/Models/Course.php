<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $fillable = [
        'name', 'badge_text', 'slug', 'category', 'category_slug', 'category_id', 'price', 'duration',
        'instructor', 'teacher_id', 'capacity', 'description', 'course_outline', 'photo',
        'rating_star', 'rating_count', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup', 'status', 'is_featured', 'display_order',
    ];

    public function faqs(): BelongsToMany
    {
        return $this->belongsToMany(FAQ::class, 'course_faq', 'course_id', 'faq_id');
    }

    public function courseCategory(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
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
