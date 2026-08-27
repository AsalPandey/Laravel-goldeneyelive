<?php

namespace Database\Seeders;

use App\Models\FAQ;
use App\Support\ApprovedCourseFaqDeploymentData;
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

        $faqs = array_map(function (array $faq): array {
            if ($faq['question'] === ApprovedCourseFaqDeploymentData::CERTIFICATE_QUESTION) {
                $faq['answer'] = ApprovedCourseFaqDeploymentData::CERTIFICATE_ANSWER;
            }

            return $faq;
        }, GoldenEyeContentBaseline::faqs());

        foreach (ApprovedCourseFaqDeploymentData::approvedFaqs() as $question => $approvedFaq) {
            $faqs[] = [
                'question' => $question,
                ...$approvedFaq,
            ];
        }

        foreach ($faqs as $faq) {
            $identity = isset($faq['id'])
                ? ['id' => $faq['id']]
                : ['question' => $faq['question']];

            FAQ::updateOrCreate(
                $identity,
                [
                    'question' => $faq['question'],
                    'answer' => $faq['answer'],
                    'status' => $faq['status'] ?? 'active',
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
