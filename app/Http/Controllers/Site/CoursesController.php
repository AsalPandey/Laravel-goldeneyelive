<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
    public function coursesAll(Request $request)
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

        $courses = Course::publiclyVisible()
            ->with('courseCategory')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('badge_text', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
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
    public function coursesDetail($slug)
    {
        $course = Course::publiclyVisible()->where('slug', $slug)->firstOrFail();

        return view('site.courses.course-detail', compact('course'));
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
