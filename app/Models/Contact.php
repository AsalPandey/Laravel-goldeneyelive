<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
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
        'reviewed' => 'Reviewed (legacy)',
        'rejected' => 'Invalid / Spam (legacy)',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'lead_source',
        'landing_page',
        'cta_id',
        'status',
        'admin_notes',
        'replied_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'replied_at' => 'datetime',
        ];
    }
}
