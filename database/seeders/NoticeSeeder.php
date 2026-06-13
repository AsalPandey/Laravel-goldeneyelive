<?php

namespace Database\Seeders;

use App\Models\Notice;
use Illuminate\Database\Seeder;

class NoticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notices = [
            [
                'title' => 'Course Information Before Enrollment',
                'subtitle' => 'Ask about IELTS/PTE, Korean, Japanese, English, computer, office, web development, or IT classes before choosing your batch.',
                'badge' => 'Academy Support',
                'image' => 'site/img/carousel-1.png',
                'link' => '/join-now?course=undecided&selected_course=undecided&source_page=notice&source_section=course-roadmap-popup&inquiry_intent=course_guidance',
                'button_text' => 'Ask for Course Help',
                'status' => 'active',
                'display_type' => 'popup',
                'is_urgent' => false,
            ],
            [
                'title' => 'Ask About Current Batch Timings',
                'subtitle' => 'Ask about current IELTS, PTE, Korean, Japanese, computer office skills, and web development batch timing before enrollment.',
                'badge' => 'Batch Help',
                'image' => 'site/img/premium.png',
                'link' => '/courses-all',
                'button_text' => 'View Course Details',
                'status' => 'active',
                'display_type' => 'bar',
                'is_urgent' => false,
            ],
            [
                'title' => 'Parent and Student Support Desk',
                'subtitle' => 'Parents can discuss class options, timing, fees, and student readiness before enrollment.',
                'badge' => 'For Parents',
                'image' => 'site/img/about.jpg',
                'link' => '/join-now?course=undecided&selected_course=undecided&source_page=notice&source_section=parent-help-desk&inquiry_intent=course_guidance',
                'button_text' => 'Ask for Course Help',
                'status' => 'inactive',
                'display_type' => 'standard',
                'is_urgent' => false,
            ],
        ];

        foreach ($notices as $notice) {
            Notice::updateOrCreate(
                ['title' => $notice['title']],
                $notice,
            );
        }
    }
}
