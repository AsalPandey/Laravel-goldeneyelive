<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::publiclyVisible()->latest('published_at')->paginate(9);

        return view('site.blog.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = BlogPost::publiclyVisible()->where('slug', $slug)->firstOrFail();
        $recentPosts = BlogPost::publiclyVisible()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('site.blog.show', compact('post', 'recentPosts'));
    }
}
