<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogRequest;
use App\Models\BlogPost;
use App\Models\Course;
use App\Support\CmsDateTime;
use App\Traits\InteractsWithAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class BlogController extends Controller
{
    use InteractsWithAssets;

    public function index(Request $request)
    {
        $query = BlogPost::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
            });
        }

        $posts = $query->latest()->paginate(15)->withQueryString();

        return view('admin.blog.index', compact('posts'));
    }

    public function toggleStatus($id)
    {
        $post = BlogPost::findOrFail($id);
        $newStatus = $post->status === 'published' ? 'draft' : 'published';

        $post->update([
            'status' => $newStatus,
            'published_at' => $newStatus === 'published' ? ($post->published_at ?? now()) : $post->published_at,
        ]);
        $this->clearSiteCache();

        Alert::success('Success', "Article marked as {$newStatus}.");

        return back();
    }

    public function create()
    {
        $courses = Course::where('status', 'active')->orderBy('name')->get(['id', 'name', 'status']);
        $blogCategories = $this->blogCategories();

        return view('admin.blog.create', compact('courses', 'blogCategories'));
    }

    public function store(BlogRequest $request)
    {
        $validated = $request->validated();
        $courseIds = $validated['courses'] ?? [];
        unset($validated['courses'], $validated['courses_present']);

        $rawSlug = $request->filled('slug') && filled(Str::slug((string) $request->slug))
            ? (string) $request->slug
            : (string) $validated['title'];

        $validated['slug'] = (new BlogPost)->generateUniqueSlug($rawSlug);

        $validated['published_at'] = CmsDateTime::fromStaffInput($validated['published_at'] ?? null);

        if ($validated['status'] === 'published' && blank($validated['published_at'] ?? null)) {
            $validated['published_at'] = now();
        }

        $validated['image'] = $this->resolveAssetUpdate($request, 'image', 'site/img/blog', 'site/img/carousel-2.jpg', 'remove_image');
        unset($validated['image_path'], $validated['remove_image']);
        $uploadedImage = $request->hasFile('image') ? $validated['image'] : null;

        try {
            DB::transaction(function () use ($validated, $courseIds): void {
                $post = BlogPost::create($validated);
                $post->courses()->sync($courseIds);

                DB::afterCommit(fn () => $this->clearSiteCache());
            });
        } catch (\Throwable $exception) {
            if ($uploadedImage) {
                $this->secureAssetDeletion($uploadedImage);
            }

            throw $exception;
        }

        Alert::success('Success', 'Blog post created successfully.');

        return redirect()->route('admin.blog.index');
    }

    public function show($id)
    {
        if (! auth()->user()->hasRole('Admin')) {
            return redirect()->route('admin.blog.preview', $id);
        }

        return redirect()->route('admin.blog.edit', $id);
    }

    public function edit($id)
    {
        $post = BlogPost::with('courses')->findOrFail($id);
        $assignedCourseIds = $post->courses->pluck('id')->all();
        $courses = Course::where('status', 'active')
            ->orWhereIn('id', $assignedCourseIds)
            ->orderBy('name')
            ->get(['id', 'name', 'status']);
        $blogCategories = $this->blogCategories();

        return view('admin.blog.edit', compact('post', 'courses', 'blogCategories'));
    }

    public function update(BlogRequest $request, $id)
    {
        $post = BlogPost::findOrFail($id);
        $oldImage = $post->image;

        $validated = $request->validated();
        $courseIds = $validated['courses'] ?? [];
        unset($validated['courses'], $validated['courses_present']);

        $rawSlug = $request->filled('slug') && filled(Str::slug((string) $request->slug))
            ? (string) $request->slug
            : (string) $validated['title'];

        $validated['slug'] = $post->generateUniqueSlug($rawSlug, $id);

        if (filled($validated['published_at'] ?? null)) {
            $validated['published_at'] = CmsDateTime::fromStaffInput(
                $validated['published_at'],
                $post->published_at,
            );
        } else {
            $validated['published_at'] = $post->published_at;
        }

        if ($validated['status'] === 'published' && $validated['published_at'] === null) {
            $validated['published_at'] = now();
        }

        $validated['image'] = $this->resolveAssetUpdate($request, 'image', 'site/img/blog', $post->image, 'remove_image');
        unset($validated['image_path'], $validated['remove_image']);
        $newImage = $validated['image'];
        $newImageUploaded = $request->hasFile('image') && $newImage !== $oldImage;

        try {
            DB::transaction(function () use ($post, $validated, $courseIds, $oldImage, $newImage): void {
                $post->update($validated);
                $post->courses()->sync($courseIds);

                DB::afterCommit(function () use ($oldImage, $newImage): void {
                    $this->deleteReplacedAsset($oldImage, $newImage);
                    $this->clearSiteCache();
                });
            });
        } catch (\Throwable $exception) {
            if ($newImageUploaded) {
                $this->secureAssetDeletion($newImage);
            }

            throw $exception;
        }

        Alert::success('Success', 'Blog post updated successfully.');

        return redirect()->route('admin.blog.index');
    }

    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);

        $path = $post->image;
        $post->delete();
        $this->secureAssetDeletion($path);
        $this->clearSiteCache();

        Alert::success('Success', 'Article permanently deleted.');

        return back();
    }

    /**
     * @return Collection<int, string>
     */
    private function blogCategories(): Collection
    {
        return BlogPost::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }
}
