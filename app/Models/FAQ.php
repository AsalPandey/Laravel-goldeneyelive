<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    use HasFactory;

    protected $table = 'f_a_q_s';

    protected $fillable = ['question', 'answer', 'status', 'order_priority', 'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup'];
}
