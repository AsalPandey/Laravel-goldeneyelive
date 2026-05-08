<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'designation', 'photo', 'bio', 'facebook_url', 'linkedin_url', 'status', 'is_featured', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup'];
}
