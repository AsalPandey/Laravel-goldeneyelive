<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use Database\Factories\CourseCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    /** @use HasFactory<CourseCategoryFactory> */
    use HasFactory, HasUniqueSlug;

    protected $fillable = ['name', 'slug', 'image', 'status', 'order_priority', 'description', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup'];

    public function courses()
    {
        return $this->hasMany(Course::class, 'category_id');
    }
}
