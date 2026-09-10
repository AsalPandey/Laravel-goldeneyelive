<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Models\Course;
use App\Models\Testimonial;
use App\Traits\InteractsWithAssets;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TestimonialController extends Controller
{
    use InteractsWithAssets;

    public function index(Request $request)
    {
        $search = $request->input('search');

        $testimonials = Testimonial::query()
            ->when($search, function ($query, $search) {
                $query->where('student_name', 'like', "%{$search}%")
                    ->orWhere('course_name', 'like', "%{$search}%");
            })
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        $courses = Course::orderBy('name')->get(['id', 'name', 'status']);

        return view('admin.testimonials.create', compact('courses'));
    }

    public function store(TestimonialRequest $request)
    {
        $validated = $request->validated();

        $validated['course_name'] = isset($validated['course_id'])
            ? Course::findOrFail($validated['course_id'])->name
            : null;

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['photo'] = $this->resolveAssetUpdate($request, 'photo', 'site/img/testimonials', null, 'remove_photo');
        unset($validated['photo_path'], $validated['remove_photo']);

        Testimonial::create($validated);
        $this->clearSiteCache();

        Alert::success('Success', 'Testimonial added successfully.');

        return redirect()->route('admin.testimonials.index');
    }

    public function show(string $id)
    {
        if (! auth()->user()->hasRole('Admin')) {
            return redirect()->route('admin.testimonials.index');
        }

        return redirect()->route('admin.testimonials.edit', $id);
    }

    public function edit(string $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $courses = Course::orderBy('name')->get(['id', 'name', 'status']);

        return view('admin.testimonials.edit', compact('testimonial', 'courses'));
    }

    public function update(TestimonialRequest $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $oldPhoto = $testimonial->photo;
        $validated = $request->validated();

        if (isset($validated['course_id'])) {
            $validated['course_name'] = Course::findOrFail($validated['course_id'])->name;
        } else {
            unset($validated['course_name']);
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['photo'] = $this->resolveAssetUpdate($request, 'photo', 'site/img/testimonials', $testimonial->photo, 'remove_photo');
        unset($validated['photo_path'], $validated['remove_photo']);

        $testimonial->update($validated);
        $this->deleteReplacedAsset($oldPhoto, $testimonial->photo);
        $this->clearSiteCache();

        Alert::success('Success', 'Testimonial updated successfully.');

        return redirect()->route('admin.testimonials.index');
    }

    /**
     * Quick Toggle for Testimonial Status
     */
    public function toggleStatus($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $newStatus = $testimonial->status === 'active' ? 'inactive' : 'active';
        $testimonial->update(['status' => $newStatus]);
        $this->clearSiteCache();

        Alert::success('Success', "Testimonial marked as {$newStatus}.");

        return back();
    }

    /**
     * Quick Toggle for Featured Status
     */
    public function toggleFeatured($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update(['is_featured' => ! $testimonial->is_featured]);
        $this->clearSiteCache();

        $message = $testimonial->is_featured ? 'Testimonial is now featured on homepage.' : 'Testimonial removed from featured list.';
        Alert::success('Success', $message);

        return back();
    }

    public function destroy(string $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        $path = $testimonial->photo;
        $testimonial->delete();
        $this->secureAssetDeletion($path);
        $this->clearSiteCache();

        Alert::success('Success', 'Testimonial permanently deleted.');

        return back();
    }
}
