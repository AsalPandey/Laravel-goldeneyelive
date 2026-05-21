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
                'title' => 'Course Guidance Before Enrollment',
                'subtitle' => 'Not sure whether to choose IELTS, PTE, Korean, Japanese, computer skills, or web development? Send a quick course-help request first.',
                'badge' => 'Guidance First',
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
                'title' => 'Parent and Student Help Desk',
                'subtitle' => 'Parents can discuss course direction, timing, fees, and student readiness before enrollment.',
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
