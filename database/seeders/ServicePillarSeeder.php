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
                'title' => 'Learn Your Way: Digital-First Flexibility',
                'icon' => 'fa fa-laptop-code',
                'summary' => 'Flexible online learning with in-person support when students need focus and collaboration.',
                'bullets' => [
                    'Online When You Want: Premium, interactive virtual classrooms that fit your schedule. Learn from our experts anywhere, anytime.',
                    'In-Person When You Need It: Access to our physical hubs for focused, face-to-face collaboration.',
                    'Always Connected: Access learning resources, quick help, and follow-up support beyond the classroom.',
                ],
                'cta_label' => 'Ask for Course Help',
                'cta_url' => '/join-now?course=undecided',
                'is_featured' => true,
                'sort_order' => 10,
            ],
            [
                'title' => 'The Network: Your Unfair Advantage',
                'icon' => 'fa fa-handshake',
                'summary' => 'Corporate access, industry exposure, and community events that help students move faster.',
                'bullets' => [
                    'Corporate Connections: Skip the cold-emailing. We connect you directly with private corporations for real-world exposure and internships.',
                    'Industry Meet and Greets: Learn directly from professionals working in the fields students want to enter.',
                    'Exclusive Community Events: Workshops, seminars, and career sessions designed to expand student networks.',
                ],
                'cta_label' => 'Join The Network',
                'cta_url' => '/contact',
                'is_featured' => true,
                'sort_order' => 20,
            ],
            [
                'title' => 'Career and College Blueprint: Zero Guesswork',
                'icon' => 'fa fa-bullseye',
                'summary' => 'Guided pathway planning for post-SEE, Plus Two, scholarships, and university applications.',
                'bullets' => [
                    'Post-SEE and Plus Two Placement: Help students compare streams, colleges, and future paths.',
                    'Scholarship Direction: Guide students toward realistic funding and admission options.',
                    'Parent-Friendly Guidance: Explain choices clearly so families can decide with confidence.',
                ],
                'cta_label' => 'Plan My Path',
                'cta_url' => '/join-now?course=undecided',
                'is_featured' => false,
                'sort_order' => 30,
            ],
            [
                'title' => 'Real-World Skills: Become Un-Ignorable',
                'icon' => 'fa fa-lightbulb',
                'summary' => 'Career-ready bootcamps and employability programs built around real workplace demand.',
                'bullets' => [
                    'Market-Ready Bootcamps: Practical training in IT, computer skills, office productivity, and communication.',
                    'Employability Upliftment: Train for interviews, job tasks, and daily workplace confidence.',
                    'Proof-Oriented Learning: Build outputs that students can explain during interviews or admissions conversations.',
                ],
                'cta_label' => 'Build My Skills',
                'cta_url' => '/courses-all',
                'is_featured' => false,
                'sort_order' => 40,
            ],
            [
                'title' => 'Global Launchpad: Languages and Test Prep',
                'icon' => 'fa fa-globe-asia',
                'summary' => 'Language, IELTS, PTE, Japanese, Korean, and study-abroad preparation for global education goals.',
                'bullets' => [
                    'Study Abroad Ready: Focused prep for IELTS, PTE, and language requirements.',
                    'Modern Language Mastery: Practical Japanese, Korean, English, and Chinese classes for global fluency.',
                    'Timeline Guidance: Match test prep with destination, application, and intake planning.',
                ],
                'cta_label' => 'Start Test Prep',
                'cta_url' => '/courses-all',
                'is_featured' => false,
                'sort_order' => 50,
            ],
            [
                'title' => 'Academic Powerhouse: Grades 8 to Masters',
                'icon' => 'fa fa-book-open',
                'summary' => 'Academic support from school level through undergraduate and masters-level IT and business support.',
                'bullets' => [
                    'Curriculum Classes: Targeted support for students who need stronger academic foundations.',
                    'Undergraduate Support: Help learners handle IT, business, and technical subjects more confidently.',
                    'Structured Follow-Up: Keep learners accountable through regular progress checks.',
                ],
                'cta_label' => 'Get Academic Support',
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
                'cta_label' => 'Ask About Events',
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
