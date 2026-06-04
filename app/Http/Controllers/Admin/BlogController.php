<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['category', 'author'])->latest();
        if ($request->filled('q'))      $query->where('title', 'like', "%{$request->q}%");
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('cat'))    $query->where('blog_category_id', $request->cat);

        $posts      = $query->paginate(20)->withQueryString();
        $categories = BlogCategory::active()->orderBy('name')->get();
        $counts = [
            'all'       => BlogPost::count(),
            'published' => BlogPost::where('status', 'published')->count(),
            'draft'     => BlogPost::where('status', 'draft')->count(),
            'scheduled' => BlogPost::where('status', 'scheduled')->count(),
        ];
        return view('admin.blog.index', compact('posts', 'categories', 'counts'));
    }

    public function create()
    {
        $categories = BlogCategory::active()->orderBy('name')->get();
        $tags       = BlogTag::orderBy('name')->get();
        return view('admin.blog.form', ['post' => new BlogPost, 'categories' => $categories, 'tags' => $tags]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['user_id'] = auth()->id();
        $post = BlogPost::create($data);
        $this->syncTags($post, $request);
        $this->handleFeaturedImage($post, $request);
        return redirect()->route('adminhu.blog.edit', $post)->with('success', 'Post created.');
    }

    public function show(BlogPost $blog) { return redirect()->route('adminhu.blog.edit', $blog); }

    public function edit(BlogPost $blog)
    {
        $blog->load(['category', 'tags', 'media']);
        $categories = BlogCategory::active()->orderBy('name')->get();
        $tags       = BlogTag::orderBy('name')->get();
        return view('admin.blog.form', ['post' => $blog, 'categories' => $categories, 'tags' => $tags]);
    }

    public function update(Request $request, BlogPost $blog)
    {
        $data = $this->validated($request, $blog);
        $blog->update($data);
        $this->syncTags($blog, $request);
        $this->handleFeaturedImage($blog, $request);
        return redirect()->route('adminhu.blog.edit', $blog)->with('success', 'Post saved.');
    }

    public function destroy(BlogPost $blog)
    {
        $blog->clearMediaCollection('featured');
        $blog->delete();
        return redirect()->route('adminhu.blog.index')->with('success', 'Post deleted.');
    }

    // ── Categories ───────────────────────────────────────
    public function categories()
    {
        $categories = BlogCategory::withCount('posts')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.blog.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'description' => ['nullable', 'string']]);
        BlogCategory::create($data);
        return back()->with('success', 'Category added.');
    }

    public function destroyCategory(BlogCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

    private function validated(Request $request, ?BlogPost $post = null): array
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'excerpt'          => ['nullable', 'string', 'max:500'],
            'body'             => ['nullable', 'string'],
            'blog_category_id' => ['nullable', 'exists:blog_categories,id'],
            'status'           => ['required', 'in:draft,published,scheduled'],
            'published_at'     => ['nullable', 'date'],
            'is_featured'      => ['nullable', 'boolean'],
            'meta_title'       => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:320'],
        ]);

        $data['is_featured']  = $request->boolean('is_featured');
        $data['published_at'] = $data['published_at'] ?? ($data['status'] === 'published' ? now() : null);

        return $data;
    }

    private function syncTags(BlogPost $post, Request $request): void
    {
        $tagNames = array_filter(array_map('trim', explode(',', $request->input('tags_input', ''))));
        $tagIds   = [];
        foreach ($tagNames as $name) {
            if (!$name) continue;
            $tag      = BlogTag::firstOrCreate(['name' => $name], ['slug' => \Str::slug($name)]);
            $tagIds[] = $tag->id;
        }
        $post->tags()->sync($tagIds);
    }

    private function handleFeaturedImage(BlogPost $post, Request $request): void
    {
        if ($request->hasFile('featured_image')) {
            $post->clearMediaCollection('featured');
            $post->addMedia($request->file('featured_image'))->toMediaCollection('featured');
        }
    }
}
