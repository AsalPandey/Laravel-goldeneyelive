<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'subtitle', 'image', 'badge', 'link', 'button_text', 'status',
        'is_urgent', 'display_type', 'starts_at', 'expires_at',
        'meta_title', 'meta_description', 'meta_keywords', 'aeo_summary', 'schema_markup',
    ];

    protected $casts = [
        'is_urgent' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function publicationState(): string
    {
        if ($this->status !== 'active') {
            return 'inactive';
        }

        if ($this->starts_at?->isFuture()) {
            return 'scheduled';
        }

        if ($this->expires_at?->isPast()) {
            return 'expired';
        }

        return 'live';
    }
}
