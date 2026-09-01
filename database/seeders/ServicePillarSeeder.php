<?php

namespace Database\Seeders;

use App\Models\ServicePillar;
use App\Support\GoldenEyeContentBaseline;
use Database\Seeders\Concerns\PreventsProductionBaselineSeeding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicePillarSeeder extends Seeder
{
    use PreventsProductionBaselineSeeding;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->preventProductionBaselineSeeding();

        $pillars = [
            [
                'title' => 'Flexible Learning Support',
                'icon' => 'fa fa-laptop-code',
                'summary' => 'Flexible online learning with in-person support when students need focus and collaboration.',
                'bullets' => [
                    'Online support when the course format allows it.',
                    'In-person classes for focused, face-to-face practice.',
                    'Learning resources, quick help, and follow-up support beyond the classroom.',
                ],
                'cta_label' => 'Ask for Course Help',
                'cta_url' => '/join-now?course=undecided&selected_course=undecided&source_page=service-pillar&source_section=digital-first-flexibility&inquiry_intent=course_guidance',
                'is_featured' => true,
                'sort_order' => 10,
            ],
            [
                'title' => 'Local Academic Support and Exposure',
                'icon' => 'fa fa-handshake',
                'summary' => 'Local academic support, practical exposure, and events that help students understand real class options.',
                'bullets' => [
                    'Career conversations that help students understand real workplace expectations.',
                    'Workshops and sessions with practical examples from relevant fields.',
                    'Community events where students can ask questions and compare course options.',
                ],
                'cta_label' => 'Ask for Course Help',
                'cta_url' => '/contact',
                'is_featured' => true,
                'sort_order' => 20,
            ],
            [
                'title' => 'Career and College Support',
                'icon' => 'fa fa-bullseye',
                'summary' => 'Academic support for post-SEE, Plus Two and current course questions.',
                'bullets' => [
                    'Post-SEE and Plus Two Support: Help students compare streams, colleges, and future paths.',
                    'Course Information: Compare current subjects, levels and learning options.',
                    'Parent-Friendly Support: Explain choices clearly so families can decide with confidence.',
                ],
                'cta_label' => 'Ask for Course Help',
                'cta_url' => '/join-now?course=undecided&selected_course=undecided&source_page=service-pillar&source_section=career-college-blueprint&inquiry_intent=course_guidance',
                'is_featured' => false,
                'sort_order' => 30,
            ],
            [
                'title' => 'Practical Job and Computer Skills',
                'icon' => 'fa fa-lightbulb',
                'summary' => 'Practical classes for computer skills, office work, IT basics, and communication.',
                'bullets' => [
                    'Practical classes in IT, computer skills, office productivity, and communication.',
                    'Practice common digital tasks used in study and workplace settings.',
                    'Build practical outputs that help learners review and explain their skills.',
                ],
                'cta_label' => 'View Course Details',
                'cta_url' => '/courses-all',
                'is_featured' => false,
                'sort_order' => 40,
            ],
            [
                'title' => 'Languages and Test Preparation',
                'icon' => 'fa fa-globe-asia',
                'summary' => 'Language, IELTS, PTE, Japanese, Korean, and exam preparation classes for international study goals.',
                'bullets' => [
                    'Focused preparation for IELTS, PTE, and language requirements.',
                    'Practical Japanese, Korean, English, and Chinese classes for study, work, or daily communication.',
                    'Batch Planning: Match test preparation with exam goals, timeline, and class availability.',
                ],
                'cta_label' => 'View Course Details',
                'cta_url' => '/courses-all',
                'is_featured' => false,
                'sort_order' => 50,
            ],
            [
                'title' => 'Academic Support Classes',
                'icon' => 'fa fa-book-open',
                'summary' => 'Academic support from school level through undergraduate IT and business support.',
                'bullets' => [
                    'Curriculum Classes: Targeted support for students who need stronger academic foundations.',
                    'Undergraduate Support: Help learners handle IT, business, and technical subjects more confidently.',
                    'Structured Follow-Up: Keep learners accountable through regular progress checks.',
                ],
                'cta_label' => 'Ask for Course Help',
                'cta_url' => '/contact',
                'is_featured' => false,
                'sort_order' => 60,
            ],
            [
                'title' => 'Events: Learn, Meet, Move',
                'icon' => 'fa fa-calendar-check',
                'summary' => 'Workshops, short programs and community events that extend learning beyond regular classes.',
                'bullets' => [
                    'Learning Sessions: Explore focused topics outside regular classes.',
                    'Skill Workshops: Take part in practical activities when announced.',
                    'Community Events: Learn alongside other students and participants.',
                ],
                'cta_label' => 'Ask for Course Help',
                'cta_url' => '/contact',
                'is_featured' => false,
                'sort_order' => 70,
            ],
        ];

        $protectedSlugs = array_keys(GoldenEyeContentBaseline::servicePillarUpdates());

        foreach ($pillars as $index => $pillar) {
            $slug = $protectedSlugs[$index] ?? Str::slug($pillar['title']);
            $pillar = [
                ...$pillar,
                ...GoldenEyeContentBaseline::servicePillarUpdates()[$slug],
            ];

            ServicePillar::updateOrCreate(
                ['slug' => $slug],
                [
                    ...$pillar,
                    'slug' => $slug,
                    'status' => 'active',
                    'meta_title' => $pillar['title'].' | Golden Eye Academy',
                    'meta_description' => $pillar['summary'],
                    'meta_keywords' => 'Golden Eye Academy, courses and classes in Pokhara',
                    'aeo_summary' => $pillar['summary'],
                ],
            );
        }
    }
}
