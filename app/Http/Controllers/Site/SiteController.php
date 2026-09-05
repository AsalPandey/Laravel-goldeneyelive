<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\SiteSetting;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Support\CmsPublicContent;
use App\Support\PublicSiteCache;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class SiteController extends Controller
{
    /**
     * Homepage
     */
    public function index()
    {
        $cachedData = cache()->remember('homepage_data', PublicSiteCache::secondsUntilNoticeTransition(), function (): array {
            return $this->homepageDataForCache();
        });

        if (! $this->isPortableHomepageCache($cachedData)) {
            cache()->forget('homepage_data');
            $cachedData = $this->homepageDataForCache();
            cache()->put('homepage_data', $cachedData, PublicSiteCache::secondsUntilNoticeTransition());
        }

        $viewData = collect($cachedData)
            ->map(fn (array $items): Collection => collect($items)
                ->map(fn (array $item): object => json_decode(json_encode($item), false, 512, JSON_THROW_ON_ERROR)))
            ->all();

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
            'testimonials' => Testimonial::where('status', 'active')
                ->with('course:id,name')
                ->orderByDesc('is_featured')
                ->latest()
                ->limit(6)
                ->get(),
            'posts' => BlogPost::publiclyVisible()->latest('published_at')->limit(3)->get(),
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
                ->orderByDesc('is_urgent')
                ->orderByRaw('CASE WHEN starts_at IS NOT NULL THEN 1 ELSE 0 END DESC')
                ->latest('starts_at')
                ->latest('updated_at')
                ->limit(3)
                ->get(),
            'categories' => $categories,
        ];
    }

    /**
     * Cache only portable arrays, never serialized Eloquent model instances.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    protected function homepageDataForCache(): array
    {
        return collect($this->getHomepageData())
            ->map(fn (Collection $items): array => $items->toArray())
            ->all();
    }

    private function isPortableHomepageCache(mixed $cachedData): bool
    {
        $expectedKeys = ['courses', 'teachers', 'testimonials', 'posts', 'servicePillars', 'faqs', 'notices', 'categories'];

        if (! is_array($cachedData) || array_diff($expectedKeys, array_keys($cachedData)) !== []) {
            return false;
        }

        return collect($expectedKeys)->every(function (string $key) use ($cachedData): bool {
            return is_array($cachedData[$key])
                && collect($cachedData[$key])->every(fn (mixed $item): bool => is_array($item));
        });
    }

    public function about()
    {
        $teacherRows = cache()->remember('about_teachers', 3600, function (): array {
            return Teacher::where('status', 'active')->orderByDesc('is_featured')->latest()->get()->toArray();
        });

        if (! is_array($teacherRows)) {
            cache()->forget('about_teachers');
            $teacherRows = Teacher::where('status', 'active')->orderByDesc('is_featured')->latest()->get()->toArray();
        }

        $teachers = collect($teacherRows)->map(fn (array $teacher): object => (object) $teacher);

        return view('site.about.about', [
            'teachers' => $teachers,
        ]);
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
        $settings = SiteSetting::query()->pluck('value', 'key')->toArray();
        $landingPage = CmsPublicContent::audiencePages($settings)[$page] ?? null;

        abort_if($landingPage === null || ! $landingPage['is_active'], 404);

        return view('site.audience.landing', [
            'landingPage' => $landingPage,
        ]);
    }

    /**
     * FAQ Page
     */
    public function faq()
    {
        $faqRows = cache()->remember('site_faqs', 3600, function (): array {
            return FAQ::where('status', 'active')->orderBy('order_priority', 'asc')->latest()->get()->toArray();
        });

        if (! is_array($faqRows)) {
            cache()->forget('site_faqs');
            $faqRows = FAQ::where('status', 'active')->orderBy('order_priority', 'asc')->latest()->get()->toArray();
        }

        $faqs = collect($faqRows)->map(fn (array $faq): object => (object) $faq);

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
