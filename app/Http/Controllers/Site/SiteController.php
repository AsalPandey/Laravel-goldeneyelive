<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\Teacher;
use App\Models\Testimonial;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class SiteController extends Controller
{
    /**
     * Homepage
     */
    public function index()
    {
        $viewData = cache()->remember('homepage_data', 3600, function () {
            return $this->getHomepageData();
        });

        // Defensive check against cache corruption
        if (! isset($viewData['courses']) || ! ($viewData['courses'] instanceof Collection)) {
            $viewData = $this->getHomepageData();
        }

        return view('site.index', $viewData);
    }

    /**
     * Get homepage data for caching or direct use.
     */
    protected function getHomepageData(): array
    {
        $categories = CourseCategory::where('status', 'active')->withCount(['courses' => function ($query) {
            $query->where('status', 'active');
        }])->orderBy('order_priority', 'asc')->get();

        return [
            'courses' => Course::publiclyVisible()
                ->salesOrdered()
                ->limit(6)
                ->get(),
            'teachers' => Teacher::where('status', 'active')->orderByDesc('is_featured')->latest()->limit(4)->get(),
            'testimonials' => Testimonial::where('status', 'active')->orderByDesc('is_featured')->latest()->limit(6)->get(),
            'posts' => BlogPost::where('status', 'published')->latest()->limit(3)->get(),
            'servicePillars' => ServicePillar::active()->ordered()->get(),
            'faqs' => FAQ::where('status', 'active')
                ->orderBy('order_priority', 'asc')
                ->latest()
                ->limit(4)
                ->get(),
            'notices' => Notice::where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                })
                ->latest('updated_at')
                ->limit(3)
                ->get(),
            'categories' => $categories,
        ];
    }

    public function about()
    {
        $teachers = cache()->remember('about_teachers', 3600, function () {
            return Teacher::where('status', 'active')->orderByDesc('is_featured')->latest()->get();
        });

        // Defensive check against cache corruption or incomplete classes
        if (! ($teachers instanceof Collection)) {
            $teachers = Teacher::where('status', 'active')->orderByDesc('is_featured')->latest()->get();
        }

        return view('site.about.about', [
            'teachers' => $teachers,
        ]);
    }

    public function aboutDetail()
    {
        return view('site.about.about-detail');
    }

    public function catalogue()
    {
        return view('site.catalogue.index', [
            'servicePillars' => ServicePillar::active()->ordered()->get(),
            'catalogueCategories' => CourseCategory::where('status', 'active')
                ->withCount(['courses' => function ($query) {
                    $query->where('status', 'active');
                }])
                ->orderBy('order_priority', 'asc')
                ->get(),
            'catalogueCourses' => Course::publiclyVisible()
                ->with('courseCategory')
                ->salesOrdered()
                ->get(),
        ]);
    }

    public function forStudents(): View
    {
        return $this->audienceLandingPage('students');
    }

    public function forParents(): View
    {
        return $this->audienceLandingPage('parents');
    }

    public function studyAbroadGuidance(): View
    {
        return $this->audienceLandingPage('study_abroad');
    }

    public function jobComputerSkills(): View
    {
        return $this->audienceLandingPage('job_computer_skills');
    }

    protected function audienceLandingPage(string $page): View
    {
        return view('site.audience.landing', [
            'landingPage' => $this->audienceLandingPages()[$page],
        ]);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function audienceLandingPages(): array
    {
        return [
            'students' => [
                'page_title' => 'Course and Class Information for Students in Pokhara - Golden Eye Academy',
                'meta_description' => 'Golden Eye Academy helps students in Pokhara compare IELTS/PTE, language, computer, office, web development, and IT classes before enrollment.',
                'source_page' => 'for_students',
                'audience_type' => 'student',
                'inquiry_intent' => 'course_selection_help',
                'selected_course' => 'undecided',
                'badge' => 'For students',
                'headline' => 'Not sure which course to choose after school or college?',
                'subheadline' => 'Golden Eye Academy helps students compare IELTS/PTE, language classes, computer skills, office classes, and web development before choosing a course.',
                'problem' => 'Many students choose courses randomly because friends joined, parents suggested something, or the course sounds popular. But the right course depends on your goal, current level, time, budget, and future plan.',
                'paths' => [
                    'IELTS / PTE if you are preparing for English exam goals',
                    'Japanese / Korean if you want language preparation',
                    'Computer & Office Skills if you want practical job-ready skills',
                    'Web Development if you want to start an IT skill path',
                ],
                'why' => 'We help you understand class options before enrollment, compare courses clearly, and choose a batch that matches your goal.',
                'proof' => [
                    'Trusted by students in Pokhara since 2008',
                    'Morning, day, and evening batches',
                    'IELTS/PTE, language, computer, office, and IT classes',
                ],
                'final_headline' => 'Tell us your goal. We will help you choose the right course before enrollment.',
                'secondary_cta' => 'courses',
            ],
            'parents' => [
                'page_title' => 'Course and Class Information for Parents in Pokhara - Golden Eye Academy',
                'meta_description' => 'Golden Eye Academy helps parents understand course fit, fees, timing, academic support, and realistic outcomes before enrollment.',
                'source_page' => 'for_parents',
                'audience_type' => 'parent',
                'inquiry_intent' => 'parent_course_guidance',
                'selected_course' => 'undecided',
                'badge' => 'For parents',
                'headline' => 'Need clear course information for your child?',
                'subheadline' => 'Golden Eye Academy helps parents understand course fit, fees, timing, academic support, and realistic outcomes before enrollment.',
                'problem' => 'Parents often want to know whether a course is useful, who teaches it, what it costs, how long it takes, and whether it fits the student\'s future plan. We explain these details clearly before enrollment.',
                'paths' => [
                    'Course fit',
                    'Fee and duration',
                    'Batch timing',
                    'Instructor support',
                    'Expected learning outcome',
                    'What support is included',
                    'What is not guaranteed',
                ],
                'why' => 'We focus on practical classes, transparent communication, and helping students choose a course based on their actual goal.',
                'proof' => [
                    'Srijana Chowk, Pokhara, Nepal',
                    'Serving Pokhara learners since 2008',
                    'Phone and WhatsApp follow-up available',
                ],
                'final_headline' => 'Share your child\'s current level and goal. Our team will suggest the most suitable course options.',
                'secondary_cta' => 'whatsapp',
            ],
            'study_abroad' => [
                'page_title' => 'IELTS, PTE and Language Preparation in Pokhara - Golden Eye Academy',
                'meta_description' => 'Explore IELTS, PTE, Japanese, Korean, English, computer, and practical classes for international study goals at Golden Eye Academy in Pokhara.',
                'source_page' => 'study_abroad_guidance',
                'audience_type' => 'study_abroad_applicant',
                'inquiry_intent' => 'study_abroad_course_guidance',
                'selected_course' => 'exam and language preparation',
                'badge' => 'IELTS, PTE and language preparation',
                'headline' => 'Preparing for international study goals?',
                'subheadline' => 'Explore IELTS, PTE, Japanese, Korean, English, and practical classes that support your academic preparation.',
                'problem' => 'Exam and language preparation can feel unclear at first. Students often need help understanding whether to start with IELTS, PTE, Japanese, Korean, English, or basic computer skills.',
                'paths' => [
                    'IELTS preparation for English test goals',
                    'PTE preparation for computer-based English testing',
                    'Japanese / Korean language courses for language pathway learners',
                    'Computer skills for academic and workplace readiness',
                ],
                'why' => 'We help students understand which preparation class fits their exam goal, timeline, current level, and preferred batch.',
                'proof' => [
                    'IELTS, PTE, Japanese, Korean, Computer & Web courses',
                    'Academic support before enrollment',
                    'Morning, day, and evening batches',
                ],
                'final_headline' => 'Tell us your exam goal, current education level, and timeline. We will explain suitable preparation classes.',
                'secondary_cta' => 'courses',
            ],
            'job_computer_skills' => [
                'page_title' => 'Computer and Job Skills Courses in Pokhara - Golden Eye Academy',
                'meta_description' => 'Golden Eye Academy helps learners build useful computer, office, web development, and IT skills for study, work, and career growth.',
                'source_page' => 'job_computer_skills',
                'audience_type' => 'job_skill_learner',
                'inquiry_intent' => 'computer_skill_guidance',
                'selected_course' => 'computer skills',
                'badge' => 'Computer and job skills',
                'headline' => 'Want practical computer or job-ready skills?',
                'subheadline' => 'Golden Eye Academy helps learners build useful computer, office, web development, and IT skills for study, work, and career growth.',
                'problem' => 'Many students and job seekers want practical skills but do not know where to start. Some need basic computer confidence, some need office skills, and some want to begin web development.',
                'paths' => [
                    'Basic Computer Skills for beginners',
                    'Office Package / Computer Skills for job readiness',
                    'Web Development for IT skill-building',
                    'Practical assignments and guided learning for confidence',
                ],
                'why' => 'We help you choose a course based on your current level and future goal, not random trends.',
                'proof' => [
                    'Computer, office, and web development tracks',
                    'Practical assignments and guided learning',
                    'Local classes in Pokhara since 2008',
                ],
                'final_headline' => 'Tell us your current skill level and goal. We will suggest the right starting point.',
                'secondary_cta' => 'courses',
            ],
        ];
    }

    /**
     * FAQ Page
     */
    public function faq()
    {
        $faqs = cache()->remember('site_faqs', 3600, function () {
            return FAQ::where('status', 'active')->orderBy('order_priority', 'asc')->latest()->get();
        });

        if (! ($faqs instanceof Collection)) {
            $faqs = FAQ::where('status', 'active')->orderBy('order_priority', 'asc')->latest()->get();
        }

        return view('site.faq.faq', [
            'faqs' => $faqs,
        ]);
    }

    public function privacyPolicy()
    {
        return view('site.others.privacyPolicy');
    }

    public function termsAndConditions()
    {
        return view('site.others.termsAndConditions');
    }
}
