<?php

namespace App\Support;

final class ApprovedCourseFaqDeploymentData
{
    public const CERTIFICATE_QUESTION = 'Do Golden Eye Academy courses include certificates?';

    public const CERTIFICATE_ANSWER = 'Golden Eye Academy provides a certificate after completion of each course. Course-specific completion requirements may be confirmed with the academy team.';

    public const BATCH_TIMING_QUESTION = 'How do I find the current batch timing?';

    public const INSTRUCTOR_QUESTION = 'Who will teach my class?';

    public const PRICE_DURATION_QUESTION = 'How much do courses cost and how long do they take?';

    public const BEGINNER_QUESTION = 'Can beginners join Golden Eye Academy courses?';

    public const PRACTICAL_QUESTION = 'What practical activities are included in classes?';

    public const STUDY_ABROAD_QUESTION = 'Where should I ask about study-abroad preparation?';

    public const COURSE_HELP_QUESTION = 'Can I ask for help before choosing a course?';

    public const AFTER_SCHOOL_QUESTION = 'How should I choose a course after SEE or Plus Two?';

    public const PARENT_QUESTION = 'Can parents discuss course options with the academy?';

    public const INQUIRY_QUESTION = 'What information should I provide during an inquiry?';

    public const AFTER_COURSE_HELP_QUESTION = 'What happens after I submit Course Help?';

    public const VISIT_QUESTION = 'Can I visit Golden Eye Academy before enrolling?';

    public const IELTS_FEEDBACK_QUESTION = 'What feedback is provided during IELTS mock practice?';

    public const PTE_COMPUTER_QUESTION = 'Does PTE training include computer-based practice?';

    public const JLPT_PREREQUISITE_QUESTION = 'Do I need to complete JLPT N5 before joining JLPT N4?';

    public const WEB_LAPTOP_QUESTION = 'Do I need to bring a laptop for the Web Development course?';

