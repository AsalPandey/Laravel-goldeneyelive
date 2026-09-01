<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Teacher;
use App\Support\ApprovedCourseFaqDeploymentData;
use Database\Seeders\Concerns\PreventsProductionBaselineSeeding;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    use PreventsProductionBaselineSeeding;

    public function run(): void
    {
        $this->preventProductionBaselineSeeding();

        $teachers = [
            [
                'name' => 'Shankar Pokharel',
                'designation' => 'Founder and Academic Director',
                'photo' => 'site/img/message-chairperson.jpg',
                'bio' => 'Leads the academy vision, parent communication, course direction, and long-term student development strategy.',
                'is_featured' => true,
            ],
            [
                'name' => 'Academic Support Team',
                'designation' => 'Admissions and Academic Support',
                'photo' => 'site/img/team-1.jpg',
                'bio' => 'Helps students and parents compare current courses and learning options before enrollment.',
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
                'bio' => 'Supports students in practical computer skills, office package, Excel, document handling, and digital confidence.',
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
            $teacher['meta_title'] = $teacher['name'].' | Golden Eye Academy Faculty';
            $teacher['meta_description'] = $teacher['bio'];
            $teacher['meta_keywords'] = 'Golden Eye Academy faculty, '.$teacher['designation'];
            $teacher['aeo_summary'] = $teacher['name'].' supports Golden Eye Academy learners as '.$teacher['designation'].'.';

            Teacher::updateOrCreate(
                ['name' => $teacher['name']],
                $teacher,
            );
        }

        $teachersByName = Teacher::query()->get(['id', 'name'])->keyBy('name');
        $teachersById = $teachersByName->keyBy('id');

        Course::query()
            ->whereIn('slug', array_keys(ApprovedCourseFaqDeploymentData::targetCourses()))
            ->get()
            ->each(function (Course $course) use ($teachersById, $teachersByName): void {
                $teacher = $teachersById->get($course->teacher_id)
                    ?? $teachersByName->get($course->instructor);

                $course->update([
                    'teacher_id' => $teacher?->id,
                    'instructor' => $teacher?->name ?? $course->instructor,
                ]);
            });
    }
}
