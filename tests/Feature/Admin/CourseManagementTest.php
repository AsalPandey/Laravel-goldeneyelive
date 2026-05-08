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

    public function test_admin_can_open_course_edit_screen(): void
    {
        Role::create(['name' => 'Admin']);

        $admin = User::factory()->create();
        $admin->assignRole('Admin');

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
}
