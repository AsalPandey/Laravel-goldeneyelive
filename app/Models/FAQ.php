<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FAQ extends Model
{
    use HasFactory;

    protected $table = 'f_a_q_s';

    protected $fillable = ['question', 'answer', 'status', 'order_priority', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup'];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_faq', 'faq_id', 'course_id');
    }
}
