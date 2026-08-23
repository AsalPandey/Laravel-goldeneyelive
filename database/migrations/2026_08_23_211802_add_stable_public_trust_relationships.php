<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table): void {
            $table->foreignId('teacher_id')->nullable()->after('instructor')->constrained()->nullOnDelete();
        });

        Schema::table('testimonials', function (Blueprint $table): void {
            $table->foreignId('course_id')->nullable()->after('course_name')->constrained()->nullOnDelete();
        });

        $teacherIdsByName = DB::table('teachers')->pluck('id', 'name');

        DB::table('courses')
            ->whereNull('teacher_id')
            ->orderBy('id')
            ->get(['id', 'instructor'])
            ->each(function (object $course) use ($teacherIdsByName): void {
                $teacherId = $teacherIdsByName->get($course->instructor);

                if ($teacherId !== null) {
                    DB::table('courses')->where('id', $course->id)->update(['teacher_id' => $teacherId]);
                }
            });

        $courseIdsByName = DB::table('courses')->pluck('id', 'name');

        DB::table('testimonials')
            ->whereNull('course_id')
            ->orderBy('id')
            ->get(['id', 'course_name'])
            ->each(function (object $testimonial) use ($courseIdsByName): void {
                $courseId = $courseIdsByName->get($testimonial->course_name);

                if ($courseId !== null) {
                    DB::table('testimonials')->where('id', $testimonial->id)->update(['course_id' => $courseId]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('course_id');
        });

        Schema::table('courses', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('teacher_id');
        });
    }
};
