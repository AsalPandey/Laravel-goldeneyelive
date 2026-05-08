<?php

namespace Tests\Feature;

use App\Models\CourseCategory;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminCacheInvalidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_cms_changes_clear_public_homepage_cache(): void
    {
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $category = CourseCategory::create([
            'name' => 'Language',
            'slug' => 'language',
            'status' => 'active',
            'order_priority' => 0,
        ]);

        Cache::put('homepage_data', ['courses' => ['stale']], 3600);
        Cache::put('homepage_data_v2', ['courses' => ['stale']], 3600);
        Cache::put('homepage_categories', ['stale'], 3600);

        $response = $this->actingAs($admin)->post(route('admin.courses.store'), [
            'name' => 'IELTS Preparation',
            'category_id' => $category->id,
            'price' => '10000',
            'duration' => '8 weeks',
            'instructor' => 'GoldenEye Team',
            'capacity' => '20',
            'description' => 'IELTS course',
            'course_outline' => 'Speaking, writing, reading, listening',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.courses.index'));

        $this->assertFalse(Cache::has('homepage_data'));
        $this->assertFalse(Cache::has('homepage_data_v2'));
        $this->assertFalse(Cache::has('homepage_categories'));
    }

    public function test_faq_cms_changes_clear_public_faq_cache(): void
    {
        $this->seed(RoleSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        Cache::put('site_faqs', ['stale'], 3600);
        Cache::put('site_footer_faqs', ['stale'], 3600);

        $response = $this->actingAs($admin)->post(route('admin.faq.store'), [
            'question' => 'What courses do you offer?',
            'answer' => 'We offer IT, language, and test preparation courses.',
            'status' => 'active',
            'order_priority' => 1,
        ]);

        $response->assertRedirect(route('admin.faq.index'));

        $this->assertFalse(Cache::has('site_faqs'));
        $this->assertFalse(Cache::has('site_footer_faqs'));
    }
}
