<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\FAQ;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CourseFaqMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_fresh_migration_sequence_succeeds_and_ends_with_final_schema(): void
    {
        $this->assertTrue(Schema::hasTable('course_faq'));
        $this->assertFalse(Schema::hasColumn('course_faq', 'id'));
        $this->assertTrue(Schema::hasColumn('course_faq', 'course_id'));
        $this->assertTrue(Schema::hasColumn('course_faq', 'faq_id'));

        $foreignKeys = DB::select('PRAGMA foreign_key_list(course_faq)');
        $this->assertCount(2, $foreignKeys);
        foreach ($foreignKeys as $fk) {
            $this->assertEquals('CASCADE', $fk->on_delete);
        }
    }

    public function test_legacy_schema_upgrades_to_final_and_preserves_assignments(): void
    {
        // Setup legacy state manually
        Schema::dropIfExists('course_faq');
        Schema::create('course_faq', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('faq_id')->constrained('f_a_q_s')->cascadeOnDelete();
            $table->unique(['course_id', 'faq_id']);
        });

        $course = Course::factory()->create();
        $faq = FAQ::factory()->create();
        DB::table('course_faq')->insert(['course_id' => $course->id, 'faq_id' => $faq->id]);

        DB::table('migrations')->where('migration', 'like', '%rebuild_course_faq_table%')->delete();
        $this->artisan('migrate', ['--path' => 'database/migrations/2026_08_06_230629_rebuild_course_faq_table.php']);

        $this->assertTrue(Schema::hasTable('course_faq'));
        $this->assertFalse(Schema::hasColumn('course_faq', 'id'));
        $this->assertDatabaseHas('course_faq', ['course_id' => $course->id, 'faq_id' => $faq->id]);
    }

    public function test_duplicate_legacy_assignments_cause_safe_failure(): void
    {
        Schema::dropIfExists('course_faq');
        Schema::create('course_faq', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('faq_id')->constrained('f_a_q_s')->cascadeOnDelete();
        });

        $course = Course::factory()->create();
        $faq = FAQ::factory()->create();

        DB::table('course_faq')->insert(['course_id' => $course->id, 'faq_id' => $faq->id]);
        DB::table('course_faq')->insert(['course_id' => $course->id, 'faq_id' => $faq->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('duplicate (course_id, faq_id) pairs exist');
        DB::table('migrations')->where('migration', 'like', '%rebuild_course_faq_table%')->delete();
        $this->artisan('migrate', ['--path' => 'database/migrations/2026_08_06_230629_rebuild_course_faq_table.php']);
    }

    public function test_orphaned_relationships_cause_safe_failure(): void
    {
        Schema::dropIfExists('course_faq');
        Schema::create('course_faq', function (Blueprint $table) {
            $table->id();
            $table->integer('course_id');
            $table->integer('faq_id');
        });

        DB::table('course_faq')->insert(['course_id' => 99999, 'faq_id' => 88888]);

        $this->expectException(\Exception::class);
        DB::table('migrations')->where('migration', 'like', '%rebuild_course_faq_table%')->delete();
        $this->artisan('migrate', ['--path' => 'database/migrations/2026_08_06_230629_rebuild_course_faq_table.php']);
    }

    public function test_rollback_restores_legacy_schema_and_preserves_assignments(): void
    {
        $course = Course::factory()->create();
        $faq = FAQ::factory()->create();
        DB::table('course_faq')->insert(['course_id' => $course->id, 'faq_id' => $faq->id]);

        $this->assertFalse(Schema::hasColumn('course_faq', 'id'));

        $this->artisan('migrate:rollback', ['--path' => 'database/migrations/2026_08_06_230629_rebuild_course_faq_table.php']);

        $this->assertTrue(Schema::hasColumn('course_faq', 'id'));
        $this->assertDatabaseHas('course_faq', ['course_id' => $course->id, 'faq_id' => $faq->id]);

        // Reapply
        $this->artisan('migrate', ['--path' => 'database/migrations/2026_08_06_230629_rebuild_course_faq_table.php']);
        $this->assertFalse(Schema::hasColumn('course_faq', 'id'));
        $this->assertDatabaseHas('course_faq', ['course_id' => $course->id, 'faq_id' => $faq->id]);
    }
}
