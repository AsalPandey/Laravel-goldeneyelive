<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('courses') || !Schema::hasTable('f_a_q_s')) {
            throw new \Exception("Missing parent tables: courses or f_a_q_s.");
        }

        if (!Schema::hasTable('course_faq')) {
            $this->createCompositeTable();
            return;
        }

        if (!Schema::hasColumn('course_faq', 'id')) {
            return; 
        }

        $duplicates = DB::table('course_faq')
            ->select('course_id', 'faq_id')
            ->groupBy('course_id', 'faq_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();
            
        if ($duplicates->isNotEmpty()) {
            throw new \Exception("Cannot normalize course_faq: duplicate (course_id, faq_id) pairs exist.");
        }
        
        $orphanedCourses = DB::table('course_faq')
            ->leftJoin('courses', 'course_faq.course_id', '=', 'courses.id')
            ->whereNull('courses.id')
            ->count();
            
        if ($orphanedCourses > 0) {
            throw new \Exception("Cannot normalize course_faq: orphaned course_id records exist.");
        }
        
        $orphanedFaqs = DB::table('course_faq')
            ->leftJoin('f_a_q_s', 'course_faq.faq_id', '=', 'f_a_q_s.id')
            ->whereNull('f_a_q_s.id')
            ->count();
            
        if ($orphanedFaqs > 0) {
            throw new \Exception("Cannot normalize course_faq: orphaned faq_id records exist.");
        }
        
        $assignments = DB::table('course_faq')->get(['course_id', 'faq_id']);
        
        Schema::drop('course_faq');
        $this->createCompositeTable();
        
        if ($assignments->isNotEmpty()) {
            DB::table('course_faq')->insert(
                $assignments->map(fn($row) => (array) $row)->toArray()
            );
        }
        
        $newCount = DB::table('course_faq')->count();
        if ($newCount !== $assignments->count()) {
            throw new \Exception("Data loss detected during course_faq normalization.");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('course_faq')) {
            return;
        }
        
        if (Schema::hasColumn('course_faq', 'id')) {
            return; 
        }
        
        $assignments = DB::table('course_faq')->get(['course_id', 'faq_id']);
        
        Schema::drop('course_faq');
        
        Schema::create('course_faq', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('faq_id')->constrained('f_a_q_s')->cascadeOnDelete();
            $table->unique(['course_id', 'faq_id']);
        });
        
        if ($assignments->isNotEmpty()) {
            DB::table('course_faq')->insert(
                $assignments->map(fn($row) => (array) $row)->toArray()
            );
        }
    }
    
    private function createCompositeTable(): void
    {
        Schema::create('course_faq', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('faq_id')->constrained('f_a_q_s')->cascadeOnDelete();
            $table->primary(['course_id', 'faq_id']);
        });
    }
};
