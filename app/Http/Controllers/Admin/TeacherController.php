<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TeacherRequest;
use App\Models\Teacher;
use App\Traits\InteractsWithAssets;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TeacherController extends Controller
{
    use InteractsWithAssets;

    public function index(Request $request)
    {
        $search = $request->input('search');

        $teachers = Teacher::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            })
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(TeacherRequest $request)
    {
        $validated = $request->validated();

        $validated['is_featured'] = $request->has('is_featured');
        $validated['photo'] = $this->handleAssetUpload($request, 'photo', 'site/img/teachers', 'site/img/team-1.jpg');

        Teacher::create($validated);
        $this->clearSiteCache();

        Alert::success('Success', 'Teacher added successfully.');

        return redirect()->route('admin.teachers.index');
    }

    public function show(string $id)
    {
        return redirect()->route('admin.teachers.edit', $id);
    }

    public function edit(string $id)
    {
        $teacher = Teacher::findOrFail($id);

        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(TeacherRequest $request, $id)
    {
        $teacher = Teacher::findOrFail($id);
        $validated = $request->validated();

        $validated['is_featured'] = $request->has('is_featured');
        $oldPhoto = $teacher->photo;
        $validated['photo'] = $this->handleAssetUpload($request, 'photo', 'site/img/teachers', $teacher->photo);

        $teacher->update($validated);
        $this->deleteReplacedAsset($oldPhoto, $teacher->photo);
        $this->clearSiteCache();

        Alert::success('Success', 'Teacher updated successfully.');

        return redirect()->route('admin.teachers.index');
    }

    /**
     * Quick Toggle for Teacher Status
     */
    public function toggleStatus($id)
    {
        $teacher = Teacher::findOrFail($id);
        $newStatus = $teacher->status === 'active' ? 'inactive' : 'active';
        $teacher->update(['status' => $newStatus]);
        $this->clearSiteCache();

        Alert::success('Success', "Teacher marked as {$newStatus}.");

        return back();
    }

    /**
     * Quick Toggle for Featured Status
     */
    public function toggleFeatured($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->update(['is_featured' => ! $teacher->is_featured]);
        $this->clearSiteCache();

        $message = $teacher->is_featured ? 'Teacher is now featured on homepage.' : 'Teacher removed from featured list.';
        Alert::success('Success', $message);

        return back();
    }

    public function destroy(string $id)
    {
        $teacher = Teacher::findOrFail($id);

        $path = $teacher->photo;
        $teacher->delete();
        $this->secureAssetDeletion($path);
        $this->clearSiteCache();

        Alert::success('Success', 'Teacher deleted successfully.');

        return back();
    }
}
