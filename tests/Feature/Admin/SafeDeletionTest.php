<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SafeDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_assets_are_not_deleted_when_course_is_deleted()
    {
        // Setup Roles
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        // Use the real repository protected asset without mutating or deleting it
        $protectedAsset = 'site/img/carousel-1.png';
        $fullPath = public_path($protectedAsset);
        $this->assertFileExists($fullPath, 'Protected baseline asset must exist in repository.');
        $originalHash = hash_file('sha256', $fullPath);

        $course = Course::create([
            'name' => 'Test Course',
            'slug' => 'test-course',
            'category' => 'other classes',
            'category_slug' => 'other-classes',
            'price' => '100',
            'duration' => '1 month',
            'instructor' => 'John Doe',
            'capacity' => '20',
            'description' => 'Test',
            'course_outline' => 'Test',
            'photo' => $protectedAsset,
            'status' => 'active',
            'rating_star' => '5',
            'rating_count' => '0',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.courses.destroy', $course->id));

        $this->assertTrue(File::exists($fullPath), 'Default protected asset was deleted!');
        $this->assertSame($originalHash, hash_file('sha256', $fullPath), 'Default protected asset content was altered!');
    }

    public function test_custom_assets_are_deleted_when_course_is_deleted()
    {
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $customPath = 'site/img/courses/'.time().'_tst12.jpg';
        $fullPath = public_path($customPath);

        if (! File::isDirectory(dirname($fullPath))) {
            File::makeDirectory(dirname($fullPath), 0755, true);
        }
        File::put($fullPath, 'dummy content');

        try {
            $course = Course::create([
                'name' => 'Test Course 2',
                'slug' => 'test-course-2',
                'category' => 'other classes',
                'category_slug' => 'other-classes',
                'price' => '200',
                'duration' => '2 months',
                'instructor' => 'Jane Doe',
                'capacity' => '30',
                'description' => 'Test',
                'course_outline' => 'Test',
                'photo' => $customPath,
                'status' => 'active',
                'rating_star' => '5',
                'rating_count' => '0',
            ]);

            $response = $this->actingAs($admin)
                ->delete(route('admin.courses.destroy', $course->id));

            $response->assertStatus(302);

            $this->assertFalse(File::exists($fullPath), 'Custom asset was NOT deleted! '.($response->getSession()->get('error') ?? ''));
        } finally {
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
    }

    public function test_custom_assets_are_deleted_when_course_category_is_deleted()
    {
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $customPath = 'site/img/'.time().'_tst34.jpg';
        $fullPath = public_path($customPath);

        if (! File::isDirectory(dirname($fullPath))) {
            File::makeDirectory(dirname($fullPath), 0755, true);
        }
        File::put($fullPath, 'dummy content');

        try {
            $category = CourseCategory::create([
                'name' => 'Test Category',
                'slug' => 'test-category',
                'image' => $customPath,
                'status' => 'active',
                'order_priority' => 0,
            ]);

            $response = $this->actingAs($admin)
                ->delete(route('admin.categories.destroy', $category->id));

            $response->assertStatus(302);

            $this->assertFalse(File::exists($fullPath), 'Custom category asset was NOT deleted! '.($response->getSession()->get('error') ?? ''));
        } finally {
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
    }

    public function test_asset_deletion_rejects_paths_outside_public_site_images()
    {
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $guardPath = storage_path('app/asset_guard.txt');

        if (! File::isDirectory(dirname($guardPath))) {
            File::makeDirectory(dirname($guardPath), 0755, true);
        }

        File::put($guardPath, 'do not delete');

        try {
            $course = Course::create([
                'name' => 'Unsafe Asset Course',
                'slug' => 'unsafe-asset-course',
                'category' => 'other classes',
                'category_slug' => 'other-classes',
                'price' => '200',
                'duration' => '2 months',
                'instructor' => 'Jane Doe',
                'capacity' => '30',
                'description' => 'Test',
                'course_outline' => 'Test',
                'photo' => '../storage/app/asset_guard.txt',
                'status' => 'active',
                'rating_star' => '5',
                'rating_count' => '0',
            ]);

            $this->actingAs($admin)
                ->delete(route('admin.courses.destroy', $course->id))
                ->assertStatus(302);

            $this->assertTrue(File::exists($guardPath), 'Unsafe asset path deleted a file outside public/site/img.');
        } finally {
            if (File::exists($guardPath)) {
                File::delete($guardPath);
            }
        }
    }

    public function test_pre_existing_public_assets_survive_deletion_routines(): void
    {
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        // Check key protected and existing public assets
        $realAssets = [
            'site/img/carousel-1.png',
            'site/img/about.jpg',
            'site/img/logo.png',
            'site/img/goldeneye-academy-book-cover-20260909.png',
        ];

        $hashesBefore = [];
        foreach ($realAssets as $asset) {
            $fullPath = public_path($asset);
            if (File::exists($fullPath)) {
                $hashesBefore[$asset] = hash_file('sha256', $fullPath);
            }
        }

        $course = Course::create([
            'name' => 'Survival Check Course',
            'slug' => 'survival-check-course',
            'category' => 'other classes',
            'category_slug' => 'other-classes',
            'price' => '150',
            'duration' => '3 weeks',
            'instructor' => 'Staff Instructor',
            'capacity' => '15',
            'description' => 'Test',
            'course_outline' => 'Test',
            'photo' => 'site/img/carousel-1.png',
            'status' => 'active',
            'rating_star' => '5',
            'rating_count' => '0',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.courses.destroy', $course->id))
            ->assertStatus(302);

        foreach ($hashesBefore as $asset => $expectedHash) {
            $fullPath = public_path($asset);
            $this->assertTrue(File::exists($fullPath), "Pre-existing asset [{$asset}] was deleted!");
            $this->assertSame($expectedHash, hash_file('sha256', $fullPath), "Pre-existing asset [{$asset}] was modified!");
        }
    }
}
