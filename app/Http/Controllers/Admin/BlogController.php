<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogRequest;
use App\Models\BlogPost;
use App\Traits\InteractsWithAssets;
use Illuminate\Http\Request;
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
            'published_at' => $newStatus === 'published' ? now() : $post->published_at,
        ]);
        $this->clearSiteCache();

        Alert::success('Success', "Article marked as {$newStatus}.");

        return back();
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(BlogRequest $request)
    {
        $validated = $request->validated();

        $validated['slug'] = $request->filled('slug')
            ? (new BlogPost)->generateUniqueSlug($request->slug)
            : (new BlogPost)->generateUniqueSlug($validated['title']);

        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        $validated['image'] = $this->handleAssetUpload($request, 'image', 'site/img/blog', 'site/img/carousel-2.jpg');

        BlogPost::create($validated);
        $this->clearSiteCache();

        Alert::success('Success', 'Blog post created successfully.');

        return redirect()->route('admin.blog.index');
    }

    public function show($id)
    {
        return redirect()->route('admin.blog.edit', $id);
    }

    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);

        return view('admin.blog.edit', compact('post'));
    }

    public function update(BlogRequest $request, $id)
    {
        $post = BlogPost::findOrFail($id);
        $oldImage = $post->image;

        $validated = $request->validated();

        $validated['slug'] = $request->filled('slug')
            ? $post->generateUniqueSlug($request->slug, $id)
            : $post->generateUniqueSlug($validated['title'], $id);

        if ($post->status !== 'published' && $validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $validated['image'] = $this->handleAssetUpload($request, 'image', 'site/img/blog', $post->image);

        $post->update($validated);
        $this->deleteReplacedAsset($oldImage, $post->image);
        $this->clearSiteCache();

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

        Alert::success('Success', 'Article removed successfully.');

        return back();
    }
}
