<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoinNowQuery extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array<string, string>
     */
    public const STATUS_LABELS = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'resolved' => 'Resolved / Closed',
        'invalid' => 'Invalid / Spam',
        'enrolled' => 'Enrolled',
        'reviewed' => 'Reviewed (legacy)',
        'rejected' => 'Invalid / Spam (legacy)',
    ];

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
        'selected_course',
        'contactMethod',
        'help_topic',
        'current_education_level',
        'preferred_batch_time',
        'goal',
        'lead_source',
        'landing_page',
        'cta_id',
        'source_page',
        'source_section',
        'audience_type',
        'inquiry_intent',
        'lead_score',
        'lead_status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'followed_up_at' => 'datetime',
            'lead_score' => 'integer',
        ];
    }
}
