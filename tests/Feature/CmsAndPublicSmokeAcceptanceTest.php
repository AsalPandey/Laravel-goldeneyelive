<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsAndPublicSmokeAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    }

    /**
     * Phase 7: CMS Browser Acceptance Workflows
     */
    public function test_cms_workflow_blog_invalid_slug_validation(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.blog.store'), [
            'title' => 'Sample Blog Title',
            'slug' => '!!!',
            'content' => 'Blog content goes here.',
            'status' => 'published',
        ]);

        $response->assertSessionHasErrors(['slug']);
        $this->assertDatabaseMissing(BlogPost::class, ['title' => 'Sample Blog Title']);
    }

    public function test_cms_workflow_category_blank_priority(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Mobile Development',
            'slug' => 'mobile-development',
            'description' => 'Mobile dev courses',
            'status' => 'active',
            'order_priority' => '',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.categories.index'));

        $category = CourseCategory::where('slug', 'mobile-development')->first();
        $this->assertNotNull($category);
        $this->assertSame(0, $category->order_priority);
    }

    public function test_cms_workflow_faq_blank_display_order(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.faq.store'), [
            'question' => 'How can I enroll?',
            'answer' => 'Visit the front desk or call us.',
            'status' => 'active',
            'order_priority' => '',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.faq.index'));

        $faq = FAQ::where('question', 'How can I enroll?')->first();
        $this->assertNotNull($faq);
        $this->assertSame(0, $faq->order_priority);
    }

    public function test_cms_workflow_notice_scheduling_and_display_type_change(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-05 10:00:00'));
        cache()->flush();

        // 1. Create active popup notice
        $this->actingAs($this->admin)->post(route('admin.notices.store'), [
            'title' => 'First Popup Notice',
            'subtitle' => 'Live right now',
            'status' => 'active',
            'display_type' => 'popup',
        ]);

        $notice1 = Notice::where('title', 'First Popup Notice')->first();
        $this->assertNotNull($notice1);

        // 2. Schedule future popup notice
        $this->actingAs($this->admin)->post(route('admin.notices.store'), [
            'title' => 'Scheduled Holiday Notice',
            'subtitle' => 'Starts tomorrow',
            'status' => 'active',
            'display_type' => 'popup',
            'starts_at' => '2026-09-06T15:45', // tomorrow 10:00 UTC
        ]);

        $notice2 = Notice::where('title', 'Scheduled Holiday Notice')->first();
        $this->assertNotNull($notice2);

        // Notice 1 remains active today
        $this->assertSame('active', $notice1->fresh()->status);
        $this->get('/')->assertSee('First Popup Notice');

        // 3. Switch Notice 1 to bar
        $this->actingAs($this->admin)->put(route('admin.notices.update', $notice1->id), [
            'title' => 'First Popup Notice',
            'subtitle' => 'Live right now',
            'status' => 'active',
            'display_type' => 'bar',
        ]);

        $this->assertSame('bar', $notice1->fresh()->display_type);
        $this->assertSame('active', $notice1->fresh()->status);

        Carbon::setTestNow(null);
    }

    public function test_cms_workflow_general_testimonial(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.testimonials.store'), [
            'student_name' => 'Bikash Shrestha',
            'course_id' => '', // General academy testimonial
            'content' => 'Exceptional learning environment and very helpful mentors.',
            'rating' => 5,
            'status' => 'active',
            'is_featured' => true,
        ]);

        $response->assertSessionHasNoErrors();
        $testimonial = Testimonial::where('student_name', 'Bikash Shrestha')->first();
        $this->assertNotNull($testimonial);
        $this->assertNull($testimonial->course_id);
        $this->assertNull($testimonial->course_name);

        cache()->flush();
        $home = $this->get('/');
        $home->assertOk();
        $home->assertSee('Bikash Shrestha');
        $home->assertSee('Academy experience');
    }

    public function test_cms_workflow_course_rename_with_testimonial(): void
    {
        $category = CourseCategory::create([
            'name' => 'Language Studies',
            'slug' => 'language-studies',
            'status' => 'active',
        ]);

        $course = Course::factory()->create([
            'name' => 'Japanese N5 Foundation',
            'slug' => 'japanese-n5-foundation',
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
            'status' => 'active',
        ]);

        $testimonial = Testimonial::create([
            'student_name' => 'Suman Thapa',
            'course_id' => $course->id,
            'course_name' => $course->name,
            'content' => 'Great course on JLPT N5 grammar and vocabulary.',
            'rating' => 5,
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)->put(route('admin.courses.update', $course->id), [
            'name' => 'Japanese JLPT N5 Masterclass',
            'slug' => 'japanese-jlpt-n5-masterclass',
            'category_id' => $category->id,
            'price' => $course->price,
            'duration' => $course->duration,
            'capacity' => $course->capacity,
            'description' => $course->description,
            'course_outline' => $course->course_outline,
            'status' => 'active',
            'instructor' => $course->instructor,
        ]);

        $this->assertSame('Japanese JLPT N5 Masterclass', $testimonial->fresh()->course_name);
    }

    public function test_cms_workflow_teacher_rename(): void
    {
        $category = CourseCategory::create([
            'name' => 'Languages',
            'slug' => 'languages',
            'status' => 'active',
        ]);

        $teacher = Teacher::create([
            'name' => 'Sarita Sharma',
            'designation' => 'Senior Instructor',
            'status' => 'active',
        ]);

        $course = Course::factory()->create([
            'name' => 'Spoken English Intensive',
            'slug' => 'spoken-english-intensive',
            'category_id' => $category->id,
            'teacher_id' => $teacher->id,
            'instructor' => $teacher->name,
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)->put(route('admin.teachers.update', $teacher->id), [
            'name' => 'Sarita Sharma Subedi',
            'designation' => 'Department Head',
            'status' => 'active',
        ]);

        $this->assertSame('Sarita Sharma Subedi', $course->fresh()->instructor);
    }

    public function test_cms_workflow_service_pillar_slug(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.service-pillars.store'), [
            'title' => '!!!???',
            'slug' => '',
            'summary' => 'Service summary text.',
            'status' => 'active',
        ]);

        $response->assertSessionHasNoErrors();
        $pillar = ServicePillar::latest('id')->first();
        $this->assertSame('service-pillar', $pillar->slug);
    }

    /**
     * Phase 8: Critical Public Regression Smoke
     */
    public function test_public_pages_smoke_returns_200_without_errors(): void
    {
        $category = CourseCategory::create([
            'name' => 'General Computing',
            'slug' => 'general-computing',
            'status' => 'active',
        ]);

        $teacher = Teacher::create([
            'name' => 'Ramesh KC',
            'designation' => 'Faculty Lead',
            'bio' => 'Professional instructor.',
            'status' => 'active',
        ]);

        $course = Course::factory()->create([
            'name' => 'Basic Office Package',
            'slug' => 'basic-office-package',
            'category_id' => $category->id,
            'teacher_id' => $teacher->id,
            'instructor' => $teacher->name,
            'status' => 'active',
        ]);

        $blog = BlogPost::create([
            'title' => 'How to Choose Your Computer Course',
            'slug' => 'how-to-choose-your-computer-course',
            'content' => '<p>Practical advice for students.</p>',
            'status' => 'published',
            'published_at' => now(),
            'author' => 'Admin',
            'category' => 'Guides',
        ]);

        $faq = FAQ::create([
            'question' => 'Are certificates provided upon completion?',
            'answer' => 'Yes, verified academy certificates are provided.',
            'status' => 'active',
            'order_priority' => 1,
        ]);

        $notice = Notice::create([
            'title' => 'Admission Open for Winter Intake',
            'subtitle' => 'Limited seats available.',
            'link' => 'https://example.com/admissions',
            'status' => 'active',
            'display_type' => 'popup',
        ]);

        $testimonial = Testimonial::create([
            'student_name' => 'Prashant Rai',
            'course_id' => $course->id,
            'course_name' => $course->name,
            'content' => 'The course was clear, structured, and helpful.',
            'rating' => 5,
            'status' => 'active',
            'is_featured' => true,
        ]);

        cache()->flush();

        // 1. Homepage /
        $homeRes = $this->get('/');
        $homeRes->assertOk();
        $homeRes->assertSee('Admission Open for Winter Intake');
        $homeRes->assertSee('Prashant Rai');
        $homeRes->assertSee('Ramesh KC');

        // 2. /blog
        $blogListingRes = $this->get(route('blog'));
        $blogListingRes->assertOk();
        $blogListingRes->assertSee('How to Choose Your Computer Course');

        // 3. representative Blog detail
        $blogDetailRes = $this->get(route('blog-detail', $blog->slug));
        $blogDetailRes->assertOk();
        $blogDetailRes->assertSee('How to Choose Your Computer Course');
        $blogDetailRes->assertSee('Practical advice for students');

        // 4. representative Course detail
        $courseDetailRes = $this->get(route('courses-detail', $course->slug));
        $courseDetailRes->assertOk();
        $courseDetailRes->assertSee('Basic Office Package');
        $courseDetailRes->assertSee('Ramesh KC');

        // 5. /faq
        $faqRes = $this->get(route('faq'));
        $faqRes->assertOk();
        $faqRes->assertSee('Are certificates provided upon completion?');
    }
}
