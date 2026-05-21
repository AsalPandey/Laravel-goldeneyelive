<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseRequest;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\JoinNowQuery;
use App\Traits\InteractsWithAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class CourseController extends Controller
{
    use InteractsWithAssets;

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $courses = Course::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('category_slug', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%")
                        ->orWhere('duration', 'like', "%{$search}%")
                        ->orWhere('instructor', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('is_featured')
            ->orderBy('display_order')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.courses.index', compact('courses', 'search'));
    }

    public function create()
    {
        $categories = CourseCategory::orderBy('name')->get();

        return view('admin.courses.create', compact('categories'));
    }

    public function store(CourseRequest $request)
    {
        $validated = $request->validated();

        $category = CourseCategory::findOrFail($validated['category_id']);
        $validated['category'] = $category->name;
        $validated['category_slug'] = $category->slug;

        $validated['slug'] = Str::slug($validated['slug']);

        $validated['rating_star'] = '0';
        $validated['rating_count'] = '0';
        $validated['is_featured'] = $request->has('is_featured');
        $validated['display_order'] = $validated['display_order'] ?? 100;

        $validated['photo'] = $this->handleAssetUpload($request, 'photo', 'site/img/courses', 'site/img/carousel-1.png');

        Course::create($validated);
        $this->clearSiteCache();

        Alert::success('Success', 'Course created successfully.');

        return redirect()->route('admin.courses.index');
    }

    public function show($id)
    {
        return redirect()->route('admin.courses.edit', $id);
    }

    public function edit($id)
    {
        $course = Course::with('courseCategory')->findOrFail($id);
        $categories = CourseCategory::orderBy('name')->get();

        return view('admin.courses.edit', compact('course', 'categories'));
    }

    public function update(CourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validated();

        $category = CourseCategory::findOrFail($validated['category_id']);
        $validated['category'] = $category->name;
        $validated['category_slug'] = $category->slug;

        $validated['slug'] = Str::slug($validated['slug']);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['display_order'] = $validated['display_order'] ?? 100;

        $validated['photo'] = $this->handleAssetUpload($request, 'photo', 'site/img/courses', $course->photo);

        $course->update($validated);

        // Synchronize Leads: Update existing enrollment records with new course details
        JoinNowQuery::where('course_id', $course->id)->update([
            'course' => $course->name,
            'course_slug' => $course->slug,
        ]);

        $this->clearSiteCache();

        Alert::success('Success', 'Course updated successfully.');

        return redirect()->route('admin.courses.index');
    }

    /**
     * Quick Toggle for Course Status
     */
    public function toggleStatus($id)
    {
        $course = Course::findOrFail($id);
        $newStatus = $course->status === 'active' ? 'inactive' : 'active';
        $course->update(['status' => $newStatus]);
        $this->clearSiteCache();

        Alert::success('Success', "Course marked as {$newStatus}.");

        return back();
    }

    /**
     * Quick Toggle for Featured Status
     */
    public function toggleFeatured($id)
    {
        $course = Course::findOrFail($id);
        $course->update(['is_featured' => ! $course->is_featured]);
        $this->clearSiteCache();

        $message = $course->is_featured ? 'Course is now featured on homepage.' : 'Course removed from featured list.';
        Alert::success('Success', $message);

        return back();
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $path = $course->photo;

        $course->delete();
        $this->secureAssetDeletion($path);
        $this->clearSiteCache();

        Alert::success('Success', 'Course retracted from curriculum.');

        return back();
    }
}
