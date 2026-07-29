<?php

namespace Database\Seeders;

use App\Models\FAQ;
use Database\Seeders\Concerns\PreventsProductionBaselineSeeding;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    use PreventsProductionBaselineSeeding;

    public function run(): void
    {
        $this->preventProductionBaselineSeeding();

        $faqs = [
            ['What courses does Golden Eye Academy offer?', 'Golden Eye Academy offers IELTS, PTE, Japanese, Korean, English, computer office skills, web development, IT classes, and course information before enrollment.', 10],
            ['Can I visit before enrollment?', 'Contact the academy before travelling to confirm the current opening time and arrange a suitable visit. You can use the visit to ask about courses, fees, and current batch information.', 20],
            ['What should I choose after SEE or Plus Two?', 'The right class depends on your goal, timeline, budget, interest, and current level. Our team explains language, IT, computer, and practical class options with you.', 30],
            ['Do you help parents understand course options?', 'Parents can contact the academy to review the published course description, fee, duration, instructor information, and questions to confirm before enrollment.', 40],
            ['Are IELTS and PTE both available?', 'Yes. Golden Eye Academy offers both IELTS and PTE preparation with exam-focused classes, mock tests, practice, and instructor feedback.', 50],
            ['Do you provide Japanese and Korean classes?', 'Yes. We provide Japanese JLPT preparation and Korean language or EPS-TOPIK preparation for learners with study, work, language, or exam preparation goals.', 60],
            ['Can beginners join computer courses?', 'Review the course name, description, and outline to see whether it starts at foundation level. Ask the academy to confirm the expected starting skills before enrollment.', 70],
            ['Do I need coding experience for web development?', 'No. The web development course starts with foundations and moves toward practical projects, Laravel, databases, and deployment concepts.', 80],
            ['Are classes practical or only theoretical?', 'Courses are designed around practical learning, guided exercises, mock tests, assignments, and project-based outputs where relevant.', 90],
            ['Do you provide certificates?', 'Certificate availability and completion requirements can vary by course. Ask the academy to confirm the current certificate details before enrollment.', 100],
            ['Are flexible class timings available?', 'Class timing depends on the current course and batch. Contact the academy to confirm which schedules are currently available.', 110],
            ['Can working professionals join?', 'Working professionals should compare the published duration and course outline, then ask whether a current batch fits their availability.', 120],
            ['How much do courses cost?', 'Fees vary by program, duration, and batch. The team can explain the current fee, timing, and available options before enrollment.', 130],
            ['Is there an online learning option?', 'Some programs may support online or hybrid guidance depending on the course structure. Contact the team for the current batch format.', 140],
            ['How do I enroll?', 'You can submit the course help form, contact the academy, or use the WhatsApp chat CTA. The team will confirm your class interest and explain the next step.', 150],
            ['What happens after I submit the form?', 'The team receives your details, reviews the selected course or course-help request, and contacts you with class and enrollment support.', 160],
            ['Can I compare course options before enrollment?', 'Yes. Select the option for help choosing the right course, and the academy team will explain suitable class and batch options.', 170],
            ['Do you run events and workshops?', 'Workshop and event availability changes over time. Check the current notices or contact the academy for verified event details.', 180],
            ['Can I switch course after enrollment?', 'Do not assume that a course change will be available. Ask the academy about the current policy, batch status, and any conditions before making payment.', 190],
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
