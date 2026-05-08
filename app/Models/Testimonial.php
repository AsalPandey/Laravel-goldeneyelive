<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = ['student_name', 'course_name', 'photo', 'content', 'rating', 'status', 'is_featured'];
}
