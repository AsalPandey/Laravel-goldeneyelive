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
