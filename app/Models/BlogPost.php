<?php

namespace App\Models;

use App\Traits\HasUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory, HasUniqueSlug;

    protected $fillable = ['title', 'slug', 'content', 'image', 'author', 'category', 'status', 'published_at', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup'];
}
