<?php

namespace Tests\Feature\Admin;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('Admin', 'web');
        Role::findOrCreate('Staff', 'web');
        $this->staff = User::factory()->create();
        $this->staff->assignRole('Staff');
    }

    public function test_all_existing_record_mutation_routes_require_admin_and_deny_crafted_staff_requests(): void
    {
        $this->actingAs($this->staff);
        foreach (Route::getRoutes() as $route) {
            if (! str_starts_with($route->getName() ?? '', 'admin.')) {
                continue;
            }
            $methods = array_diff($route->methods(), ['GET', 'HEAD', 'OPTIONS']);
            if (! $methods || str_ends_with($route->getName(), '.store') && ! str_contains($route->getName(), 'branding')) {
                continue;
            }
            $this->assertContains('role:Admin', $route->gatherMiddleware(), $route->getName());
            $uri = preg_replace('/\\{[^}]+\\}/', '1', $route->uri());
            $this->call(reset($methods), '/'.$uri)->assertForbidden();
        }
    }

    public function test_staff_can_view_and_add_every_content_module_but_cannot_edit_or_publish(): void
    {
        $category = CourseCategory::factory()->create();
        $payloads = [
            'courses' => [Course::class, ['name' => 'Staff course', 'slug' => 'staff-course', 'category_id' => $category->id, 'description' => 'Course content', 'price' => '100', 'duration' => '1 month', 'instructor' => 'Team', 'capacity' => '20', 'course_outline' => 'Learn computing']],
            'categories' => [CourseCategory::class, ['name' => 'Staff category', 'slug' => 'staff-category']],
            'service-pillars' => [ServicePillar::class, ['title' => 'Staff pillar']],
            'blog' => [BlogPost::class, ['title' => 'Staff post', 'content' => 'Post content']],
            'faq' => [FAQ::class, ['question' => 'Staff question?', 'answer' => 'Answer']],
            'teachers' => [Teacher::class, ['name' => 'Staff teacher', 'designation' => 'Teacher']],
            'testimonials' => [Testimonial::class, ['student_name' => 'Student', 'content' => 'Content', 'rating' => 5]],
            'notices' => [Notice::class, ['title' => 'Staff notice']],
        ];
        $existingNotice = Notice::factory()->create(['status' => 'active']);
        $this->actingAs($this->staff);
        foreach ($payloads as $module => [$model, $payload]) {
            $this->get(route('admin.'.$module.'.index'))->assertOk();
            $this->get(route('admin.'.$module.'.create'))->assertOk();
            $this->post(route('admin.'.$module.'.store'), [...$payload, 'status' => $module === 'blog' ? 'published' : 'active', 'is_featured' => 1])
                ->assertSessionHasNoErrors()->assertRedirect();
            $record = $model::latest('id')->firstOrFail();
            $this->assertSame($module === 'blog' ? 'draft' : 'inactive', $record->status);
            $this->assertFalse((bool) $record->is_featured);
            $this->get(route('admin.'.$module.'.edit', $record))->assertForbidden();
            $this->put(route('admin.'.$module.'.update', $record), $payload)->assertForbidden();
            $this->delete(route('admin.'.$module.'.destroy', $record))->assertForbidden();
        }
        $this->assertSame('active', $existingNotice->fresh()->status);
    }

    public function test_admin_can_create_update_and_delete_and_staff_sees_no_edit_controls(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $payload = ['question' => 'New FAQ?', 'answer' => 'New answer', 'status' => 'active'];
        $this->actingAs($admin)->post(route('admin.faq.store'), $payload)->assertSessionHasNoErrors()->assertRedirect();
        $faq = FAQ::latest('id')->firstOrFail();
        $this->put(route('admin.faq.update', $faq), [...$payload, 'answer' => 'Updated'])->assertRedirect();
        $this->assertSame('Updated', $faq->fresh()->answer);
        $this->actingAs($this->staff)->get(route('admin.faq.index'))->assertOk()
            ->assertDontSee('action="'.route('admin.faq.toggle-status', $faq).'"', false)
            ->assertDontSee('href="'.route('admin.faq.edit', $faq).'"', false);
        $this->actingAs($admin)->delete(route('admin.faq.destroy', $faq))->assertRedirect();
        $this->assertModelMissing($faq);
    }
}
