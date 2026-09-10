<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TestAssetSurvivalRegressionTest extends TestCase
{
    use RefreshDatabase;

    private array $criticalAssets = [
        'site/img/carousel-1.png',
        'site/img/about.jpg',
        'site/img/logo.png',
        'site/img/goldeneye-academy-book-cover-20260909.png',
        'site/img/goldeneye-computer-advanced-back-cover-premium-20260909.png',
        'site/img/goldeneye-computer-advanced-back-cover-premium-b5-20260909.png',
        'site/img/goldeneye-computer-advanced-book-cover-20260909.png',
        'site/img/goldeneye-computer-advanced-book-cover-premium-20260909.png',
        'site/img/goldeneye-computer-basics-back-cover-premium-20260909.png',
        'site/img/goldeneye-computer-basics-book-cover-20260909.png',
        'site/img/goldeneye-computer-basics-book-cover-premium-20260909.png',
    ];

    public function test_all_critical_and_untracked_assets_survive_cms_destructions(): void
    {
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        // Snapshot existing assets and their hashes
        $assetHashes = [];
        foreach ($this->criticalAssets as $relativePath) {
            $fullPath = public_path($relativePath);
            if (File::exists($fullPath)) {
                $assetHashes[$relativePath] = hash_file('sha256', $fullPath);
            }
        }

        $this->assertNotEmpty($assetHashes, 'At least some critical assets should exist in the repository.');

        // 1. Create and destroy a course referencing a protected asset
        $course = Course::create([
            'name' => 'Protected Asset Course',
            'slug' => 'protected-asset-course',
            'category' => 'other classes',
            'category_slug' => 'other-classes',
            'price' => '100',
            'duration' => '1 month',
            'instructor' => 'John Doe',
            'capacity' => '20',
            'description' => 'Test',
            'course_outline' => 'Test',
            'photo' => 'site/img/carousel-1.png',
            'status' => 'active',
            'rating_star' => '5',
            'rating_count' => '0',
        ]);
        $this->actingAs($admin)->delete(route('admin.courses.destroy', $course->id))->assertRedirect();

        // 2. Create and destroy a category referencing a protected asset
        $category = CourseCategory::create([
            'name' => 'Protected Asset Category',
            'slug' => 'protected-asset-category',
            'image' => 'site/img/carousel-1.png',
            'status' => 'active',
            'order_priority' => 0,
        ]);
        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category->id))->assertRedirect();

        // 3. Create and destroy a teacher referencing a protected asset
        $teacher = Teacher::create([
            'name' => 'Protected Asset Teacher',
            'email' => 'teacher@example.com',
            'photo' => 'site/img/carousel-1.png',
            'status' => 'active',
            'bio' => 'Test bio',
        ]);
        $this->actingAs($admin)->delete(route('admin.teachers.destroy', $teacher->id))->assertRedirect();

        // 4. Attempt to delete a protected asset via branding asset endpoint
        $this->actingAs($admin)->delete(route('admin.branding.asset.destroy'), [
            'path' => 'site/img/carousel-1.png',
        ]);

        // Verify that every single asset still exists and its content hash is unchanged
        foreach ($assetHashes as $relativePath => $expectedHash) {
            $fullPath = public_path($relativePath);
            $this->assertTrue(File::exists($fullPath), "Critical asset [{$relativePath}] was deleted during CMS operations!");
            $this->assertSame($expectedHash, hash_file('sha256', $fullPath), "Critical asset [{$relativePath}] was modified during CMS operations!");
        }
    }
}
