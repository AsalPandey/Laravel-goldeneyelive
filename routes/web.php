<?php

use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BrandingController;
use App\Http\Controllers\Admin\CourseCategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\SEOController;
use App\Http\Controllers\Admin\ServicePillarController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Site\AnalyticsEventController;
use App\Http\Controllers\Site\BlogController as PublicBlogController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\CoursesController;
use App\Http\Controllers\Site\RobotsController;
use App\Http\Controllers\Site\SiteController;
use App\Http\Controllers\Site\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', RobotsController::class);
Route::redirect('/catelogue', '/catalogue', 301)->name('catalogue.legacy');
Route::redirect('/all-courses', '/courses-all', 301)->name('all-courses.redirect');
Route::post('/analytics/events', [AnalyticsEventController::class, 'store'])
    ->middleware('throttle:120,1')
    ->name('analytics.events.store');

Route::controller(SiteController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/about', 'about')->name('about');
    Route::redirect('/about-detail', '/about', 301)->name('about-detail');
    Route::get('/catalogue', 'catalogue')->name('catalogue');
    Route::get('/for-students', 'forStudents')->name('for-students');
    Route::get('/for-parents', 'forParents')->name('for-parents');
    Route::get('/study-abroad-guidance', 'studyAbroadGuidance')->name('study-abroad-guidance');
    Route::get('/job-computer-skills', 'jobComputerSkills')->name('job-computer-skills');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/privacy-policy', 'privacyPolicy')->name('privacy-policy');
    Route::get('/terms-and-conditions', 'termsAndConditions')->name('terms-and-conditions');
});

Route::controller(PublicBlogController::class)->group(function () {
    Route::get('/blog', 'index')->name('blog');
    Route::get('/blog/{slug}', 'show')->name('blog-detail');
});

Route::controller(ContactController::class)->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit')->name('contact-submit')->middleware('throttle:10,1');
    Route::post('/newsletter', 'newsletter')->name('newsletter')->middleware('throttle:5,1');
    Route::get('/join-now', 'joinNow')->name('join-now');
    Route::post('/join-now', 'joinNowSubmit')->name('join-now-submit')->middleware('throttle:10,1');
});

Route::controller(CoursesController::class)->group(function () {
    Route::get('/courses', 'courses')->name('courses');
    Route::get('/courses-all', 'coursesAll')->name('courses-all');
    Route::get('/courses-category/{slug}', 'courseCategory')->name('course-category');
    Route::get('/courses-category-legacy/{slug}', 'courseCatagory')->name('course-catagory');
    Route::get('/courses/{slug}', 'coursesDetail')->name('courses-detail');
});

