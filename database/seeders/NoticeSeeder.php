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
                'title' => 'Free Course Roadmap Help',
                'subtitle' => 'Not sure whether to choose IELTS, PTE, Korean, Japanese, computer skills, or web development? Send a quick course-help request first.',
                'badge' => 'Guidance First',
                'image' => 'site/img/carousel-1.png',
                'link' => '/join-now?course=undecided',
                'button_text' => 'Ask for Course Help',
                'status' => 'active',
                'display_type' => 'popup',
                'is_urgent' => false,
            ],
            [
                'title' => 'New Batches Open This Month',
                'subtitle' => 'IELTS, PTE, Korean, Japanese, computer office skills, and web development batches are accepting inquiries.',
                'badge' => 'Admissions Open',
                'image' => 'site/img/premium.png',
                'link' => '/courses-all',
                'button_text' => 'Explore Programs',
                'status' => 'active',
                'display_type' => 'bar',
                'is_urgent' => true,
            ],
            [
                'title' => 'Parent and Student Help Desk',
                'subtitle' => 'Parents can discuss course direction, timing, fees, and student readiness before enrollment.',
                'badge' => 'For Parents',
                'image' => 'site/img/about.jpg',
                'link' => '/contact',
                'button_text' => 'Ask a Question',
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
