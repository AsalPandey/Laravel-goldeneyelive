<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseRequest;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\JoinNowQuery;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Traits\InteractsWithAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $teachers = Teacher::orderBy('name')->get(['id', 'name', 'status']);
        $faqs = FAQ::where('status', 'active')->orderBy('order_priority')->get();

        return view('admin.courses.create', compact('categories', 'teachers', 'faqs'));
    }

    public function store(CourseRequest $request)
    {
        $validated = $request->validated();
        $validated['status'] ??= 'inactive';
        $faqIds = $validated['faqs'] ?? [];
        unset($validated['faqs']);

        $category = CourseCategory::findOrFail($validated['category_id']);
        $validated['category'] = $category->name;
        $validated['category_slug'] = $category->slug;

        if (filled($validated['teacher_id'] ?? null)) {
            $validated['instructor'] = Teacher::findOrFail($validated['teacher_id'])->name;
        }

        $validated['slug'] = Str::slug($validated['slug']);

        $validated['rating_star'] = '0';
        $validated['rating_count'] = '0';
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['display_order'] = $validated['display_order'] ?? 100;

        $validated['photo'] = $this->handleAssetUpload(
            $request,
            'photo',
            'site/img/courses',
            'site/img/carousel-1.png',
        );
        unset($validated['photo_path']);

        $uploadedPhoto = $request->hasFile('photo') ? $validated['photo'] : null;

        try {
            DB::transaction(function () use ($validated, $faqIds) {
                $course = Course::create($validated);
                $course->faqs()->sync($faqIds);

                DB::afterCommit(fn () => $this->clearSiteCache());
            });
        } catch (\Throwable $e) {
            if ($uploadedPhoto && $uploadedPhoto !== 'site/img/carousel-1.png') {
                $this->secureAssetDeletion($uploadedPhoto);
            }
            throw $e;
        }

        Alert::success('Success', 'Course created successfully.');

        return redirect()->route('admin.courses.index');
    }

    public function show($id)
    {
        if (! auth()->user()->hasRole('Admin')) {
            return redirect()->route('admin.courses.preview', $id);
        }

        return redirect()->route('admin.courses.edit', $id);
    }

    public function edit($id)
    {
        $course = Course::with(['courseCategory', 'faqs'])->findOrFail($id);
        $categories = CourseCategory::orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get(['id', 'name', 'status']);

        $assignedFaqIds = $course->faqs->pluck('id')->toArray();
        $faqs = FAQ::where('status', 'active')
            ->orWhereIn('id', $assignedFaqIds)
            ->orderBy('order_priority')
            ->get();

        return view('admin.courses.edit', compact('course', 'categories', 'teachers', 'faqs'));
    }

    public function update(CourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        $validated = $request->validated();
        $faqIds = $validated['faqs'] ?? [];
        unset($validated['faqs']);

        $category = CourseCategory::findOrFail($validated['category_id']);
        $validated['category'] = $category->name;
        $validated['category_slug'] = $category->slug;

        if (filled($validated['teacher_id'] ?? null)) {
            $validated['instructor'] = Teacher::findOrFail($validated['teacher_id'])->name;
        }

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['display_order'] = $validated['display_order'] ?? 100;

        $oldCourseName = $course->name;
        $oldPhoto = $course->photo;
        $validated['photo'] = $this->handleAssetUpload($request, 'photo', 'site/img/courses', $oldPhoto);
        unset($validated['photo_path']);

        $photoChanged = $validated['photo'] !== $oldPhoto;
        $newPhotoUploaded = $request->hasFile('photo') && $photoChanged;

        try {
            DB::transaction(function () use ($course, $validated, $faqIds, $oldPhoto, $photoChanged, $oldCourseName) {
                $course->update($validated);
                $course->faqs()->sync($faqIds);

                JoinNowQuery::where('course_id', $course->id)->update([
                    'course' => $course->name,
                    'course_slug' => $course->slug,
                ]);

                if ($oldCourseName !== $course->name) {
                    Testimonial::where('course_id', $course->id)
                        ->orWhere(function ($query) use ($oldCourseName): void {
                            $query->whereNull('course_id')->where('course_name', $oldCourseName);
                        })
                        ->update([
                            'course_name' => $course->name,
                            'course_id' => $course->id,
                        ]);
                }

                DB::afterCommit(function () use ($oldPhoto, $photoChanged, $course) {
                    if ($photoChanged) {
                        $this->deleteReplacedAsset($oldPhoto, $course->photo);
                    }
                    $this->clearSiteCache();
                });
            });
        } catch (\Throwable $e) {
            if ($newPhotoUploaded && isset($validated['photo']) && $validated['photo'] !== $oldPhoto) {
                $this->secureAssetDeletion($validated['photo']);
            }
            throw $e;
        }

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

        Alert::success('Success', 'Course permanently deleted.');

        return back();
    }
}
