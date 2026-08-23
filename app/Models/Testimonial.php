<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = ['student_name', 'course_name', 'course_id', 'photo', 'content', 'rating', 'status', 'is_featured'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
