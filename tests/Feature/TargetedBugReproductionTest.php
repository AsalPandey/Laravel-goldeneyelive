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

class TargetedBugReproductionTest extends TestCase
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

    public function test_bug_1_blog_slug_punctuation_is_rejected_or_safely_fallback_and_blog_listing_returns_200(): void
    {
        // 1. Entering '!!!' as Blog slug triggers validation error
        $response = $this->actingAs($this->admin)->post(route('admin.blog.store'), [
            'title' => 'Valid Blog Title',
            'slug' => '!!!',
            'content' => '<p>Some valid content for the blog post.</p>',
            'status' => 'published',
            'author' => 'Author Name',
            'category' => 'Insights',
        ]);

        $response->assertSessionHasErrors(['slug']);
        $this->assertDatabaseMissing(BlogPost::class, ['title' => 'Valid Blog Title']);

        // 2. Creating blog with empty slug auto-generates slug from title
        $response2 = $this->actingAs($this->admin)->post(route('admin.blog.store'), [
            'title' => 'Auto Generated Blog Slug',
            'slug' => '',
            'content' => '<p>Some valid content.</p>',
            'status' => 'published',
            'author' => 'Author Name',
            'category' => 'Insights',
        ]);
        $response2->assertSessionHasNoErrors();
        $post = BlogPost::where('title', 'Auto Generated Blog Slug')->first();
        $this->assertNotNull($post);
        $this->assertSame('auto-generated-blog-slug', $post->slug);

        // 3. Public /blog listing returns 200 and displays the post
        cache()->flush();
        $publicResponse = $this->get(route('blog'));
        $publicResponse->assertOk();
        $publicResponse->assertSee('Auto Generated Blog Slug');
    }

    public function test_bug_2_category_blank_priority_saves_safely_with_default_zero(): void
    {
        // Create with blank priority
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Web Development',
            'slug' => 'web-development',
            'description' => 'Test description',
            'status' => 'active',
            'order_priority' => '', // blank string
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.categories.index'));

        $category = CourseCategory::where('slug', 'web-development')->first();
        $this->assertNotNull($category);
        $this->assertSame(0, $category->order_priority);

        // Update with blank priority
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.categories.update', $category->id), [
            'name' => 'Web Development Updated',
            'slug' => 'web-development',
            'description' => 'Test description',
            'status' => 'active',
            'order_priority' => '',
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $updateResponse->assertRedirect(route('admin.categories.index'));
        $this->assertSame(0, $category->fresh()->order_priority);
    }

    public function test_bug_3_and_4_notice_timeline_integration(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-05 10:00:00'));
        cache()->flush();

        // T0: current popup A active and visible
        $this->actingAs($this->admin)->post(route('admin.notices.store'), [
            'title' => 'Notice A Current Popup',
            'subtitle' => 'Live now',
            'link' => 'https://example.com/a',
            'status' => 'active',
            'display_type' => 'popup',
        ]);

        $noticeA = Notice::where('title', 'Notice A Current Popup')->first();
        $this->assertNotNull($noticeA);

        $resT0 = $this->get('/');
        $resT0->assertOk();
        $resT0->assertSee('Notice A Current Popup');

        // T1: create future popup B starting tomorrow (at 10:00 UTC) and expiring in 2 hours (12:00 UTC)
        $this->actingAs($this->admin)->post(route('admin.notices.store'), [
            'title' => 'Notice B Future Popup',
            'subtitle' => 'Starts tomorrow',
            'link' => 'https://example.com/b',
            'status' => 'active',
            'display_type' => 'popup',
            'starts_at' => '2026-09-06T15:45', // Staff input Nepal time (+5:45) -> 2026-09-06 10:00 UTC
            'expires_at' => '2026-09-06T17:45', // 12:00 UTC
        ]);

        $noticeB = Notice::where('title', 'Notice B Future Popup')->first();
        $this->assertNotNull($noticeB);

        // Expected: A remains active and visible today
        $this->assertSame('active', $noticeA->fresh()->status);
        $resToday = $this->get('/');
        $resToday->assertSee('Notice A Current Popup');
        $resToday->assertDontSee('Notice B Future Popup');

        // At B start: advance to tomorrow 10:01 UTC
        Carbon::setTestNow(Carbon::parse('2026-09-06 10:01:00'));

        $resBStart = $this->get('/');
        // B becomes authoritative according to Notice contract
        $resBStart->assertSee('Notice B Future Popup');

        // After B expires: advance to tomorrow 12:01 UTC
        Carbon::setTestNow(Carbon::parse('2026-09-06 12:01:00'));

        $resBExpired = $this->get('/');
        // B disappears, A falls back
        $resBExpired->assertDontSee('Notice B Future Popup');
        $resBExpired->assertSee('Notice A Current Popup');

        // Repeat representative BAR scenario
        // T0: current bar X active and visible
        $this->actingAs($this->admin)->post(route('admin.notices.store'), [
            'title' => 'Notice X Current Bar',
            'subtitle' => 'Live bar now',
            'link' => 'https://example.com/x',
            'status' => 'active',
            'display_type' => 'bar',
        ]);

        $noticeX = Notice::where('title', 'Notice X Current Bar')->first();
        $this->assertNotNull($noticeX);
        $resBarT0 = $this->get('/');
        $resBarT0->assertSee('Notice X Current Bar');

        // T1: create future bar Y starting in 1 day and expiring in 2 hours
        $this->actingAs($this->admin)->post(route('admin.notices.store'), [
            'title' => 'Notice Y Future Bar',
            'subtitle' => 'Starts later',
            'link' => 'https://example.com/y',
            'status' => 'active',
            'display_type' => 'bar',
            'starts_at' => '2026-09-07T15:45', // 2026-09-07 10:00 UTC
            'expires_at' => '2026-09-07T17:45', // 12:00 UTC
        ]);

        $noticeY = Notice::where('title', 'Notice Y Future Bar')->first();
        $this->assertNotNull($noticeY);

        // Bar X remains active and visible
        $this->assertSame('active', $noticeX->fresh()->status);
        $resBarToday = $this->get('/');
        $resBarToday->assertSee('Notice X Current Bar');
        $resBarToday->assertDontSee('Notice Y Future Bar');

        // At Y start: 2026-09-07 10:01 UTC
        Carbon::setTestNow(Carbon::parse('2026-09-07 10:01:00'));
        $resBarYStart = $this->get('/');
        $resBarYStart->assertSee('Notice Y Future Bar');

        // After Y expires: 2026-09-07 12:01 UTC
        Carbon::setTestNow(Carbon::parse('2026-09-07 12:01:00'));
        $resBarYExpired = $this->get('/');
        $resBarYExpired->assertDontSee('Notice Y Future Bar');
        $resBarYExpired->assertSee('Notice X Current Bar');

        Carbon::setTestNow(null);
    }

    public function test_bug_5_faq_blank_priority_saves_safely_with_default_zero(): void
    {
        // Create with blank priority
        $response = $this->actingAs($this->admin)->post(route('admin.faq.store'), [
            'question' => 'What is the refund policy?',
            'answer' => 'Refunds are subject to terms.',
            'status' => 'active',
            'order_priority' => '', // blank string
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.faq.index'));

        $faq = FAQ::where('question', 'What is the refund policy?')->first();
        $this->assertNotNull($faq);
        $this->assertSame(0, $faq->order_priority);

        // Update with blank priority
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.faq.update', $faq->id), [
            'question' => 'What is the refund policy? (Updated)',
            'answer' => 'Updated refund details.',
            'status' => 'active',
            'order_priority' => '',
        ]);

        $updateResponse->assertSessionHasNoErrors();
        $updateResponse->assertRedirect(route('admin.faq.index'));
        $this->assertSame(0, $faq->fresh()->order_priority);
    }

    public function test_bug_6_notice_display_type_switch_enforces_one_active_notice_per_type(): void
    {
        // Notice A: active bar
        $noticeA = Notice::create([
            'title' => 'Notice A Active Bar',
            'status' => 'active',
            'display_type' => 'bar',
        ]);

        // Notice B: active popup
        $noticeB = Notice::create([
            'title' => 'Notice B Active Popup',
            'status' => 'active',
            'display_type' => 'popup',
        ]);

        // Edit Notice B: popup -> bar
        $response = $this->actingAs($this->admin)->put(route('admin.notices.update', $noticeB->id), [
            'title' => 'Notice B Switched to Bar',
            'status' => 'active',
            'display_type' => 'bar',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('inactive', $noticeA->fresh()->status);
        $this->assertSame('active', $noticeB->fresh()->status);

        // Reverse test: Notice C is active popup, Notice D is active bar
        $noticeC = Notice::create([
            'title' => 'Notice C Active Popup',
            'status' => 'active',
            'display_type' => 'popup',
        ]);
        $noticeD = Notice::create([
            'title' => 'Notice D Active Bar',
            'status' => 'active',
            'display_type' => 'bar',
        ]);

        // Edit Notice D: bar -> popup
        $reverseResponse = $this->actingAs($this->admin)->put(route('admin.notices.update', $noticeD->id), [
            'title' => 'Notice D Switched to Popup',
            'status' => 'active',
            'display_type' => 'popup',
        ]);

        $reverseResponse->assertSessionHasNoErrors();
        $this->assertSame('inactive', $noticeC->fresh()->status);
        $this->assertSame('active', $noticeD->fresh()->status);
    }

    public function test_bug_7a_general_testimonial_renders_on_homepage(): void
    {
        cache()->flush();
        $testimonial = Testimonial::create([
            'student_name' => 'General Student',
            'course_name' => null,
            'course_id' => null,
            'content' => 'Great general academy experience and supportive teachers.',
            'rating' => 5,
            'status' => 'active',
            'is_featured' => true,
        ]);

        $res = $this->get('/');
        $res->assertOk();
        $res->assertSee('General Student');
        $res->assertSee('Academy experience');
    }

    public function test_bug_7b_course_rename_synchronizes_testimonials(): void
    {
        $category = CourseCategory::create([
            'name' => 'Programming',
            'slug' => 'programming',
            'status' => 'active',
        ]);

        $course = Course::factory()->create([
            'name' => 'Initial Course Name',
            'slug' => 'initial-course-name',
            'category_id' => $category->id,
            'category' => $category->name,
            'category_slug' => $category->slug,
            'status' => 'active',
        ]);

        $testimonial = Testimonial::create([
            'student_name' => 'Jane Alumna',
            'course_id' => $course->id,
            'course_name' => $course->name,
            'content' => 'Exceptional instruction.',
            'rating' => 5,
            'status' => 'active',
        ]);

        // Rename course via admin
        $response = $this->actingAs($this->admin)->put(route('admin.courses.update', $course->id), [
            'name' => 'Renamed Course Name',
            'slug' => 'renamed-course-name',
            'category_id' => $category->id,
            'price' => $course->price,
            'duration' => $course->duration,
            'capacity' => $course->capacity,
            'description' => $course->description,
            'course_outline' => $course->course_outline,
            'status' => 'active',
            'instructor' => $course->instructor,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('Renamed Course Name', $testimonial->fresh()->course_name);

        // Also test switching testimonial to General academy testimonial
        $editTestimonialResponse = $this->actingAs($this->admin)->put(route('admin.testimonials.update', $testimonial->id), [
            'student_name' => 'Jane Alumna',
            'course_id' => '', // blank -> general
            'content' => 'Exceptional instruction.',
            'rating' => 5,
            'status' => 'active',
        ]);

        $editTestimonialResponse->assertSessionHasNoErrors();
        $fresh = $testimonial->fresh();
        $this->assertNull($fresh->course_id);
        $this->assertSame('Renamed Course Name', $fresh->course_name);
    }

    public function test_bug_8_teacher_rename_synchronizes_course_instructor(): void
    {
        $category = CourseCategory::create([
            'name' => 'Design',
            'slug' => 'design',
            'status' => 'active',
        ]);

        $teacher = Teacher::create([
            'name' => 'Test Teacher Original',
            'designation' => 'Lead Faculty',
            'bio' => 'Experienced educator',
            'status' => 'active',
        ]);

        $course = Course::factory()->create([
            'name' => 'Graphic Design Masterclass',
            'slug' => 'graphic-design-masterclass',
            'category_id' => $category->id,
            'teacher_id' => $teacher->id,
            'instructor' => $teacher->name,
            'status' => 'active',
        ]);

        // Course with manual instructor (no teacher_id)
        $manualCourse = Course::factory()->create([
            'name' => 'Guest Lecture Workshop',
            'slug' => 'guest-lecture-workshop',
            'category_id' => $category->id,
            'teacher_id' => null,
            'instructor' => 'Visiting Guest Lecturer',
            'status' => 'active',
        ]);

        $this->assertSame('Test Teacher Original', $course->fresh()->instructor);

        // Rename teacher via admin
        $response = $this->actingAs($this->admin)->put(route('admin.teachers.update', $teacher->id), [
            'name' => 'Test Teacher Renamed',
            'designation' => 'Senior Faculty',
            'status' => 'active',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('Test Teacher Renamed', $course->fresh()->instructor);
        // Manual instructor remains untouched
        $this->assertSame('Visiting Guest Lecturer', $manualCourse->fresh()->instructor);

        // Check homepage matches
        cache()->flush();
        $homeResponse = $this->get('/');
        $homeResponse->assertOk();
        $homeResponse->assertSee('Test Teacher Renamed');
        $homeResponse->assertSee('Graphic Design Masterclass');
    }

    public function test_bug_9_service_pillar_slug_with_punctuation_generates_safe_slug(): void
    {
        $response1 = $this->actingAs($this->admin)->post(route('admin.service-pillars.store'), [
            'title' => '!!!',
            'slug' => '',
            'summary' => 'Some summary',
            'status' => 'active',
        ]);

        $response1->assertSessionHasNoErrors();
        $pillar1 = ServicePillar::latest('id')->first();
        $this->assertSame('service-pillar', $pillar1->slug);

        // Second pillar with punctuation title generates service-pillar-2
        $response2 = $this->actingAs($this->admin)->post(route('admin.service-pillars.store'), [
            'title' => '###',
            'slug' => '',
            'summary' => 'Another summary',
            'status' => 'active',
        ]);

        $response2->assertSessionHasNoErrors();
        $pillar2 = ServicePillar::latest('id')->first();
        $this->assertSame('service-pillar-2', $pillar2->slug);
    }
}
