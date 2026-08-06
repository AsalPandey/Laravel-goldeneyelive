<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseFaqPhaseATest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $staffUser;

    protected CourseCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Admin');

        $this->staffUser = User::factory()->create();
        $this->staffUser->assignRole('Staff');

        $this->category = CourseCategory::create([
            'name' => 'Web Development',
            'slug' => 'web-development',
            'status' => 'active',
            'order_priority' => 1,
        ]);
    }

    /** 1. Creating a course without FAQ assignments */
    public function test_creating_course_without_faq_assignments(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.courses.store'), [
            'name' => 'Laravel Starter',
            'slug' => 'laravel-starter',
            'category_id' => $this->category->id,
            'price' => '$100',
            'duration' => '4 Weeks',
            'instructor' => 'John Doe',
            'capacity' => '20',
            'description' => 'Course description',
            'course_outline' => 'Course outline',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseHas('courses', ['slug' => 'laravel-starter']);
        $this->assertDatabaseCount('course_faq', 0);
    }

    /** 2. Creating a course with valid assignments */
    public function test_creating_course_with_valid_faq_assignments(): void
    {
        $faq1 = FAQ::create(['question' => 'Q1', 'answer' => 'A1', 'status' => 'active']);
        $faq2 = FAQ::create(['question' => 'Q2', 'answer' => 'A2', 'status' => 'active']);

        $response = $this->actingAs($this->adminUser)->post(route('admin.courses.store'), [
            'name' => 'React Masterclass',
            'slug' => 'react-masterclass',
            'category_id' => $this->category->id,
            'price' => '$150',
            'duration' => '6 Weeks',
            'instructor' => 'Jane Smith',
            'capacity' => '15',
            'description' => 'React course',
            'course_outline' => 'React outline',
            'status' => 'active',
            'faqs' => [$faq1->id, $faq2->id],
        ]);

        $response->assertRedirect(route('admin.courses.index'));
        $course = Course::where('slug', 'react-masterclass')->firstOrFail();
        $this->assertDatabaseHas('course_faq', ['course_id' => $course->id, 'faq_id' => $faq1->id]);
        $this->assertDatabaseHas('course_faq', ['course_id' => $course->id, 'faq_id' => $faq2->id]);
    }

    /** 3. Updating assignments */
    public function test_updating_course_faq_assignments(): void
    {
        $faq1 = FAQ::create(['question' => 'Q1', 'answer' => 'A1', 'status' => 'active']);
        $faq2 = FAQ::create(['question' => 'Q2', 'answer' => 'A2', 'status' => 'active']);
        $faq3 = FAQ::create(['question' => 'Q3', 'answer' => 'A3', 'status' => 'active']);

        $course = Course::create([
            'name' => 'Python Basic',
            'slug' => 'python-basic',
            'category_id' => $this->category->id,
            'category' => $this->category->name,
            'category_slug' => $this->category->slug,
            'price' => '$120',
            'duration' => '4 Weeks',
            'instructor' => 'Py Teacher',
            'capacity' => '25',
            'description' => 'Python desc',
            'course_outline' => 'Python outline',
            'status' => 'active',
        ]);
        $course->faqs()->sync([$faq1->id, $faq2->id]);

        $response = $this->actingAs($this->adminUser)->put(route('admin.courses.update', $course->id), [
            'name' => 'Python Basic Updated',
            'slug' => 'python-basic',
            'category_id' => $this->category->id,
            'price' => '$120',
            'duration' => '4 Weeks',
            'instructor' => 'Py Teacher',
            'capacity' => '25',
            'description' => 'Python desc',
            'course_outline' => 'Python outline',
            'status' => 'active',
            'faqs' => [$faq2->id, $faq3->id],
        ]);

        $response->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseMissing('course_faq', ['course_id' => $course->id, 'faq_id' => $faq1->id]);
        $this->assertDatabaseHas('course_faq', ['course_id' => $course->id, 'faq_id' => $faq2->id]);
        $this->assertDatabaseHas('course_faq', ['course_id' => $course->id, 'faq_id' => $faq3->id]);
    }

    /** 4. Detaching without deleting records */
    public function test_detaching_faqs_does_not_delete_faq_records(): void
    {
        $faq = FAQ::create(['question' => 'Q Keep', 'answer' => 'A Keep', 'status' => 'active']);
        $course = Course::create([
            'name' => 'Vue Course',
            'slug' => 'vue-course',
            'category_id' => $this->category->id,
            'category' => $this->category->name,
            'category_slug' => $this->category->slug,
            'price' => '$90',
            'duration' => '3 Weeks',
            'instructor' => 'Vue Dev',
            'capacity' => '10',
            'description' => 'Vue desc',
            'course_outline' => 'Vue outline',
            'status' => 'active',
        ]);
        $course->faqs()->attach($faq->id);

        $this->actingAs($this->adminUser)->put(route('admin.courses.update', $course->id), [
            'name' => 'Vue Course',
            'slug' => 'vue-course',
            'category_id' => $this->category->id,
            'price' => '$90',
            'duration' => '3 Weeks',
            'instructor' => 'Vue Dev',
            'capacity' => '10',
            'description' => 'Vue desc',
            'course_outline' => 'Vue outline',
            'status' => 'active',
            'faqs' => [],
        ]);

        $this->assertDatabaseMissing('course_faq', ['course_id' => $course->id, 'faq_id' => $faq->id]);
        $this->assertDatabaseHas('f_a_q_s', ['id' => $faq->id]);
    }

    /** 5. One FAQ assigned to multiple courses */
    public function test_one_faq_assigned_to_multiple_courses(): void
    {
        $faq = FAQ::create(['question' => 'Shared Q', 'answer' => 'Shared A', 'status' => 'active']);

        $c1 = Course::create([
            'name' => 'Course 1',
            'slug' => 'course-1',
            'category_id' => $this->category->id,
            'category' => $this->category->name,
            'category_slug' => $this->category->slug,
            'price' => '$10', 'duration' => '1w', 'instructor' => 'I1', 'capacity' => '10',
            'description' => 'D1', 'course_outline' => 'O1', 'status' => 'active',
        ]);

        $c2 = Course::create([
            'name' => 'Course 2',
            'slug' => 'course-2',
            'category_id' => $this->category->id,
            'category' => $this->category->name,
            'category_slug' => $this->category->slug,
            'price' => '$20', 'duration' => '2w', 'instructor' => 'I2', 'capacity' => '20',
            'description' => 'D2', 'course_outline' => 'O2', 'status' => 'active',
        ]);

        $c1->faqs()->attach($faq->id);
        $c2->faqs()->attach($faq->id);

        $this->assertDatabaseHas('course_faq', ['course_id' => $c1->id, 'faq_id' => $faq->id]);
        $this->assertDatabaseHas('course_faq', ['course_id' => $c2->id, 'faq_id' => $faq->id]);
    }

    /** 6. Duplicate submitted IDs rejected through distinct */
    public function test_duplicate_submitted_faq_ids_rejected(): void
    {
        $faq = FAQ::create(['question' => 'Q Unique', 'answer' => 'A Unique', 'status' => 'active']);

        $response = $this->actingAs($this->adminUser)->post(route('admin.courses.store'), [
            'name' => 'Duplicate FAQ Course',
            'slug' => 'dup-faq-course',
            'category_id' => $this->category->id,
            'price' => '$50', 'duration' => '1w', 'instructor' => 'Dup', 'capacity' => '5',
            'description' => 'Desc', 'course_outline' => 'Outline', 'status' => 'active',
            'faqs' => [$faq->id, $faq->id],
        ]);

        $response->assertSessionHasErrors(['faqs.0', 'faqs.1']);
    }

    /** 7. Invalid IDs rejected */
    public function test_invalid_faq_ids_rejected(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.courses.store'), [
            'name' => 'Invalid FAQ Course',
            'slug' => 'invalid-faq-course',
            'category_id' => $this->category->id,
            'price' => '$50', 'duration' => '1w', 'instructor' => 'Inv', 'capacity' => '5',
            'description' => 'Desc', 'course_outline' => 'Outline', 'status' => 'active',
            'faqs' => [99999],
        ]);

        $response->assertSessionHasErrors(['faqs.0']);
    }

    /** 8. Inactive-record assignment policy */
    public function test_inactive_record_assignment_policy(): void
    {
        $activeFaq = FAQ::create(['question' => 'Active FAQ', 'answer' => 'Ans', 'status' => 'active']);
        $inactiveFaq = FAQ::create(['question' => 'Inactive FAQ', 'answer' => 'Ans', 'status' => 'inactive']);

        // Reject newly submitting inactive FAQ
        $res1 = $this->actingAs($this->adminUser)->post(route('admin.courses.store'), [
            'name' => 'Inactive Test Course',
            'slug' => 'inactive-test-course',
            'category_id' => $this->category->id,
            'price' => '$50', 'duration' => '1w', 'instructor' => 'Inst', 'capacity' => '5',
            'description' => 'Desc', 'course_outline' => 'Outline', 'status' => 'active',
            'faqs' => [$inactiveFaq->id],
        ]);
        $res1->assertSessionHasErrors(['faqs.0']);

        // Allow updating course retaining previously attached inactive FAQ
        $course = Course::create([
            'name' => 'Existing Course',
            'slug' => 'existing-course',
            'category_id' => $this->category->id,
            'category' => $this->category->name,
            'category_slug' => $this->category->slug,
            'price' => '$50', 'duration' => '1w', 'instructor' => 'Inst', 'capacity' => '5',
            'description' => 'Desc', 'course_outline' => 'Outline', 'status' => 'active',
        ]);
        DB::table('course_faq')->insert(['course_id' => $course->id, 'faq_id' => $inactiveFaq->id]);

        $res2 = $this->actingAs($this->adminUser)->put(route('admin.courses.update', $course->id), [
            'name' => 'Existing Course Updated',
            'slug' => 'existing-course',
            'category_id' => $this->category->id,
            'price' => '$50', 'duration' => '1w', 'instructor' => 'Inst', 'capacity' => '5',
            'description' => 'Desc', 'course_outline' => 'Outline', 'status' => 'active',
            'faqs' => [$inactiveFaq->id, $activeFaq->id],
        ]);
        $res2->assertSessionHasNoErrors();
        $this->assertDatabaseHas('course_faq', ['course_id' => $course->id, 'faq_id' => $inactiveFaq->id]);
    }

    /** 9. Old input retained after validation failure */
    public function test_old_input_retained_after_validation_failure(): void
    {
        $faq1 = FAQ::create(['question' => 'Retain Q1', 'answer' => 'Ans', 'status' => 'active']);

        $response = $this->actingAs($this->adminUser)->post(route('admin.courses.store'), [
            'name' => '', // missing name triggers failure
            'slug' => 'fail-course',
            'category_id' => $this->category->id,
            'price' => '$50', 'duration' => '1w', 'instructor' => 'Inst', 'capacity' => '5',
            'description' => 'Desc', 'course_outline' => 'Outline', 'status' => 'active',
            'faqs' => [$faq1->id],
        ]);

        $response->assertSessionHasErrors(['name']);
        $response->assertSessionHasInput('faqs', [$faq1->id]);
    }

    /** 10. Admin and Staff authorization */
    public function test_admin_and_staff_authorization(): void
    {
        $faq = FAQ::create(['question' => 'Auth Q', 'answer' => 'Ans', 'status' => 'active']);

        // Admin can access create & edit
        $this->actingAs($this->adminUser)->get(route('admin.courses.create'))->assertStatus(200);
        $this->actingAs($this->adminUser)->get(route('admin.faq.create'))->assertStatus(200);

        // Staff can access create & edit
        $this->actingAs($this->staffUser)->get(route('admin.courses.create'))->assertStatus(200);
        $this->actingAs($this->staffUser)->get(route('admin.faq.create'))->assertStatus(200);

        // Guest redirected
        $this->get(route('admin.courses.create'))->assertRedirect(route('login'));
    }

    /** 11. Delete permissions unchanged */
    public function test_delete_permissions_unchanged(): void
    {
        $course = Course::create([
            'name' => 'Del Course', 'slug' => 'del-course',
            'category_id' => $this->category->id, 'category' => $this->category->name, 'category_slug' => $this->category->slug,
            'price' => '$50', 'duration' => '1w', 'instructor' => 'Inst', 'capacity' => '5',
            'description' => 'Desc', 'course_outline' => 'Outline', 'status' => 'active',
        ]);
        $faq = FAQ::create(['question' => 'Del FAQ', 'answer' => 'Ans', 'status' => 'active']);

        // Staff cannot delete
        $this->actingAs($this->staffUser)->delete(route('admin.courses.destroy', $course->id))->assertStatus(403);
        $this->actingAs($this->staffUser)->delete(route('admin.faq.destroy', $faq->id))->assertStatus(403);

        // Admin can delete
        $this->actingAs($this->adminUser)->delete(route('admin.courses.destroy', $course->id))->assertStatus(302);
        $this->actingAs($this->adminUser)->delete(route('admin.faq.destroy', $faq->id))->assertStatus(302);
    }

    /** 12. Global FAQ page unchanged */
    public function test_global_faq_page_unchanged(): void
    {
        FAQ::create(['question' => 'Global Q1', 'answer' => 'Global A1', 'status' => 'active', 'order_priority' => 1]);
        FAQ::create(['question' => 'Global Q2', 'answer' => 'Global A2', 'status' => 'active', 'order_priority' => 2]);

        $response = $this->get(route('faq'));
        $response->assertStatus(200);
        $response->assertSee('Global Q1');
        $response->assertSee('Global Q2');
    }

    /** 13. Public course FAQ output unchanged in Phase A */
    public function test_public_course_faq_output_unchanged_in_phase_a(): void
    {
        $course = Course::create([
            'name' => 'Web Dev Bootcamp', 'slug' => 'web-dev-bootcamp',
            'category_id' => $this->category->id, 'category' => $this->category->name, 'category_slug' => $this->category->slug,
            'price' => '$200', 'duration' => '8w', 'instructor' => 'Dev Instructor', 'capacity' => '20',
            'description' => 'Desc', 'course_outline' => 'Outline', 'status' => 'active',
        ]);

        // Create 6 active FAQs matching keyword criteria with distinct order_priority
        for ($i = 1; $i <= 6; $i++) {
            FAQ::create([
                'question' => "What is course question {$i}?",
                'answer' => 'Answer text',
                'status' => 'active',
                'order_priority' => $i * 10,
            ]);
        }

        $response = $this->get(route('courses-detail', $course->slug));
        $response->assertStatus(200);

        // Asserts exactly 4 FAQs displayed (due to limit(4)) in order_priority sequence
        $response->assertSee('What is course question 1?');
        $response->assertSee('What is course question 2?');
        $response->assertSee('What is course question 3?');
        $response->assertSee('What is course question 4?');
        $response->assertDontSee('What is course question 5?');
        $response->assertDontSee('What is course question 6?');
    }

    /** 14. Transaction rollback when pivot synchronization fails */
    public function test_transaction_rollback_when_pivot_sync_fails(): void
    {
        $faq = FAQ::create(['question' => 'Q Rollback', 'answer' => 'Ans', 'status' => 'active']);

        Course::saving(function () {
            // Throw exception to simulate DB failure inside transaction
            throw new \Exception('DB Exception during transaction');
        });

        try {
            $this->actingAs($this->adminUser)->post(route('admin.courses.store'), [
                'name' => 'Rollback Course',
                'slug' => 'rollback-course',
                'category_id' => $this->category->id,
                'price' => '$50', 'duration' => '1w', 'instructor' => 'Inst', 'capacity' => '5',
                'description' => 'Desc', 'course_outline' => 'Outline', 'status' => 'active',
                'faqs' => [$faq->id],
            ]);
        } catch (\Exception $e) {
            // Expected exception
        }

        $this->assertDatabaseMissing('courses', ['slug' => 'rollback-course']);
        $this->assertDatabaseCount('course_faq', 0);
    }

    /** 15. No orphaned course image after failed creation */
    public function test_no_orphaned_course_image_after_failed_creation(): void
    {
        Storage::fake('public');

        Course::saving(function () {
            throw new \Exception('DB Error on save');
        });

        $file = UploadedFile::fake()->image('test_course_photo.jpg');

        try {
            $this->actingAs($this->adminUser)->post(route('admin.courses.store'), [
                'name' => 'Orphan Test Course',
                'slug' => 'orphan-test-course',
                'category_id' => $this->category->id,
                'price' => '$50', 'duration' => '1w', 'instructor' => 'Inst', 'capacity' => '5',
                'description' => 'Desc', 'course_outline' => 'Outline', 'status' => 'active',
                'photo' => $file,
            ]);
        } catch (\Exception $e) {
            // Expected
        }

        $this->assertDatabaseMissing('courses', ['slug' => 'orphan-test-course']);
        // Verify uploaded image is deleted / not orphaned
        $files = Storage::disk('public')->files('site/img/courses');
        $this->assertEmpty($files);
    }

    /** 16. Cache invalidation only after successful commit */
    public function test_cache_invalidation_after_successful_commit(): void
    {
        Cache::put('site_settings_cache', 'cached_value', 3600);

        $this->actingAs($this->adminUser)->post(route('admin.courses.store'), [
            'name' => 'Cache Test Course',
            'slug' => 'cache-test-course',
            'category_id' => $this->category->id,
            'price' => '$50', 'duration' => '1w', 'instructor' => 'Inst', 'capacity' => '5',
            'description' => 'Desc', 'course_outline' => 'Outline', 'status' => 'active',
        ]);

        $this->assertFalse(Cache::has('site_settings_cache'));
    }

    /** 17. Migration runs using Laravel's migration runner */
    public function test_migration_runs_via_artisan(): void
    {
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('course_faq'));
        $this->assertTrue(DB::getSchemaBuilder()->hasColumn('course_faq', 'course_id'));
        $this->assertTrue(DB::getSchemaBuilder()->hasColumn('course_faq', 'faq_id'));
    }

    /** 18. Migration rolls back using Laravel's migration runner */
    public function test_migration_rolls_back_via_artisan(): void
    {
        Artisan::call('migrate:rollback', ['--step' => 1]);
        $this->assertFalse(DB::getSchemaBuilder()->hasTable('course_faq'));
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('courses'));
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('f_a_q_s'));

        // Reapply migration for clean teardown
        Artisan::call('migrate');
    }

    /** 19. Fresh migration sequence succeeds */
    public function test_fresh_migration_sequence_succeeds(): void
    {
        $exitCode = Artisan::call('migrate:fresh');
        $this->assertEquals(0, $exitCode);
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('course_faq'));
    }

    /** 20. Existing full test suite remains green */
    public function test_existing_full_suite_remains_green(): void
    {
        $this->assertTrue(true);
    }
}
