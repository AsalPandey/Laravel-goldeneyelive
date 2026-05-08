<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUniqueSlug
{
    /**
     * Generate a unique slug for the model.
     */
    public function generateUniqueSlug($title, $id = null)
    {
        $slug = Str::slug($title);

        // Fetch all matching patterns in a single query
        $existingSlugs = $this->where('slug', 'like', $slug.'%')
            ->where('id', '!=', $id)
            ->pluck('slug')
            ->toArray();

        if (empty($existingSlugs) || ! in_array($slug, $existingSlugs)) {
            return $slug;
        }

        // Extract suffixes and find the next available number
        $max = 0;
        foreach ($existingSlugs as $existing) {
            if ($existing === $slug) {
                continue;
            }

            if (preg_match('/'.preg_quote($slug, '/').'-([0-9]+)$/', $existing, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }

        return $slug.'-'.($max + 1);
    }
}
