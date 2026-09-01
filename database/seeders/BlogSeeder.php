<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Support\GoldenEyeArticleBaseline;
use Carbon\CarbonImmutable;
use Database\Seeders\Concerns\PreventsProductionBaselineSeeding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    use PreventsProductionBaselineSeeding;

    private const BASELINE_PUBLISHED_AT = '2026-07-01 00:00:00';

    public function run(): void
    {
        $this->preventProductionBaselineSeeding();

        foreach (GoldenEyeArticleBaseline::articles() as $index => $post) {
            $summary = Str::squish(strip_tags($post['content']));
            $post['status'] = 'published';
            $post['published_at'] = BlogPost::where('slug', $post['slug'])->value('published_at')
                ?? CarbonImmutable::parse(self::BASELINE_PUBLISHED_AT)->subDays($index);
            $post['meta_title'] = $post['title'].' | Golden Eye Academy';
            $post['meta_description'] = Str::limit($summary, 155, '');
            $post['aeo_summary'] = Str::limit($summary, 240, '');
            BlogPost::updateOrCreate(['slug' => $post['slug']], $post);
        }
    }
}
