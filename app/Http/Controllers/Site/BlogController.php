<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::where('status', 'published')->whereNotNull('slug')->latest()->paginate(9);

        return view('site.blog.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = BlogPost::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $recentPosts = BlogPost::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->latest()
            ->limit(3)
            ->get();

        return view('site.blog.show', compact('post', 'recentPosts'));
    }
}
