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
            'photo' => 'site/img/carousel-1.jpg', // This is a protected asset
            'status' => 'active',
            'rating_star' => '5',
            'rating_count' => '0',
        ]);

        // Mock the file existence
        $path = public_path('site/img/carousel-1.jpg');
        if (! File::isDirectory(dirname($path))) {
            File::makeDirectory(dirname($path), 0755, true);
        }
        File::put($path, 'dummy content');

        $this->actingAs($admin)
            ->delete(route('admin.courses.destroy', $course->id));

        $this->assertTrue(File::exists($path), 'Default asset was deleted!');

        // Clean up
        File::delete($path);
    }

    public function test_custom_assets_are_deleted_when_course_is_deleted()
    {
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $customPath = 'site/img/courses/custom_test_image.jpg';
        $fullPath = public_path($customPath);

        if (! File::isDirectory(dirname($fullPath))) {
            File::makeDirectory(dirname($fullPath), 0755, true);
        }
        File::put($fullPath, 'dummy content');

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
    }

    public function test_custom_assets_are_deleted_when_course_category_is_deleted()
    {
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $customPath = 'site/img/custom_category_test_image.jpg';
        $fullPath = public_path($customPath);

        if (! File::isDirectory(dirname($fullPath))) {
            File::makeDirectory(dirname($fullPath), 0755, true);
        }
        File::put($fullPath, 'dummy content');

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

        File::delete($guardPath);
    }
}
