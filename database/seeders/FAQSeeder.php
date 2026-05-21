<?php

namespace Database\Seeders;

use App\Models\FAQ;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['What courses does GoldenEye Academy offer?', 'GoldenEye Academy offers IELTS, PTE, Japanese, Korean, English, computer office skills, web development, and course guidance before enrollment.', 10],
            ['Can I ask for help before choosing a course?', 'Yes. Students, parents, job seekers, and study abroad applicants can ask for quick course help before choosing a course.', 20],
            ['What should I choose after SEE or Plus Two?', 'The right path depends on your goal, timeline, budget, interest, and current level. Our team compares study abroad, language, IT, and job-skill options with you.', 30],
            ['Do you help parents understand course options?', 'Yes. Parents can talk with the team about course outcomes, class timing, fees, student readiness, and next-step planning.', 40],
            ['Are IELTS and PTE both available?', 'Yes. GoldenEye Academy offers both IELTS and PTE preparation. The team helps students choose based on destination, requirement, timeline, and test preference.', 50],
            ['Do you provide Japanese and Korean classes?', 'Yes. We provide Japanese JLPT preparation and Korean language or EPS-TOPIK preparation for learners planning study, work, or migration pathways.', 60],
            ['Can beginners join computer courses?', 'Yes. Beginners can join foundation computer, office package, and digital productivity courses. No advanced technical background is required.', 70],
            ['Do I need coding experience for web development?', 'No. The web development course starts with foundations and moves toward practical projects, Laravel, databases, and deployment concepts.', 80],
            ['Are classes practical or only theoretical?', 'Courses are designed around practical learning, guided exercises, mock tests, assignments, and project-based outputs where relevant.', 90],
            ['Do you provide certificates?', 'Yes. Students who complete course requirements receive certificates that can support job applications, academic profiles, and skill proof.', 100],
            ['Are flexible class timings available?', 'Yes. Morning, day, and evening timing options may be available depending on the program and batch schedule.', 110],
            ['Can working professionals join?', 'Yes. Working professionals can choose short courses, office skills, English communication, or flexible language/test-prep batches.', 120],
            ['How much do courses cost?', 'Fees vary by program, duration, and batch. The team can explain the current fee, timing, and available options before enrollment.', 130],
            ['Is there an online learning option?', 'Some programs may support online or hybrid guidance depending on the course structure. Contact the team for the current batch format.', 140],
            ['How do I enroll?', 'You can submit the course guidance form, contact the academy, or use the WhatsApp chat CTA. The team will confirm your goal and guide the next step.', 150],
            ['What happens after I submit the form?', 'The team receives your details, reviews the selected course or course-help request, and contacts you for confirmation and guidance.', 160],
            ['Can I ask for a course recommendation?', 'Yes. Select the option for help choosing the right program, and the team will contact you with a practical recommendation.', 170],
            ['Do you run events and workshops?', 'Yes. GoldenEye Academy can run workshops, skill sessions, course-help events, and career-focused activities based on schedule and demand.', 180],
            ['Can I switch course after getting guidance?', 'Course changes depend on batch status and availability. The team will help you avoid wrong enrollment before payment whenever possible.', 190],
            ['Where is GoldenEye Academy located?', 'GoldenEye Academy is based around Srijana Chowk, Pokhara. Contact the team for exact visit timing and location support.', 200],
        ];

        foreach ($faqs as [$question, $answer, $priority]) {
            FAQ::updateOrCreate(
                ['question' => $question],
                [
                    'answer' => $answer,
                    'status' => 'active',
                    'order_priority' => $priority,
                    'meta_title' => $question.' | GoldenEye Academy FAQ',
                    'meta_description' => $answer,
                    'meta_keywords' => 'GoldenEye Academy FAQ, '.$question,
                    'aeo_summary' => $answer,
                    'schema_markup' => json_encode([
                        '@context' => 'https://schema.org',
                        '@type' => 'Question',
                        'name' => $question,
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $answer,
                        ],
                    ]),
                ],
            );
        }
    }
}
