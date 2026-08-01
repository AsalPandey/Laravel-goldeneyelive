<?php

namespace Database\Seeders;

use App\Models\FAQ;
use App\Support\GoldenEyeContentBaseline;
use Database\Seeders\Concerns\PreventsProductionBaselineSeeding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FAQSeeder extends Seeder
{
    use PreventsProductionBaselineSeeding;

    public function run(): void
    {
        $this->preventProductionBaselineSeeding();

        foreach (GoldenEyeContentBaseline::faqs() as $faq) {
            FAQ::updateOrCreate(
                ['id' => $faq['id']],
                [
                    'question' => $faq['question'],
                    'answer' => $faq['answer'],
                    'status' => 'active',
                    'order_priority' => $faq['order_priority'],
                    'meta_title' => $faq['question'].' | Golden Eye Academy FAQ',
                    'meta_description' => Str::limit($faq['answer'], 155, ''),
                    'meta_keywords' => 'Golden Eye Academy FAQ, courses in Pokhara',
                    'aeo_summary' => $faq['answer'],
                    'schema_markup' => json_encode([
                        '@context' => 'https://schema.org',
                        '@type' => 'Question',
                        'name' => $faq['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $faq['answer'],
                        ],
                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                ],
            );
        }
    }
}
