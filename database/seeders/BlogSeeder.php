<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Which Course Should I Choose After SEE or Plus Two?',
                'category' => 'Student Guide',
                'image' => 'site/img/carousel-1.png',
                'author' => 'GoldenEye Course Help Team',
                'content' => '<p>Choosing a course after SEE or Plus Two should start with your goal, timeline, budget, and current skill level. Students should compare study abroad requirements, language needs, computer confidence, and job-readiness before enrolling.</p><p>At GoldenEye Academy, simple guidance comes before course selection. This helps students avoid random enrollment and choose a practical next step.</p>',
                'meta_keywords' => 'after SEE course, after plus two course, student guidance Nepal',
            ],
            [
                'title' => 'IELTS or PTE: How to Choose the Right Test',
                'category' => 'Study Abroad',
                'image' => 'site/img/ielts-preparation.jpg',
                'author' => 'GoldenEye Test Prep Team',
                'content' => '<p>IELTS is widely accepted and includes a human speaking interview. PTE is computer-based, often faster for results, and preferred by students who are comfortable with typed and recorded responses.</p><p>The right choice depends on destination, university requirement, timeline, confidence level, and test style.</p>',
                'meta_keywords' => 'IELTS vs PTE, PTE Pokhara, IELTS Pokhara',
            ],
            [
                'title' => 'Why Office Skills Still Matter for Job Seekers',
                'category' => 'Career Skills',
                'image' => 'site/img/computer-office-package.jpg',
                'author' => 'GoldenEye Career Team',
                'content' => '<p>Office skills are still a practical entry point for many job seekers. Word, Excel, PowerPoint, email, internet research, and document handling are basic expectations in admin, front desk, accounting support, and office roles.</p><p>A focused office package can quickly improve confidence for interviews and workplace tasks.</p>',
                'meta_keywords' => 'office package Pokhara, computer course Nepal, Excel training',
            ],
            [
                'title' => 'How Web Development Builds a Career Portfolio',
                'category' => 'IT Career',
                'image' => 'site/img/basic-web-development.jpg',
                'author' => 'GoldenEye IT Faculty',
                'content' => '<p>Web development is valuable because students can show what they can build. A portfolio with landing pages, dashboards, forms, database projects, and deployed work is stronger than a certificate alone.</p><p>GoldenEye focuses on practical projects so learners can explain their work with confidence.</p>',
                'meta_keywords' => 'web development course Pokhara, Laravel course Nepal, coding portfolio',
            ],
            [
                'title' => 'Korean EPS-TOPIK Preparation: What Beginners Should Know',
                'category' => 'Language',
                'image' => 'site/img/eps-topik.jpg',
                'author' => 'GoldenEye Korean Faculty',
                'content' => '<p>EPS-TOPIK preparation requires consistent vocabulary, listening practice, reading speed, and exam-style discipline. Beginners should first master Hangul and then build daily study habits.</p><p>Structured classes help learners avoid random memorization and stay aligned with exam needs.</p>',
                'meta_keywords' => 'EPS TOPIK Nepal, Korean class Pokhara, Korean language',
            ],
            [
                'title' => 'Japanese JLPT N5: A Practical Starting Plan',
                'category' => 'Language',
                'image' => 'site/img/jlpt-n5.jpg',
                'author' => 'GoldenEye Japanese Faculty',
                'content' => '<p>JLPT N5 starts with Hiragana, Katakana, basic Kanji, daily vocabulary, and simple grammar. Students should focus on small daily practice instead of waiting for long study sessions.</p><p>A good N5 plan balances reading, listening, writing, and conversation exposure.</p>',
                'meta_keywords' => 'JLPT N5 Pokhara, Japanese language Nepal, Japan study',
            ],
            [
                'title' => 'Parents Guide: How to Evaluate a Training Institute',
                'category' => 'Parent Guide',
                'image' => 'site/img/about.jpg',
                'author' => 'GoldenEye Academic Team',
                'content' => '<p>Parents should evaluate an institute by guidance quality, teacher experience, class structure, practical outcomes, communication, and follow-up support. The best decision is not always the cheapest or fastest option.</p><p>Ask what the student will be able to do after the course and how progress will be tracked.</p>',
                'meta_keywords' => 'parent guide training institute, course guidance Nepal',
            ],
            [
                'title' => 'Why You Should Ask Before Enrollment',
                'category' => 'Admissions',
                'image' => 'site/img/cat-4.jpg',
                'author' => 'GoldenEye Admissions Team',
                'content' => '<p>Good guidance reduces wrong enrollment. Students often know they want improvement but are unsure whether they need language, computer, test prep, or career support first.</p><p>A short guidance conversation helps align the course with the student goal.</p>',
                'meta_keywords' => 'course guidance Pokhara, free course help Nepal, admissions guidance',
            ],
        ];

        foreach ($posts as $index => $post) {
            $post['slug'] = Str::slug($post['title']);
            $post['status'] = 'published';
            $post['published_at'] = now()->subDays($index + 1);
            $post['meta_title'] = $post['title'].' | GoldenEye Academy';
            $post['meta_description'] = Str::limit(strip_tags($post['content']), 155, '');
            $post['aeo_summary'] = Str::limit(strip_tags($post['content']), 240, '');
            $post['schema_markup'] = json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post['title'],
                'author' => $post['author'],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'GoldenEye Academy',
                ],
            ]);

            BlogPost::updateOrCreate(
                ['slug' => $post['slug']],
                $post,
            );
        }
    }
}
