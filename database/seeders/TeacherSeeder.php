<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            [
                'name' => 'Shankar Pokharel',
                'designation' => 'Founder and Academic Director',
                'photo' => 'site/img/message-chairperson.jpg',
                'bio' => 'Guides the academy vision, parent guidance, course direction, and long-term student development strategy.',
                'is_featured' => true,
            ],
            [
                'name' => 'Course Help Team',
                'designation' => 'Course Roadmap and Admissions',
                'photo' => 'site/img/team-1.jpg',
                'bio' => 'Helps students, parents, abroad applicants, and job seekers compare programs before enrollment.',
                'is_featured' => true,
            ],
            [
                'name' => 'Aakash Subedi',
                'designation' => 'IELTS and PTE Specialist',
                'photo' => 'site/img/team_4.jpg',
                'bio' => 'Coaches test-prep students with mock analysis, writing correction, speaking practice, and score improvement planning.',
                'is_featured' => true,
            ],
            [
                'name' => 'Pradeep Paudel',
                'designation' => 'Senior Korean Instructor',
                'photo' => 'site/img/team-2.jpg',
                'bio' => 'Leads Korean language and EPS-TOPIK preparation with practical listening, reading, and vocabulary drills.',
                'is_featured' => true,
            ],
            [
                'name' => 'Surendra Bhattarai',
                'designation' => 'Computer and Office Skills Lead',
                'photo' => 'site/img/team-3.jpg',
                'bio' => 'Trains students in practical computer skills, office package, Excel, documentation, and digital confidence.',
                'is_featured' => true,
            ],
            [
                'name' => 'Ajay B.K.',
                'designation' => 'English Communication Coach',
                'photo' => 'site/img/team_1.jpg',
                'bio' => 'Supports English learners with speaking confidence, grammar correction, presentations, and interview preparation.',
                'is_featured' => false,
            ],
            [
                'name' => 'Navaraj Thapa',
                'designation' => 'Japanese Language Instructor',
                'photo' => 'site/img/team-4.jpg',
                'bio' => 'Guides Japanese learners through JLPT vocabulary, grammar, scripts, listening, and culture-focused practice.',
                'is_featured' => false,
            ],
        ];

        foreach ($teachers as $teacher) {
            $teacher['status'] = 'active';
            $teacher['facebook_url'] = 'https://www.facebook.com/goldeneyeacademy';
            $teacher['linkedin_url'] = 'https://www.linkedin.com/company/golden-eye-academy/';
            $teacher['meta_title'] = $teacher['name'].' | GoldenEye Academy Faculty';
            $teacher['meta_description'] = $teacher['bio'];
            $teacher['meta_keywords'] = 'GoldenEye Academy faculty, '.$teacher['designation'];
            $teacher['aeo_summary'] = $teacher['name'].' supports GoldenEye Academy learners as '.$teacher['designation'].'.';

            Teacher::updateOrCreate(
                ['name' => $teacher['name']],
                $teacher,
            );
        }
    }
}
