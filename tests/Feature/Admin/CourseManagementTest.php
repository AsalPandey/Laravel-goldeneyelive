<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin']);
    }

    private function adminUser(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        return $admin;
    }

    public function test_admin_can_open_course_edit_screen(): void
    {
        $admin = $this->adminUser();

        $category = CourseCategory::factory()->create();
        $course = Course::factory()->create([
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.courses.edit', $course))
            ->assertOk()
            ->assertSee('Edit Course')
            ->assertSee($course->name);
    }

    public function test_admin_can_update_course_details_without_server_error(): void
    {
        $admin = $this->adminUser();
        $oldCategory = CourseCategory::factory()->create([
            'name' => 'Old Category',
            'slug' => 'old-category',
        ]);
        $newCategory = CourseCategory::factory()->create([
            'name' => 'Language and Test Prep',
            'slug' => 'language-and-test-prep',
        ]);
        $course = Course::factory()->create([
            'category_id' => $oldCategory->id,
            'category' => $oldCategory->name,
            'category_slug' => $oldCategory->slug,
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.courses.update', $course), [
                'name' => 'Updated IELTS Course',
                'slug' => 'updated-ielts-course',
                'badge_text' => 'Updated',
                'category_id' => $newCategory->id,
                'price' => 'Rs. 12,000',
                'duration' => '8 Weeks',
                'instructor' => 'GoldenEye Team',
                'capacity' => '20 Seats',
                'description' => '<p>Updated description.</p>',
                'course_outline' => '<ul><li>Speaking practice</li></ul>',
                'photo_path' => 'site/img/cat-1.jpg',
                'status' => 'active',
                'display_order' => 7,
            ]);

        $response->assertRedirect(route('admin.courses.index'));

        $this->assertDatabaseHas(Course::class, [
            'id' => $course->id,
            'name' => 'Updated IELTS Course',
            'category_id' => $newCategory->id,
            'category' => 'Language and Test Prep',
            'category_slug' => 'language-and-test-prep',
            'display_order' => 7,
        ]);
    }

    public function test_course_update_validation_returns_friendly_errors(): void
    {
        $admin = $this->adminUser();
        $course = Course::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.courses.edit', $course))
            ->put(route('admin.courses.update', $course), [
                'name' => '',
                'category_id' => 999999,
                'price' => '',
                'duration' => '',
                'instructor' => '',
                'capacity' => '',
                'description' => '',
                'course_outline' => '',
                'status' => 'active',
            ]);

        $response
            ->assertRedirect(route('admin.courses.edit', $course))
            ->assertSessionHasErrors(['name', 'category_id', 'price', 'duration', 'instructor', 'capacity', 'description', 'course_outline']);
    }

    public function test_course_save_requires_unique_slug_and_visible_management_controls(): void
    {
        $admin = $this->adminUser();
        $category = CourseCategory::factory()->create([
            'name' => 'Language and Test Prep',
            'slug' => 'language-and-test-prep',
        ]);

        $existingCourse = Course::factory()->create([
            'name' => 'Existing IELTS Course',
            'slug' => 'existing-ielts-course',
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.courses.index', ['search' => 'Existing IELTS']))
            ->assertOk()
            ->assertSee('Search')
            ->assertSee('Clear')
            ->assertSee('Edit '.$existingCourse->name, false)
            ->assertSee('Delete '.$existingCourse->name, false)
            ->assertSee($existingCourse->name)
            ->assertDontSee('Web Development 999');

        $response = $this->actingAs($admin)
            ->from(route('admin.courses.create'))
            ->post(route('admin.courses.store'), [
                'name' => 'New IELTS Course',
                'slug' => 'existing-ielts-course',
                'category_id' => $category->id,
                'price' => 'Rs. 12,000',
                'duration' => '8 Weeks',
                'instructor' => 'GoldenEye Team',
                'capacity' => '20 Seats',
                'description' => '<p>New course description.</p>',
                'course_outline' => '<ul><li>Practice plan</li></ul>',
                'photo_path' => 'site/img/cat-1.jpg',
                'status' => 'active',
                'display_order' => 10,
            ]);

        $response
            ->assertRedirect(route('admin.courses.create'))
            ->assertSessionHasErrors('slug');

        $this->assertDatabaseMissing(Course::class, [
            'name' => 'New IELTS Course',
        ]);
    }

    public function test_admin_can_open_category_edit_screen(): void
    {
        $admin = $this->adminUser();
        $category = CourseCategory::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.categories.edit', $category))
            ->assertOk()
            ->assertSee('Update')
            ->assertSee($category->name);
    }

    public function test_admin_can_update_category_details_without_server_error(): void
    {
        $admin = $this->adminUser();
        $category = CourseCategory::factory()->create();
        $course = Course::factory()->create([
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Study Abroad Preparation',
                'slug' => 'study-abroad-preparation',
                'description' => 'IELTS, PTE, and language preparation.',
                'image_path' => 'site/img/cat-1.jpg',
                'status' => 'active',
                'order_priority' => 3,
            ]);

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas(CourseCategory::class, [
            'id' => $category->id,
            'name' => 'Study Abroad Preparation',
            'slug' => 'study-abroad-preparation',
            'order_priority' => 3,
        ]);

        $this->assertDatabaseHas(Course::class, [
            'id' => $course->id,
            'category' => 'Study Abroad Preparation',
            'category_slug' => 'study-abroad-preparation',
        ]);
    }

    public function test_category_update_validation_returns_friendly_errors(): void
    {
        $admin = $this->adminUser();
        $category = CourseCategory::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.categories.edit', $category))
            ->put(route('admin.categories.update', $category), [
                'name' => '',
                'slug' => '',
                'status' => 'archived',
                'order_priority' => 'first',
            ]);

        $response
            ->assertRedirect(route('admin.categories.edit', $category))
            ->assertSessionHasErrors(['name', 'slug', 'status', 'order_priority']);
    }

    public function test_category_save_requires_unique_slug_and_visible_management_controls(): void
    {
        $admin = $this->adminUser();

        $existingCategory = CourseCategory::factory()->create([
            'name' => 'Computer Skills',
            'slug' => 'computer-skills',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.categories.index', ['search' => 'Computer']))
            ->assertOk()
            ->assertSee('Search')
            ->assertSee('Clear')
            ->assertSee('Edit '.$existingCategory->name, false)
            ->assertSee('Delete '.$existingCategory->name, false)
            ->assertSee('Computer Skills');

        $response = $this->actingAs($admin)
            ->from(route('admin.categories.create'))
            ->post(route('admin.categories.store'), [
                'name' => 'Computer Office Skills',
                'slug' => 'computer-skills',
                'description' => 'Office and computer classes.',
                'image_path' => 'site/img/cat-1.jpg',
                'status' => 'active',
                'order_priority' => 4,
            ]);

        $response
            ->assertRedirect(route('admin.categories.create'))
            ->assertSessionHasErrors('slug');

        $this->assertDatabaseMissing(CourseCategory::class, [
            'name' => 'Computer Office Skills',
        ]);
    }
}
