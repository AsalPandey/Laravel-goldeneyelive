<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
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
        return view('admin.testimonials.create');
    }

    public function store(TestimonialRequest $request)
    {
        $validated = $request->validated();

        $validated['is_featured'] = $request->has('is_featured');
        $validated['photo'] = $this->handleAssetUpload($request, 'photo', 'site/img/testimonials', null);

        Testimonial::create($validated);
        $this->clearSiteCache();

        Alert::success('Success', 'Testimonial added successfully.');

        return redirect()->route('admin.testimonials.index');
    }

    public function show(string $id)
    {
        return redirect()->route('admin.testimonials.edit', $id);
    }

    public function edit(string $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(TestimonialRequest $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $oldPhoto = $testimonial->photo;
        $validated = $request->validated();

        $validated['is_featured'] = $request->has('is_featured');
        $validated['photo'] = $this->handleAssetUpload($request, 'photo', 'site/img/testimonials', $testimonial->photo);

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

        Alert::success('Success', 'Testimonial deleted successfully.');

        return back();
    }
}
