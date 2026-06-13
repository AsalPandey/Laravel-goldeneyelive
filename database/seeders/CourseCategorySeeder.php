<?php

namespace Database\Seeders;

use App\Models\CourseCategory;
use Illuminate\Database\Seeder;

class CourseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'IELTS, PTE and Language Preparation',
                'slug' => 'study-abroad-test-prep',
                'image' => 'site/img/ielts-preparation.jpg',
                'status' => 'active',
                'order_priority' => 10,
                'description' => 'IELTS, PTE, SAT, Japanese, Korean, and English classes for learners preparing for exam and language goals.',
                'meta_title' => 'IELTS, PTE and Language Classes in Pokhara | Golden Eye Academy',
                'meta_description' => 'Prepare for IELTS, PTE, SAT, Japanese, Korean, and English classes with Golden Eye Academy in Pokhara.',
                'meta_keywords' => 'IELTS Pokhara, PTE Pokhara, Japanese class Pokhara, Korean class Pokhara, English class',
                'aeo_summary' => 'Golden Eye Academy offers IELTS, PTE, SAT, Japanese, Korean, and English preparation classes for learners in Pokhara.',
            ],
            [
                'name' => 'Computer and Office Skills',
                'slug' => 'computer-classes',
                'image' => 'site/img/computer-office-package.jpg',
                'status' => 'active',
                'order_priority' => 20,
                'description' => 'Practical computer, office package, Excel, office file handling, and digital productivity programs for students and job seekers.',
                'meta_title' => 'Computer and Office Skills Course in Pokhara | Golden Eye Academy',
                'meta_description' => 'Learn practical computer and office skills including Word, Excel, PowerPoint, email, internet, and workplace productivity.',
                'meta_keywords' => 'computer course Pokhara, office package Nepal, Excel training Pokhara',
                'aeo_summary' => 'The Computer and Office Skills category prepares learners for daily workplace tasks and stronger job readiness.',
            ],
            [
                'name' => 'Web Development and IT Career',
                'slug' => 'web-development-it-career',
                'image' => 'site/img/basic-web-development.jpg',
                'status' => 'active',
                'order_priority' => 30,
                'description' => 'Project-based coding, Laravel, web development, and software-career programs for beginners and upskilling learners.',
                'meta_title' => 'Web Development Course in Pokhara | Golden Eye Academy',
                'meta_description' => 'Build job-ready web development skills with practical HTML, CSS, Laravel, database, API, and deployment training.',
                'meta_keywords' => 'web development Pokhara, Laravel course Nepal, coding class Pokhara',
                'aeo_summary' => 'Golden Eye Academy offers practical web development classes for learners who want a software career path.',
            ],
            [
                'name' => 'Global Language Academy',
                'slug' => 'language-classes',
                'image' => 'site/img/advanced-english.jpg',
                'status' => 'active',
                'order_priority' => 40,
                'description' => 'English communication, Japanese, Korean, Chinese, and professional language classes for academic and career mobility.',
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
