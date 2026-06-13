<?php

namespace Database\Seeders;

use App\Models\FAQ;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['What courses does Golden Eye Academy offer?', 'Golden Eye Academy offers IELTS, PTE, Japanese, Korean, English, computer office skills, web development, IT classes, and course information before enrollment.', 10],
            ['Can I visit before enrollment?', 'Yes. Students, parents, and learners can contact or visit the academy team to ask about classes, batch timing, fees, and course options before enrollment.', 20],
            ['What should I choose after SEE or Plus Two?', 'The right class depends on your goal, timeline, budget, interest, and current level. Our team explains language, IT, computer, and practical class options with you.', 30],
            ['Do you help parents understand course options?', 'Yes. Parents can talk with the team about course outcomes, class timing, fees, student readiness, and next-step planning.', 40],
            ['Are IELTS and PTE both available?', 'Yes. Golden Eye Academy offers both IELTS and PTE preparation with exam-focused classes, mock tests, practice, and instructor feedback.', 50],
            ['Do you provide Japanese and Korean classes?', 'Yes. We provide Japanese JLPT preparation and Korean language or EPS-TOPIK preparation for learners with study, work, language, or exam preparation goals.', 60],
            ['Can beginners join computer courses?', 'Yes. Beginners can join foundation computer, office package, and digital productivity courses. No advanced technical background is required.', 70],
            ['Do I need coding experience for web development?', 'No. The web development course starts with foundations and moves toward practical projects, Laravel, databases, and deployment concepts.', 80],
            ['Are classes practical or only theoretical?', 'Courses are designed around practical learning, guided exercises, mock tests, assignments, and project-based outputs where relevant.', 90],
            ['Do you provide certificates?', 'Yes. Students who complete course requirements receive certificates that can support job applications, academic profiles, and skill proof.', 100],
            ['Are flexible class timings available?', 'Yes. Morning, day, and evening timing options may be available depending on the program and batch schedule.', 110],
            ['Can working professionals join?', 'Yes. Working professionals can choose short courses, office skills, English communication, or flexible language/test-prep batches.', 120],
            ['How much do courses cost?', 'Fees vary by program, duration, and batch. The team can explain the current fee, timing, and available options before enrollment.', 130],
            ['Is there an online learning option?', 'Some programs may support online or hybrid guidance depending on the course structure. Contact the team for the current batch format.', 140],
            ['How do I enroll?', 'You can submit the course help form, contact the academy, or use the WhatsApp chat CTA. The team will confirm your class interest and explain the next step.', 150],
            ['What happens after I submit the form?', 'The team receives your details, reviews the selected course or course-help request, and contacts you with class and enrollment support.', 160],
            ['Can I compare course options before enrollment?', 'Yes. Select the option for help choosing the right course, and the academy team will explain suitable class and batch options.', 170],
            ['Do you run events and workshops?', 'Yes. Golden Eye Academy can run workshops, skill sessions, academic support events, and career-focused activities based on schedule and demand.', 180],
            ['Can I switch course after enrollment?', 'Course changes depend on batch status, seat availability, and class progress. The team will explain available options clearly.', 190],
            ['Where is Golden Eye Academy located?', 'Golden Eye Academy is based around Srijana Chowk, Pokhara. Contact the team for exact visit timing and location support.', 200],
        ];

        foreach ($faqs as [$question, $answer, $priority]) {
            FAQ::updateOrCreate(
                ['question' => $question],
                [
                    'answer' => $answer,
                    'status' => 'active',
                    'order_priority' => $priority,
                    'meta_title' => $question.' | Golden Eye Academy FAQ',
                    'meta_description' => $answer,
                    'meta_keywords' => 'Golden Eye Academy FAQ, '.$question,
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