    /**
     * @return array<string, array{name: string, instructor: string, price: string, duration: string}>
     */
    public static function courseMetadata(): array
    {
        return [
            'ielts-masterclass' => ['name' => 'IELTS Masterclass for Band 7+', 'instructor' => 'Saroj Giri', 'price' => 'Rs. 7,000', 'duration' => '6 Weeks'],
            'pte-elite-training' => ['name' => 'PTE Elite Academic Training', 'instructor' => 'Saroj Giri', 'price' => 'Rs. 7,000', 'duration' => '6 Weeks'],
            'jlpt-n5-elite' => ['name' => 'Japanese Proficiency JLPT N5', 'instructor' => 'Navaraj Thapa', 'price' => 'Rs. 15,000', 'duration' => '6 Months'],
            'jlpt-n4' => ['name' => 'Japanese Proficiency JLPT N4', 'instructor' => 'Navaraj Thapa', 'price' => 'Rs. 13,000', 'duration' => '3 Months'],
            'professional-korean-eps' => ['name' => 'Professional Korean EPS-TOPIK', 'instructor' => 'Pradeep Paudel', 'price' => 'Rs. 18,000', 'duration' => '6 Months'],
            'basic-korean-course' => ['name' => 'Basic Korean Course', 'instructor' => 'Pradeep Paudel', 'price' => 'Rs. 10,000', 'duration' => '3 Months'],
            'global-english-pro' => ['name' => 'Global English Professional Track', 'instructor' => 'Saroj Giri', 'price' => 'Rs. 7,000', 'duration' => '45 Days'],
            'basic-english-course' => ['name' => 'Basic English Foundation', 'instructor' => 'Saroj Giri', 'price' => 'Rs. 5,000', 'duration' => '45 Days'],
            'professional-web-development' => ['name' => 'Professional Web Development', 'instructor' => 'Asal Pandey', 'price' => 'Rs. 24,850', 'duration' => '3 Months'],
            'advanced-computer-diploma' => ['name' => 'Advanced Diploma in Computer Science', 'instructor' => 'Sanju Khanal', 'price' => 'Rs. 14,000', 'duration' => '3 Months'],
            'corporate-office-package' => ['name' => 'Corporate Office and Admin Package', 'instructor' => 'Sanju Khanal', 'price' => 'Rs. 7,000', 'duration' => '3 Months'],
            'chinese-language-course' => ['name' => 'Chinese Language Starter', 'instructor' => 'Chham Maya Rai', 'price' => 'Rs. 15,000', 'duration' => '1 Month'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function targetCourses(): array
    {
        return [
            ...array_map(
                fn (array $course): string => $course['name'],
                self::courseMetadata(),
            ),
            'free-course-roadmap-help' => 'Free Course Roadmap Help',
        ];
    }

    /**
     * @return array<string, array{answer: string, order_priority: int, status: string}>
     */
    public static function approvedFaqs(): array
    {
        return [
            self::IELTS_FEEDBACK_QUESTION => [
                'answer' => 'IELTS mock practice includes mock marking, score-improvement guidance and personalized feedback. Individual progress depends on the learner’s starting level, attendance, practice and continued effort.',
                'order_priority' => 201,
                'status' => 'active',
            ],
            self::PTE_COMPUTER_QUESTION => [
                'answer' => 'Yes. PTE training includes computer-lab practice to help students become familiar with computer-based test activities. Ask the academy about the current practice schedule for your batch.',
                'order_priority' => 202,
                'status' => 'active',
            ],
            self::JLPT_PREREQUISITE_QUESTION => [
                'answer' => 'Yes. Students must complete JLPT N5 before joining the JLPT N4 course. Contact the academy if you studied equivalent Japanese content elsewhere and need help confirming the appropriate starting level.',
                'order_priority' => 203,
                'status' => 'active',
            ],
            self::WEB_LAPTOP_QUESTION => [
                'answer' => 'Yes. Students must bring their own laptop for the Web Development course. Ask the academy team if you need guidance before preparing your device for class.',
                'order_priority' => 204,
                'status' => 'active',
            ],
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function matrix(): array
    {
        return [
            'ielts-masterclass' => [self::BATCH_TIMING_QUESTION, self::CERTIFICATE_QUESTION, self::INSTRUCTOR_QUESTION, self::PRICE_DURATION_QUESTION, self::STUDY_ABROAD_QUESTION, self::IELTS_FEEDBACK_QUESTION],
            'pte-elite-training' => [self::BATCH_TIMING_QUESTION, self::CERTIFICATE_QUESTION, self::INSTRUCTOR_QUESTION, self::PRICE_DURATION_QUESTION, self::STUDY_ABROAD_QUESTION, self::PTE_COMPUTER_QUESTION],
            'jlpt-n5-elite' => [self::BEGINNER_QUESTION, self::BATCH_TIMING_QUESTION, self::CERTIFICATE_QUESTION, self::INSTRUCTOR_QUESTION, self::PRICE_DURATION_QUESTION],
            'jlpt-n4' => [self::BATCH_TIMING_QUESTION, self::CERTIFICATE_QUESTION, self::INSTRUCTOR_QUESTION, self::PRICE_DURATION_QUESTION, self::JLPT_PREREQUISITE_QUESTION],
            'professional-korean-eps' => [self::BATCH_TIMING_QUESTION, self::CERTIFICATE_QUESTION, self::INSTRUCTOR_QUESTION, self::PRICE_DURATION_QUESTION],
            'basic-korean-course' => [self::BEGINNER_QUESTION, self::BATCH_TIMING_QUESTION, self::CERTIFICATE_QUESTION, self::INSTRUCTOR_QUESTION, self::PRICE_DURATION_QUESTION],
            'global-english-pro' => [self::BATCH_TIMING_QUESTION, self::CERTIFICATE_QUESTION, self::INSTRUCTOR_QUESTION, self::PRICE_DURATION_QUESTION],
            'basic-english-course' => [self::BEGINNER_QUESTION, self::BATCH_TIMING_QUESTION, self::CERTIFICATE_QUESTION, self::INSTRUCTOR_QUESTION, self::PRICE_DURATION_QUESTION],
            'professional-web-development' => [self::PRACTICAL_QUESTION, self::BATCH_TIMING_QUESTION, self::CERTIFICATE_QUESTION, self::INSTRUCTOR_QUESTION, self::PRICE_DURATION_QUESTION, self::WEB_LAPTOP_QUESTION],
            'advanced-computer-diploma' => [self::BEGINNER_QUESTION, self::PRACTICAL_QUESTION, self::BATCH_TIMING_QUESTION, self::CERTIFICATE_QUESTION, self::INSTRUCTOR_QUESTION, self::PRICE_DURATION_QUESTION],
            'corporate-office-package' => [self::BEGINNER_QUESTION, self::BATCH_TIMING_QUESTION, self::CERTIFICATE_QUESTION, self::INSTRUCTOR_QUESTION, self::PRICE_DURATION_QUESTION],
            'chinese-language-course' => [self::BEGINNER_QUESTION, self::BATCH_TIMING_QUESTION, self::CERTIFICATE_QUESTION, self::INSTRUCTOR_QUESTION, self::PRICE_DURATION_QUESTION],
            'free-course-roadmap-help' => [self::COURSE_HELP_QUESTION, self::AFTER_SCHOOL_QUESTION, self::PARENT_QUESTION, self::INQUIRY_QUESTION, self::AFTER_COURSE_HELP_QUESTION, self::VISIT_QUESTION],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function requiredFaqQuestions(): array
    {
        $questions = array_merge(...array_values(self::matrix()));

        return array_values(array_unique($questions));
    }
}
