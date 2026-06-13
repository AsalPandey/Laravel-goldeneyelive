<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'student_name' => 'Sandesh Mahat',
                'course_name' => 'Web Development with Laravel Basics',
                'photo' => 'site/img/testimonial-1.jpg',
                'content' => 'Golden Eye Academy helped me understand web development step by step. The project practice made the course useful beyond theory.',
                'rating' => 5,
                'is_featured' => true,
            ],
            [
                'student_name' => 'Apekshya Chhetri',
                'course_name' => 'Korean Language',
                'photo' => 'site/img/testimonial-3.jpg',
                'content' => 'The Korean classes were structured and practical. I became more confident with Hangul, listening, and daily conversation.',
                'rating' => 5,
                'is_featured' => true,
            ],
            [
                'student_name' => 'Nirmala Thapa',
                'course_name' => 'IELTS Preparation',
                'photo' => 'site/img/testimonial-4.jpg',
                'content' => 'The IELTS mock tests and speaking feedback helped me understand exactly what to improve before the real exam.',
                'rating' => 5,
                'is_featured' => true,
            ],
            [
                'student_name' => 'Sharap Dorje Gurung',
                'course_name' => 'Computer and Office Skills',
                'photo' => 'site/img/testimonial-2.jpg',
                'content' => 'The office package course improved my confidence in Excel, documents, presentations, and day-to-day computer tasks.',
                'rating' => 5,
                'is_featured' => true,
            ],
            [
                'student_name' => 'Rojina Gurung',
                'course_name' => 'PTE Academic Preparation',
                'photo' => 'site/img/user.png',
                'content' => 'The PTE templates and computer-based practice made the exam feel less confusing. I knew what to focus on each week.',
                'rating' => 5,
                'is_featured' => false,
            ],
            [
                'student_name' => 'Suman Pariyar',
                'course_name' => 'Course Information Before Enrollment',
                'photo' => 'site/img/user.png',
                'content' => 'I was unsure between language and computer courses. The quick class-information session gave me a clear order of what to learn first.',
                'rating' => 5,
                'is_featured' => false,
            ],
            [
                'student_name' => 'Pratiksha Sharma',
                'course_name' => 'Practical English Communication',
                'photo' => 'site/img/user.png',
                'content' => 'My speaking confidence improved through regular practice, correction, and interview-style activities.',
                'rating' => 5,
                'is_featured' => false,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            $testimonial['status'] = 'active';

            Testimonial::updateOrCreate(
                [
                    'student_name' => $testimonial['student_name'],
                    'course_name' => $testimonial['course_name'],
                ],
                $testimonial,
            );
        }
    }
}
