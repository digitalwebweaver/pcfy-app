<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;

class BlogFrontController extends Controller
{
    public function index()
    {
        $posts      = BlogPost::published()->with(['category', 'author'])->latest('published_at')->paginate(12);
        $categories = BlogCategory::active()->withCount(['posts' => fn($q) => $q->published()])->having('posts_count', '>', 0)->orderBy('name')->get();
        $featured   = BlogPost::published()->where('is_featured', true)->with(['category'])->latest('published_at')->first();
        return view('blog.index', compact('posts', 'categories', 'featured'));
    }

    public function show(string $slug)
    {
        $post    = BlogPost::published()->where('slug', $slug)->with(['category', 'author', 'tags'])->firstOrFail();
        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->when($post->blog_category_id, fn($q) => $q->where('blog_category_id', $post->blog_category_id))
            ->latest('published_at')
            ->limit(3)
            ->get();
        return view('blog.show', compact('post', 'related'));
    }

    public function category(string $slug)
    {
        $category   = BlogCategory::active()->where('slug', $slug)->firstOrFail();
        $posts      = BlogPost::published()->where('blog_category_id', $category->id)->with(['author'])->latest('published_at')->paginate(12);
        $categories = BlogCategory::active()->withCount(['posts' => fn($q) => $q->published()])->having('posts_count', '>', 0)->orderBy('name')->get();
        return view('blog.category', compact('category', 'posts', 'categories'));
    }
}
