<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CoursesController extends Controller
{
    /**
     * Legacy course landing page.
     */
    public function courses(): RedirectResponse
    {
        return redirect()->route('courses-all', [], 301);
    }

    /**
     * All courses with pagination
     */
    public function coursesAll(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $categorySlug = $request->query('category');

        $categories = CourseCategory::where('status', 'active')
            ->withCount(['courses' => function ($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('order_priority', 'asc')
            ->get();

        $featuredCourses = Course::publiclyVisible()
            ->salesOrdered()
            ->limit(3)
            ->get();

        $searchTokens = collect(preg_split('/[^\pL\pN+#]+/u', Str::lower($search)) ?: [])
            ->filter(fn (string $token): bool => mb_strlen($token) >= 2)
            ->unique()
            ->take(8)
            ->values();

        $courses = Course::publiclyVisible()
            ->with('courseCategory')
            ->when($searchTokens->isNotEmpty(), function ($query) use ($searchTokens) {
                $query->where(function ($query) use ($searchTokens) {
                    foreach ($searchTokens as $token) {
                        $likeToken = '%'.$token.'%';

                        $query->orWhere('name', 'like', $likeToken)
                            ->orWhere('slug', 'like', $likeToken)
                            ->orWhere('description', 'like', $likeToken)
                            ->orWhere('badge_text', 'like', $likeToken)
                            ->orWhere('category', 'like', $likeToken)
                            ->orWhere('category_slug', 'like', $likeToken)
                            ->orWhereHas('courseCategory', function ($categoryQuery) use ($likeToken) {
                                $categoryQuery->where('name', 'like', $likeToken)
                                    ->orWhere('slug', 'like', $likeToken);
                            });
                    }
                });
            })
            ->when($categorySlug, function ($query) use ($categorySlug) {
                $query->whereHas('courseCategory', function ($categoryQuery) use ($categorySlug) {
                    $categoryQuery->where('slug', $categorySlug)->where('status', 'active');
                });
            })
            ->salesOrdered()
            ->paginate(9)
            ->withQueryString();

        return view('site.courses.course-all', compact('courses', 'categories', 'featuredCourses', 'search', 'categorySlug'));
    }

    /**
     * Course details
     */
    public function coursesDetail(string $slug): View
    {
        $course = Course::publiclyVisible()
            ->with('courseCategory')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('site.courses.course-detail', $this->courseDetailViewData($course));
    }

    public function preview(Course $course): Response
    {
        $course->loadMissing('courseCategory');

        return response()
            ->view('site.courses.course-detail', [
                ...$this->courseDetailViewData($course),
                'isPreview' => true,
            ])
            ->header('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    /**
     * @return array<string, mixed>
     */
    private function courseDetailViewData(Course $course): array
    {
        $instructor = $course->teacher()
            ->where('status', 'active')
            ->first();

        $instructorCourses = $instructor
            ? Course::publiclyVisible()
                ->where('teacher_id', $instructor->id)
                ->orderBy('name')
                ->limit(4)
                ->pluck('name')
            : collect();

        $testimonial = Testimonial::where('status', 'active')
            ->where('course_id', $course->id)
            ->orderByDesc('is_featured')
            ->latest()
            ->first();

        $helpfulBlogs = $course->blogs()
            ->publiclyVisible()
            ->orderByDesc('blog_posts.published_at')
            ->orderByDesc('blog_posts.id')
            ->limit(3)
            ->get();

        $faqs = $course->faqs()
            ->where('f_a_q_s.status', 'active')
            ->orderBy('f_a_q_s.order_priority')
            ->orderBy('f_a_q_s.id')
            ->get();

        return compact('course', 'instructor', 'instructorCourses', 'testimonial', 'helpfulBlogs', 'faqs');
    }

    /**
     * Filter courses by category slug
     */
    public function courseCategory(string $slug): RedirectResponse
    {
        return redirect()->route('courses-all', ['category' => $slug], 301);
    }

    public function courseCatagory(string $slug): RedirectResponse
    {
        return redirect()->route('courses-all', ['category' => $slug], 301);
    }
}
