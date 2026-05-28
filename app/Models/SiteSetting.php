<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $cacheKey = "setting_{$key}";
        $cachedValue = cache()->get($cacheKey);

        if (is_scalar($cachedValue)) {
            return $cachedValue;
        }

        if ($cachedValue !== null) {
            cache()->forget($cacheKey);
        }

        $value = self::query()
            ->where('key', $key)
            ->value('value');

        if ($value === null && ! self::query()->where('key', $key)->exists()) {
            return $default;
        }

        cache()->put($cacheKey, $value, 3600);

        return $value;
    }
}
