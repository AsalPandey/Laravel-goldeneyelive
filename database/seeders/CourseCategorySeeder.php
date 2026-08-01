<?php

namespace Database\Seeders;

use App\Models\CourseCategory;
use Database\Seeders\Concerns\PreventsProductionBaselineSeeding;
use Illuminate\Database\Seeder;

class CourseCategorySeeder extends Seeder
{
    use PreventsProductionBaselineSeeding;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->preventProductionBaselineSeeding();

        $categories = [
            [
                'name' => 'IELTS, PTE and Language Preparation',
                'slug' => 'study-abroad-test-prep',
                'image' => 'site/img/ielts-preparation.jpg',
                'status' => 'active',
                'order_priority' => 10,
                'description' => 'IELTS, PTE, Japanese, Korean, and English classes for learners developing exam and language skills.',
                'meta_title' => 'IELTS, PTE and Language Classes in Pokhara | Golden Eye Academy',
                'meta_description' => 'Explore IELTS, PTE, Japanese, Korean, and English classes at Golden Eye Academy in Pokhara.',
                'meta_keywords' => 'IELTS Pokhara, PTE Pokhara, Japanese class Pokhara, Korean class Pokhara, English class',
                'aeo_summary' => 'Golden Eye Academy offers IELTS, PTE, Japanese, Korean, and English preparation classes for learners in Pokhara.',
            ],
            [
                'name' => 'Computer and Office Skills',
                'slug' => 'computer-classes',
                'image' => 'site/img/computer-office-package.jpg',
                'status' => 'active',
                'order_priority' => 20,
                'description' => 'Practical computer, office package, Excel, file-handling and digital-productivity courses for study and workplace tasks.',
                'meta_title' => 'Computer and Office Skills Course in Pokhara | Golden Eye Academy',
                'meta_description' => 'Learn practical computer and office skills including Word, Excel, PowerPoint, email, internet, and workplace productivity.',
                'meta_keywords' => 'computer course Pokhara, office package Nepal, Excel training Pokhara',
                'aeo_summary' => 'The Computer and Office Skills category builds confidence with common study, office and workplace tasks.',
            ],
            [
                'name' => 'Web Development and IT Career',
                'slug' => 'web-development-it-career',
                'image' => 'site/img/basic-web-development.jpg',
                'status' => 'active',
                'order_priority' => 30,
                'description' => 'Web-development foundations taught through practical project work, including Laravel, databases, APIs and deployment concepts.',
                'meta_title' => 'Web Development Course in Pokhara | Golden Eye Academy',
                'meta_description' => 'Build web-development foundations through practical HTML, CSS, Laravel, database, API and deployment project work.',
                'meta_keywords' => 'web development Pokhara, Laravel course Nepal, coding class Pokhara',
                'aeo_summary' => 'Golden Eye Academy offers practical web-development classes covering foundations, Laravel, APIs and deployment concepts.',
            ],
            [
                'name' => 'Global Language Academy',
                'slug' => 'language-classes',
                'image' => 'site/img/advanced-english.jpg',
                'status' => 'active',
                'order_priority' => 40,
                'description' => 'English communication, Japanese, Korean, Chinese and professional language classes for study, work and everyday communication.',
                'meta_title' => 'Language Classes in Pokhara | Golden Eye Academy',
                'meta_description' => 'Learn English, Japanese, Korean, and Chinese with practical language classes for study, work, and exam preparation goals.',
                'meta_keywords' => 'language classes Pokhara, Korean class Nepal, Japanese class Pokhara, English speaking course',
                'aeo_summary' => 'The Global Language Academy category supports students and professionals who need stronger communication skills.',
            ],
            [
                'name' => 'Academic Support and Events',
                'slug' => 'other-classes',
                'image' => 'site/img/cat-4.jpg',
                'status' => 'active',
                'order_priority' => 50,
                'description' => 'Course information sessions, workshops, academic support, and short skill-based classes for learners and parents.',
                'meta_title' => 'Academic Support and Events in Pokhara | Golden Eye Academy',
                'meta_description' => 'Ask about course information, workshops, academic support, and events at Golden Eye Academy in Pokhara.',
                'meta_keywords' => 'academic support Pokhara, student support Nepal, academy events Pokhara',
                'aeo_summary' => 'Golden Eye Academy provides academic support and events for learners who need clear course and class information before enrollment.',
            ],
        ];

        foreach ($categories as $category) {
            CourseCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }
    }
}
