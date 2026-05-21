<?php

namespace Database\Seeders;

use App\Models\ServicePillar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicePillarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
                'title' => 'Local Guidance and Exposure',
                'icon' => 'fa fa-handshake',
                'summary' => 'Local guidance, practical exposure, and events that help students understand real options.',
                'bullets' => [
                    'Career conversations that help students understand real workplace expectations.',
                    'Workshops and sessions with practical examples from relevant fields.',
                    'Community events where students can ask questions and compare next steps.',
                ],
                'cta_label' => 'Ask for Course Help',
                'cta_url' => '/contact',
                'is_featured' => true,
                'sort_order' => 20,
            ],
            [
                'title' => 'Career and College Guidance',
                'icon' => 'fa fa-bullseye',
                'summary' => 'Guided pathway planning for post-SEE, Plus Two, scholarships, and university questions.',
                'bullets' => [
                    'Post-SEE and Plus Two Path Guidance: Help students compare streams, colleges, and future paths.',
                    'Scholarship Direction: Guide students toward realistic funding and admission options.',
                    'Parent-Friendly Guidance: Explain choices clearly so families can decide with confidence.',
                ],
                'cta_label' => 'Ask for Course Help',
                'cta_url' => '/join-now?course=undecided&selected_course=undecided&source_page=service-pillar&source_section=career-college-blueprint&inquiry_intent=course_guidance',
                'is_featured' => false,
                'sort_order' => 30,
            ],
            [
                'title' => 'Practical Job and Computer Skills',
                'icon' => 'fa fa-lightbulb',
                'summary' => 'Practical training for computer skills, office work, IT basics, and communication.',
                'bullets' => [
                    'Practical training in IT, computer skills, office productivity, and communication.',
                    'Practice for interviews, job tasks, and daily workplace confidence.',
                    'Build outputs that students can explain during interviews or admissions conversations.',
                ],
                'cta_label' => 'View Course Details',
                'cta_url' => '/courses-all',
                'is_featured' => false,
                'sort_order' => 40,
            ],
            [
                'title' => 'Languages and Test Preparation',
                'icon' => 'fa fa-globe-asia',
                'summary' => 'Language, IELTS, PTE, Japanese, Korean, and study-abroad preparation for global education goals.',
                'bullets' => [
                    'Focused preparation for IELTS, PTE, and language requirements.',
                    'Practical Japanese, Korean, English, and Chinese classes for study, work, or daily communication.',
                    'Timeline Guidance: Match test prep with destination, application, and intake planning.',
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
                'summary' => 'Workshops, tech seminars, career summits, and community events that turn learning into real connections.',
                'bullets' => [
                    'Career Summits: Meet colleges, mentors, and employers in one focused space.',
                    'Skill Workshops: Join practical events that add proof to your profile.',
                    'Community Meetups: Build a circle with ambitious students and professionals.',
                ],
                'cta_label' => 'Ask for Course Help',
                'cta_url' => '/contact',
                'is_featured' => false,
                'sort_order' => 70,
            ],
        ];

        foreach ($pillars as $pillar) {
            $slug = Str::slug($pillar['title']);

            ServicePillar::updateOrCreate(
                ['slug' => $slug],
                [
                    ...$pillar,
                    'slug' => $slug,
                    'status' => 'active',
                    'meta_title' => $pillar['title'].' | GoldenEye Academy',
                    'meta_description' => $pillar['summary'],
                    'meta_keywords' => 'GoldenEye Academy, '.$pillar['title'],
                    'aeo_summary' => $pillar['summary'],
                    'schema_markup' => json_encode([
                        '@context' => 'https://schema.org',
                        '@type' => 'Service',
                        'name' => $pillar['title'],
                        'description' => $pillar['summary'],
                        'provider' => [
                            '@type' => 'EducationalOrganization',
                            'name' => 'GoldenEye Academy',
                        ],
                    ]),
                ],
            );
        }
    }
}
