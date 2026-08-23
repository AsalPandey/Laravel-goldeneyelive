<?php

namespace App\Support;

use App\Models\Notice;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class PublicSiteCache
{
    public const MaximumSeconds = 3600;

    public static function secondsUntilNoticeTransition(): int
    {
        try {
            if (! Schema::hasTable('notices')) {
                return self::MaximumSeconds;
            }

            $now = now();
            $nextStart = Notice::query()
                ->where('status', 'active')
                ->where('starts_at', '>', $now)
                ->min('starts_at');
            $nextExpiry = Notice::query()
                ->where('status', 'active')
                ->where('expires_at', '>=', $now)
                ->min('expires_at');
            $transitions = collect([$nextStart, $nextExpiry])
                ->filter()
                ->map(fn (mixed $transition): int => max(1, (int) ceil($now->diffInSeconds($transition, false)) + 1))
                ->filter(fn (int $seconds): bool => $seconds > 0);

            return min(self::MaximumSeconds, $transitions->min() ?? self::MaximumSeconds);
        } catch (Throwable) {
            return 60;
        }
    }
}
