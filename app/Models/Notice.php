<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function scopeCurrentlyEligible(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where(function (Builder $query): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    public function scopeInPublicPriority(Builder $query): Builder
    {
        return $query->orderByDesc('is_urgent')
            ->orderByRaw('CASE WHEN starts_at IS NOT NULL THEN 1 ELSE 0 END DESC')
            ->latest('starts_at')
            ->latest('updated_at')
            ->latest('id');
    }

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
