<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::publiclyVisible()->latest('published_at')->paginate(9);

        return view('site.blog.index', compact('posts'));
    }

    public function show(string $slug): View
    {
        $post = BlogPost::publiclyVisible()->where('slug', $slug)->firstOrFail();
        $relatedCourses = $post->courses()
            ->publiclyVisible()
            ->with('courseCategory:id,name,status')
            ->orderByDesc('courses.is_featured')
            ->orderBy('courses.display_order')
            ->orderBy('courses.name')
            ->limit(3)
            ->get();
        $recentPosts = BlogPost::publiclyVisible()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('site.blog.show', compact('post', 'relatedCourses', 'recentPosts'));
    }

    public function preview(BlogPost $post): Response
    {
        $relatedCourses = $post->courses()
            ->publiclyVisible()
            ->with('courseCategory:id,name,status')
            ->orderByDesc('courses.is_featured')
            ->orderBy('courses.display_order')
            ->orderBy('courses.name')
            ->limit(3)
            ->get();
        $recentPosts = BlogPost::publiclyVisible()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return response()
            ->view('site.blog.show', [
                'post' => $post,
                'relatedCourses' => $relatedCourses,
                'recentPosts' => $recentPosts,
                'isPreview' => true,
            ])
            ->header('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }
}
