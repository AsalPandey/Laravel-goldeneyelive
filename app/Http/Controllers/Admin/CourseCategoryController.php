<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Traits\InteractsWithAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class CourseCategoryController extends Controller
{
    use InteractsWithAssets;

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $categories = CourseCategory::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->withCount(['courses as active_courses_count' => function ($query) {
                $query->where('status', 'active');
            }])
            ->withCount('courses as total_courses_count')
            ->orderBy('order_priority', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(CategoryRequest $request)
    {
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['slug']);

        $validated['image'] = $this->handleAssetUpload($request, 'image', 'site/img/categories', null);

        CourseCategory::create($validated);
        $this->clearSiteCache();

        Alert::success('Success', 'Category created successfully.');

        return redirect()->route('admin.categories.index');
    }

    public function show($id)
    {
        return redirect()->route('admin.categories.edit', $id);
    }

    public function edit($id)
    {
        $category = CourseCategory::findOrFail($id);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = CourseCategory::findOrFail($id);
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['slug']);

        $oldImage = $category->image;
        $validated['image'] = $this->handleAssetUpload($request, 'image', 'site/img/categories', $category->image);

        $category->update($validated);
        $category->refresh();
        $this->deleteReplacedAsset($oldImage, $category->image);

        // Sync course de-normalized fields
        Course::where('category_id', $id)->update([
            'category' => $category->name,
            'category_slug' => $category->slug,
        ]);

        $this->clearSiteCache();

        Alert::success('Success', 'Category updated successfully.');

        return redirect()->route('admin.categories.index');
    }

    public function toggleStatus($id)
    {
        $category = CourseCategory::findOrFail($id);
        $newStatus = $category->status === 'active' ? 'inactive' : 'active';
        $category->update(['status' => $newStatus]);
        $this->clearSiteCache();

        Alert::success('Success', "Category marked as {$newStatus}.");

        return back();
    }

    public function destroy($id)
    {
        $category = CourseCategory::findOrFail($id);

        if ($category->courses()->count() > 0) {
            Alert::error('Cannot Delete', 'This category contains active courses. Reassign or remove them first.');

            return back();
        }

        $path = $category->image;
        $category->delete();
        $this->secureAssetDeletion($path);

        $this->clearSiteCache();

        Alert::success('Success', 'Category removed successfully.');

        return back();
    }
}
