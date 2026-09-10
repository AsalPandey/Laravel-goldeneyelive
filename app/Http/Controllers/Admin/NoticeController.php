<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NoticeRequest;
use App\Models\Notice;
use App\Support\CmsDateTime;
use App\Traits\InteractsWithAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $validated['display_type'] ??= 'popup';
        $validated['starts_at'] = CmsDateTime::fromStaffInput($validated['starts_at'] ?? null);
        $validated['expires_at'] = CmsDateTime::fromStaffInput($validated['expires_at'] ?? null);

        $validated['image'] = $this->resolveAssetUpdate($request, 'image', 'site/img/notices', null, 'remove_image');
        unset($validated['image_path'], $validated['remove_image']);
        $uploadedImage = $request->hasFile('image') ? $validated['image'] : null;

        try {
            DB::transaction(function () use ($validated): void {
                $this->deactivateCurrentNoticeFor($validated);
                Notice::create($validated);

                DB::afterCommit(fn () => $this->clearSiteCache());
            });
        } catch (\Throwable $exception) {
            if ($uploadedImage) {
                $this->secureAssetDeletion($uploadedImage);
            }

            throw $exception;
        }

        Alert::success('Success', 'Notice posted successfully.');

        return redirect()->route('admin.notices.index');
    }

    public function show($id)
    {
        if (! auth()->user()->hasRole('Admin')) {
            return redirect()->route('admin.notices.index');
        }

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
        $validated['display_type'] = $validated['display_type'] ?? $notice->display_type ?? 'popup';
        $validated['starts_at'] = CmsDateTime::fromStaffInput(
            $validated['starts_at'] ?? null,
            $notice->starts_at,
        );
        $validated['expires_at'] = CmsDateTime::fromStaffInput(
            $validated['expires_at'] ?? null,
            $notice->expires_at,
        );

        $validated['image'] = $this->resolveAssetUpdate($request, 'image', 'site/img/notices', $notice->image, 'remove_image');
        unset($validated['image_path'], $validated['remove_image']);
        $uploadedImage = $request->hasFile('image') ? $validated['image'] : null;

        try {
            DB::transaction(function () use ($notice, $validated, $oldImage): void {
                $this->deactivateCurrentNoticeFor($validated, $notice->getKey());
                $notice->update($validated);

                DB::afterCommit(function () use ($oldImage, $notice): void {
                    $this->deleteReplacedAsset($oldImage, $notice->image);
                    $this->clearSiteCache();
                });
            });
        } catch (\Throwable $exception) {
            if ($uploadedImage && $uploadedImage !== $oldImage) {
                $this->secureAssetDeletion($uploadedImage);
            }

            throw $exception;
        }

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

        DB::transaction(function () use ($notice, $newStatus): void {
            $activation = [
                'status' => $newStatus,
                'display_type' => $notice->display_type,
                'starts_at' => $notice->starts_at,
                'expires_at' => $notice->expires_at,
            ];

            $this->deactivateCurrentNoticeFor($activation, $notice->getKey());
            $notice->update(['status' => $newStatus]);

            DB::afterCommit(fn () => $this->clearSiteCache());
        });

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

        Alert::success('Success', 'Notice permanently deleted.');

        return back();
    }

    /**
     * Deactivate only notices competing on the same surface right now.
     * Future scheduled notices must not displace the current live notice.
     *
     * @param  array{status?: string, display_type?: string, starts_at?: mixed, expires_at?: mixed}  $activation
     */
    private function deactivateCurrentNoticeFor(array $activation, int|string|null $exceptId = null): void
    {
        if (($activation['status'] ?? null) !== 'active') {
            return;
        }

        $startsAt = $activation['starts_at'] ?? null;
        $expiresAt = $activation['expires_at'] ?? null;

        if (($startsAt && $startsAt->isFuture()) || ($expiresAt && $expiresAt->isPast())) {
            return;
        }

        $displayType = $activation['display_type'] ?? 'popup';
        $competingDisplayTypes = $displayType === 'bar' ? ['bar'] : ['popup', 'standard'];

        Notice::query()
            ->where('status', 'active')
            ->whereIn('display_type', $competingDisplayTypes)
            ->when($exceptId !== null, fn ($query) => $query->whereKeyNot($exceptId))
            ->where(function ($query): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->update(['status' => 'inactive']);
    }
}
