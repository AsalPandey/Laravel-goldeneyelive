<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NoticeRequest;
use App\Models\Notice;
use App\Traits\InteractsWithAssets;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class NoticeController extends Controller
{
    use InteractsWithAssets;

    public function index(Request $request)
    {
        $search = $request->input('search');

        $notices = Notice::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.notices.index', compact('notices'));
    }

    public function create()
    {
        return view('admin.notices.create');
    }

    public function store(NoticeRequest $request)
    {
        $validated = $request->validated();

        if ($validated['status'] === 'active') {
            Notice::where('status', 'active')
                ->where('display_type', $validated['display_type'] ?? 'popup')
                ->update(['status' => 'inactive']);
        }

        $validated['image'] = $this->handleAssetUpload($request, 'image', 'site/img/notices');

        Notice::create($validated);
        $this->clearSiteCache();

        Alert::success('Success', 'Notice posted successfully.');

        return redirect()->route('admin.notices.index');
    }

    public function show($id)
    {
        return redirect()->route('admin.notices.edit', $id);
    }

    public function edit($id)
    {
        $notice = Notice::findOrFail($id);

        return view('admin.notices.edit', compact('notice'));
    }

    public function update(NoticeRequest $request, $id)
    {
        $notice = Notice::findOrFail($id);
        $oldImage = $notice->image;

        $validated = $request->validated();

        if (isset($validated['status']) && $validated['status'] === 'active' && $notice->status !== 'active') {
            Notice::where('status', 'active')
                ->where('display_type', $validated['display_type'] ?? $notice->display_type)
                ->update(['status' => 'inactive']);
        }

        $validated['image'] = $this->handleAssetUpload($request, 'image', 'site/img/notices', $notice->image);

        $notice->update($validated);
        $this->deleteReplacedAsset($oldImage, $notice->image);
        $this->clearSiteCache();

        Alert::success('Success', 'Notice updated successfully.');

        return redirect()->route('admin.notices.index');
    }

    /**
     * Quick Toggle for Notice Status
     */
    public function toggleStatus($id)
    {
        $notice = Notice::findOrFail($id);
        $newStatus = $notice->status === 'active' ? 'inactive' : 'active';

        if ($newStatus === 'active') {
            Notice::where('status', 'active')
                ->where('display_type', $notice->display_type)
                ->update(['status' => 'inactive']);
        }

        $notice->update(['status' => $newStatus]);
        $this->clearSiteCache();

        Alert::success('Success', "Notice marked as {$newStatus}.");

        return back();
    }

    public function destroy($id)
    {
        $notice = Notice::findOrFail($id);
        $image = $notice->image;

        $notice->delete();

        if ($image) {
            $this->secureAssetDeletion($image);
        }

        $this->clearSiteCache();

        Alert::success('Success', 'Notice removed successfully.');

        return back();
    }
}
