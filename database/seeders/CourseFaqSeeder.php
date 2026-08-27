<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\FAQ;
use App\Support\ApprovedCourseFaqDeploymentData;
use Database\Seeders\Concerns\PreventsProductionBaselineSeeding;
use Illuminate\Database\Seeder;
use LogicException;

class CourseFaqSeeder extends Seeder
{
    use PreventsProductionBaselineSeeding;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->preventProductionBaselineSeeding();

        foreach (ApprovedCourseFaqDeploymentData::matrix() as $slug => $questions) {
            $course = Course::query()->where('slug', $slug)->first();

            if ($course === null) {
                throw new LogicException("Cannot seed approved Course-FAQ assignments: course [{$slug}] is missing.");
            }

            $faqIdsByQuestion = FAQ::query()
                ->whereIn('question', $questions)
                ->pluck('id', 'question');

            $missingQuestions = array_values(array_diff($questions, $faqIdsByQuestion->keys()->all()));

            if ($missingQuestions !== []) {
                throw new LogicException(
                    "Cannot seed approved Course-FAQ assignments for [{$slug}]: missing FAQ [{$missingQuestions[0]}].",
                );
            }

            $course->faqs()->sync(
                array_map(
                    fn (string $question): int => (int) $faqIdsByQuestion->get($question),
                    $questions,
                ),
            );
        }
    }
}