/*
|--------------------------------------------------------------------------
| Protected Admin Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:Admin|Staff')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::redirect('/admin', '/admin/dashboard');
    });

    // Admin/Staff Only Sub-routes
    Route::middleware(['role:Admin|Staff', 'image.limit'])->group(function () {
        // CMS: Academy Management
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('courses/{course}/preview', [CoursesController::class, 'preview'])->name('courses.preview');
            Route::get('blog/{post}/preview', [PublicBlogController::class, 'preview'])->name('blog.preview');
            Route::resource('courses', CourseController::class)->except('destroy');
            Route::resource('categories', CourseCategoryController::class)->except('destroy');
            Route::patch('categories/{id}/toggle-status', [CourseCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
            Route::resource('service-pillars', ServicePillarController::class)->except('destroy');
            Route::patch('service-pillars/{service_pillar}/toggle-status', [ServicePillarController::class, 'toggleStatus'])->name('service-pillars.toggle-status');
            Route::patch('courses/{id}/toggle-status', [CourseController::class, 'toggleStatus'])->name('courses.toggle-status');
            Route::patch('courses/{id}/toggle-featured', [CourseController::class, 'toggleFeatured'])->name('courses.toggle-featured');
            Route::resource('blog', BlogController::class)->except('destroy');
            Route::patch('blog/{id}/toggle-status', [BlogController::class, 'toggleStatus'])->name('blog.toggle-status');
            Route::resource('faq', FAQController::class)->except('destroy');
            Route::patch('faq/{id}/toggle-status', [FAQController::class, 'toggleStatus'])->name('faq.toggle-status');
            Route::resource('teachers', TeacherController::class)->except('destroy');
            Route::patch('teachers/{id}/toggle-status', [TeacherController::class, 'toggleStatus'])->name('teachers.toggle-status');
            Route::patch('teachers/{id}/toggle-featured', [TeacherController::class, 'toggleFeatured'])->name('teachers.toggle-featured');
            Route::resource('testimonials', TestimonialController::class)->except('destroy');
            Route::patch('testimonials/{id}/toggle-status', [TestimonialController::class, 'toggleStatus'])->name('testimonials.toggle-status');
            Route::patch('testimonials/{id}/toggle-featured', [TestimonialController::class, 'toggleFeatured'])->name('testimonials.toggle-featured');
            Route::resource('notices', NoticeController::class)->except('destroy');
            Route::patch('notices/{id}/toggle', [NoticeController::class, 'toggleStatus'])->name('notices.toggle');
            Route::get('media', [MediaController::class, 'index'])->name('media.index');
            Route::post('media', [MediaController::class, 'store'])->name('media.store');

            // Website content and non-destructive media operations
            Route::get('branding', [BrandingController::class, 'index'])->name('branding.index');
            Route::post('branding/update', [BrandingController::class, 'update'])->name('branding.update');
            Route::post('branding/asset', [BrandingController::class, 'storeAsset'])->name('branding.asset.store');

            // Admin Only: Site Authority & Security Settings
            Route::middleware('role:Admin')->group(function () {
                Route::delete('courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
                Route::delete('categories/{category}', [CourseCategoryController::class, 'destroy'])->name('categories.destroy');
                Route::delete('service-pillars/{service_pillar}', [ServicePillarController::class, 'destroy'])->name('service-pillars.destroy');
                Route::delete('blog/{blog}', [BlogController::class, 'destroy'])->name('blog.destroy');
                Route::delete('faq/{faq}', [FAQController::class, 'destroy'])->name('faq.destroy');
                Route::delete('teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
                Route::delete('testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('testimonials.destroy');
                Route::delete('notices/{notice}', [NoticeController::class, 'destroy'])->name('notices.destroy');
                Route::delete('submissions/newsletter/{id}', [SubmissionController::class, 'destroyNewsletter'])->name('submissions.newsletter.destroy');

                // Destructive asset operations
                Route::delete('branding/asset', [BrandingController::class, 'destroyAsset'])->name('branding.asset.destroy');
                Route::post('branding/asset/purge', [BrandingController::class, 'purgeAssets'])->name('branding.asset.purge');

                // SEO & AI Authority Center
                Route::get('seo', [SEOController::class, 'index'])->name('seo.index');
                Route::post('seo/update', [SEOController::class, 'update'])->name('seo.update');
            });

            // Admin Submission Listings & Management
            Route::prefix('submissions')->name('submissions.')->group(function () {
                Route::get('contacts', [SubmissionController::class, 'contact_display'])->name('contact-display');
                Route::patch('contacts/{id}/status', [SubmissionController::class, 'updateContactStatus'])->name('contact.status.update');
                Route::delete('contacts/{id}', [SubmissionController::class, 'destroyContact'])->name('contact.destroy');
                Route::patch('contacts/{id}/restore', [SubmissionController::class, 'restoreContact'])->name('contact.restore');

                Route::get('enrollments', [SubmissionController::class, 'join_now_display'])->name('join_now-display');
                Route::patch('enrollments/{id}/status', [SubmissionController::class, 'updateJoinStatus'])->name('join_now.status.update');
                Route::delete('enrollments/{id}', [SubmissionController::class, 'destroyJoin'])->name('join_now.destroy');
                Route::patch('enrollments/{id}/restore', [SubmissionController::class, 'restoreJoin'])->name('join_now.restore');

                Route::get('newsletter', [SubmissionController::class, 'newsletter_display'])->name('newsletter-display');
                Route::post('bulk-delete', [SubmissionController::class, 'bulkDestroy'])->name('bulk-delete');
            });
        });
    });
});

require __DIR__.'/settings.php';
