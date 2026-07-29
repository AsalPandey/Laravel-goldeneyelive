<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Carbon\CarbonImmutable;
use Database\Seeders\Concerns\PreventsProductionBaselineSeeding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    use PreventsProductionBaselineSeeding;

    private const BASELINE_PUBLISHED_AT = '2026-07-01 00:00:00';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->preventProductionBaselineSeeding();

        $posts = [
            [
                'title' => 'Which Course Should I Choose After SEE or Plus Two?',
                'slug' => 'which-course-should-i-choose-after-see-or-plus-two',
                'category' => 'Student Guide',
                'image' => 'site/img/carousel-1.png',
                'author' => 'Golden Eye Academy',
                'content' => <<<'HTML'
<p>Choosing a class after SEE or Plus Two is easier when you start with the problem you want to solve. A popular course is not automatically the right course for your current level, available time, or next learning goal.</p>
<h2>Start with your immediate goal</h2>
<p>Write down one clear goal. You may want to improve English for an exam, learn a language, become more confident with office software, or explore web development. If you have several goals, decide which one needs attention first.</p>
<h2>Compare the information already available</h2>
<ul>
<li>Read the course description and outline.</li>
<li>Check the published fee and duration.</li>
<li>Review the listed instructor information.</li>
<li>Ask which batch times and seats are currently available.</li>
</ul>
<h2>Check the starting level</h2>
<p>A course name alone may not explain the expected starting skills. Ask whether the class begins with foundations, whether a level check is needed, and what practice students are expected to complete outside class.</p>
<h2>Make the decision with your family</h2>
<p>Students and parents can compare the same information together. Before enrolling, confirm the current schedule, total fee, learning areas, and any course-specific requirements directly with the academy.</p>
HTML,
                'meta_keywords' => 'after SEE course, after plus two course, student support Nepal',
            ],
            [
                'title' => 'IELTS or PTE: How to Choose the Right Test',
                'slug' => 'ielts-or-pte-how-to-choose-the-right-test',
                'category' => 'Exam Preparation',
                'image' => 'site/img/ielts-preparation.jpg',
                'author' => 'Golden Eye Academy',
                'content' => <<<'HTML'
<p>IELTS and PTE are different English-language tests. Choosing between them should begin with the exact requirement of the institution or organization that will receive your result, not with a promise that one test is easier.</p>
<h2>Verify the requirement first</h2>
<p>Check the official requirement for your intended use and confirm which test, test type, score, and validity rules apply. Requirements can change, so use official test-provider and receiving-organization information for the final decision.</p>
<h2>Compare how you prefer to practise</h2>
<ul>
<li>Consider whether you are comfortable reading and responding on a computer.</li>
<li>Think about how you handle timed listening, reading, writing, and speaking tasks.</li>
<li>Review official sample materials for both tests.</li>
<li>Notice which format helps you show your current English ability more clearly.</li>
</ul>
<h2>Ask a class provider practical questions</h2>
<p>Before joining a preparation class, ask what the published course outline covers, how practice is reviewed, what equipment is used, and which current batch fits your availability. Do not assume that a class can guarantee a score.</p>
<h2>Choose a preparation routine you can maintain</h2>
<p>Your decision should account for your current level, available study time, target requirement, and willingness to practise consistently. Confirm the current course details before enrollment.</p>
HTML,
                'meta_keywords' => 'IELTS vs PTE, PTE Pokhara, IELTS Pokhara',
            ],
            [
                'title' => 'Why Office Skills Still Matter for Job Seekers',
                'slug' => 'why-office-skills-still-matter-for-job-seekers',
                'category' => 'Career Skills',
                'image' => 'site/img/computer-office-package.jpg',
                'author' => 'Golden Eye Academy',
                'content' => <<<'HTML'
<p>Office-computer skills can support study, administration, communication, and everyday document work. A useful course should help a learner understand the tools and practise common tasks, without promising employment.</p>
<h2>Identify the tasks you need to learn</h2>
<p>Make a list of the work you currently find difficult. Examples may include formatting documents, organising files, using spreadsheets, preparing slides, writing email, or managing information safely.</p>
<h2>Review the published course outline</h2>
<ul>
<li>Check which applications and topics are listed.</li>
<li>Ask whether the course begins at your current level.</li>
<li>Confirm how much time is available for guided practice.</li>
<li>Ask what device or software access is expected.</li>
</ul>
<h2>Practise with realistic examples</h2>
<p>Learning is easier to assess when you can create and explain your own files. Keep copies of completed documents, spreadsheets, or presentations so you can review your progress and identify what still needs practice.</p>
<h2>Confirm current details before enrolling</h2>
<p>Check the current fee, duration, batch timing, instructor information, and any completion requirements with the academy. A course can support skill development, but employment outcomes depend on many factors outside the course.</p>
HTML,
                'meta_keywords' => 'office package Pokhara, computer course Nepal, Excel training',
            ],
            [
                'title' => 'How Web Development Builds a Career Portfolio',
                'slug' => 'how-web-development-builds-a-career-portfolio',
                'category' => 'IT Career',
                'image' => 'site/img/basic-web-development.jpg',
                'author' => 'Golden Eye Academy',
                'content' => <<<'HTML'
<p>A web-development portfolio is a collection of work you can demonstrate and explain. It does not guarantee a job, but it can help you review your own progress and show how you approached a technical problem.</p>
<h2>Begin with small, complete projects</h2>
<p>A finished small project is often easier to discuss than a large unfinished idea. Start with pages or applications that have a clear purpose, readable structure, and a working user flow.</p>
<h2>Record what each project demonstrates</h2>
<ul>
<li>The problem or user need you addressed.</li>
<li>The technologies and techniques you used.</li>
<li>The parts you completed yourself.</li>
<li>The difficulty you encountered and how you tested the result.</li>
</ul>
<h2>Keep presentation and safety in mind</h2>
<p>Remove passwords, personal data, private keys, and confidential information before sharing a project. Use clear screenshots or a safe demonstration and explain any feature that is not available publicly.</p>
<h2>Compare a course with your learning goal</h2>
<p>Read the course description and outline to see which technologies are listed. Ask about the expected starting level, current schedule, available instructor information, and how project work is handled before enrollment.</p>
HTML,
                'meta_keywords' => 'web development course Pokhara, Laravel course Nepal, coding portfolio',
            ],
            [
                'title' => 'Korean EPS-TOPIK Preparation: What Beginners Should Know',
                'slug' => 'korean-eps-topik-preparation-what-beginners-should-know',
                'category' => 'Language',
                'image' => 'site/img/eps-topik.jpg',
                'author' => 'Golden Eye Academy',
                'content' => <<<'HTML'
<p>Beginners preparing for Korean-language study need a clear starting routine. Exam policies and requirements should always be checked through official sources; a class should focus on the language areas listed in its published outline.</p>
<h2>Build the reading foundation</h2>
<p>Start by learning Hangul carefully enough to recognise and write common syllable patterns. Rushing past the alphabet can make later vocabulary and listening practice harder to organise.</p>
<h2>Use a repeatable study routine</h2>
<ul>
<li>Review a manageable set of words regularly.</li>
<li>Listen to short examples and check what you understood.</li>
<li>Read simple sentences without relying only on transliteration.</li>
<li>Record mistakes and revisit them.</li>
</ul>
<h2>Separate language preparation from application advice</h2>
<p>Language classes and consulting are different services. Confirm official employment, application, or eligibility rules with the responsible official source or appropriate consulting provider.</p>
<h2>Questions to ask before joining</h2>
<p>Check the course outline, duration, current fee, starting level, instructor information, and current batch timing. No class should promise an exam, employment, or migration outcome.</p>
HTML,
                'meta_keywords' => 'EPS TOPIK Nepal, Korean class Pokhara, Korean language',
            ],
            [
                'title' => 'Japanese JLPT N5: A Practical Starting Plan',
                'slug' => 'japanese-jlpt-n5-a-practical-starting-plan',
                'category' => 'Language',
                'image' => 'site/img/jlpt-n5.jpg',
                'author' => 'Golden Eye Academy',
                'content' => <<<'HTML'
<p>JLPT N5 learners usually need a foundation in Japanese scripts, vocabulary, grammar, reading, and listening. Use the official test information for the current exam structure and policies.</p>
<h2>Organise the first learning areas</h2>
<p>Hiragana and Katakana provide an important reading base. After that, learners can gradually add the vocabulary, grammar patterns, Kanji, reading, and listening areas listed in their learning plan.</p>
<h2>Practise in short, regular sessions</h2>
<ul>
<li>Read and write a small set of characters.</li>
<li>Review vocabulary in simple sentences.</li>
<li>Listen to short examples more than once.</li>
<li>Keep a record of recurring mistakes.</li>
</ul>
<h2>Use official material for exam decisions</h2>
<p>Registration dates, fees, venues, score rules, and test policies can change. Check the official JLPT information rather than relying on an old article or informal claim.</p>
<h2>Review the class before enrollment</h2>
<p>Compare the published outline with your current level. Confirm the fee, duration, current batch timing, instructor information, and expected study routine directly with the academy.</p>
HTML,
                'meta_keywords' => 'JLPT N5 Pokhara, Japanese language Nepal, Japan study',
            ],
            [
                'title' => 'Parents Guide: How to Evaluate an Academy',
                'slug' => 'parents-guide-how-to-evaluate-a-training-institute',
                'category' => 'Parent Guide',
                'image' => 'site/img/about.jpg',
                'author' => 'Golden Eye Academy',
                'content' => <<<'HTML'
<p>Parents can support a better course decision by checking the information that affects the student directly. A clear comparison is more useful than relying only on advertising, a low fee, or a friend’s choice.</p>
<h2>Start with the student’s current need</h2>
<p>Ask the student what they want to improve and why. Compare that goal with the published course description, outline, duration, and expected starting level.</p>
<h2>Questions to ask before payment</h2>
<ul>
<li>What is the current total fee and duration?</li>
<li>Which batch times and seats are currently available?</li>
<li>Who is expected to teach the current batch?</li>
<li>What learning areas are included in the published outline?</li>
<li>Are there certificate or completion requirements?</li>
<li>What is not guaranteed by the course?</li>
</ul>
<h2>Check evidence carefully</h2>
<p>Do not rely on an unverified testimonial, result, partnership, or qualification claim. Ask for information that can be checked and confirm that any student feedback was published with appropriate permission.</p>
<h2>Review the decision together</h2>
<p>After gathering the details, discuss whether the schedule, fee, course focus, and practice expectations are realistic for the student. Contact the academy again if any important detail remains unclear.</p>
HTML,
                'meta_keywords' => 'parent guide academy, course information Nepal',
            ],
            [
                'title' => 'Why You Should Ask Before Enrollment',
                'slug' => 'why-you-should-ask-before-enrollment',
                'category' => 'Admissions',
                'image' => 'site/img/cat-4.jpg',
                'author' => 'Golden Eye Academy',
                'content' => <<<'HTML'
<p>Asking questions before enrollment helps you compare the published information with your actual goal. It also gives the academy a chance to clarify current details that may not be stored on a course page.</p>
<h2>Information to review online</h2>
<p>Read the course name, description, outline, fee, duration, category, and listed instructor. Note anything that is empty, unclear, or likely to change.</p>
<h2>Current details to confirm directly</h2>
<ul>
<li>Available batch timing and seat availability.</li>
<li>The instructor expected for the current batch.</li>
<li>Any equipment, software, books, or starting skills required.</li>
<li>Certificate or completion conditions, if relevant.</li>
<li>The payment and course-change policy.</li>
</ul>
<h2>Share enough context</h2>
<p>Explain your current level, learning goal, available time, and the course you are considering. This makes the conversation more specific without requiring you to share unnecessary personal information.</p>
<h2>Keep realistic expectations</h2>
<p>A course can provide classes and learning support, but it cannot guarantee a score, visa, admission, job, or placement. Confirm what the course includes and what still depends on your attendance and practice.</p>
HTML,
                'meta_keywords' => 'course information Pokhara, free course help Nepal, enrollment support',
            ],
        ];

        foreach ($posts as $index => $post) {
            $post['status'] = 'published';
            $post['published_at'] = BlogPost::where('slug', $post['slug'])->value('published_at')
                ?? CarbonImmutable::parse(self::BASELINE_PUBLISHED_AT)->subDays($index);
            $post['meta_title'] = $post['title'].' | Golden Eye Academy';
            $post['meta_description'] = Str::limit(strip_tags($post['content']), 155, '');
            $post['aeo_summary'] = Str::limit(strip_tags($post['content']), 240, '');
            $post['schema_markup'] = json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post['title'],
                'author' => $post['author'],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'Golden Eye Academy',
                ],
            ]);

            BlogPost::updateOrCreate(
                ['slug' => $post['slug']],
                $post,
            );
        }
    }
}
