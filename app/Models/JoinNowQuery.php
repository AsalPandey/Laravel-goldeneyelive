<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JoinNowQuery extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'phone',
        'address',
        'course',
        'queries',
        'status',
        'admin_notes',
        'followed_up_at',
        'course_id',
        'course_slug',
        'contactMethod',
        'lead_source',
        'landing_page',
        'cta_id',
    ];
}
