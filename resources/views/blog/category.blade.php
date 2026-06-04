@extends('layouts.site')
@section('title', $category->name.' — Blog — pcfy.in')
@section('screen_label', 'Blog category')

@push('styles')
<style>
.blog-layout { display:grid; grid-template-columns:1fr 280px; gap:32px; max-width:1200px; margin:0 auto; padding:40px 20px; }
@media(max-width:768px){ .blog-layout { grid-template-columns:1fr; } }
.post-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }
.post-card { background:#fff; border:1.5px solid #e2e8f0; border-radius:10px; overflow:hidden; transition:box-shadow .18s,transform .18s; }
.post-card:hover { box-shadow:0 6px 24px rgba(0,0,0,.1); transform:translateY(-2px); }
.post-card-img { width:100%; aspect-ratio:16/9; object-fit:cover; display:block; background:#f1f5f9; }
.post-card-img-placeholder { width:100%; aspect-ratio:16/9; background:linear-gradient(135deg,#1e3a5f 0%,#2d5a8e 100%); }
.post-card-body { padding:16px; }
.post-title { font-size:15px; font-weight:700; color:#1e3a5f; line-height:1.35; margin-bottom:6px; text-decoration:none; display:block; }
.post-title:hover { color:#f59e0b; }
.post-excerpt { font-size:13px; color:#64748b; line-height:1.5; }
.post-meta { font-size:11px; color:#94a3b8; margin-top:10px; display:flex; gap:10px; }
.cat-list { list-style:none; padding:0; margin:0; }
.cat-list li a { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; color:#1e3a5f; text-decoration:none; font-weight:500; }
.cat-list li a:hover,.cat-list li a.active { color:#f59e0b; }
.sidebar-card { background:#fff; border:1.5px solid #e2e8f0; border-radius:10px; padding:20px; margin-bottom:20px; }
.sidebar-title { font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:14px; }
</style>
@endpush

@section('content')
<div style="background:#f8fafc; min-height:60vh; padding-bottom:60px;">

  <div style="background:#1e3a5f; padding:40px 20px; color:#fff;">
    <div style="max-width:1200px; margin:0 auto;">
      <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#f59e0b;margin-bottom:6px;">Category</div>
      <h1 style="font-size:clamp(24px,4vw,40px);font-weight:800;margin:0 0 8px;">{{ $category->name }}</h1>
      @if($category->description)
        <p style="color:#94a3b8;font-size:15px;max-width:500px;margin:0;">{{ $category->description }}</p>
      @endif
    </div>
  </div>

  <div class="blog-layout">
    <div>
      @if($posts->isEmpty())
        <div style="text-align:center;padding:60px 0;color:#64748b;">
          <p style="font-size:18px;font-weight:600;">No posts in this category yet.</p>
          <a href="{{ route('blog') }}" style="color:#1e3a5f;font-weight:600;">View all posts →</a>
        </div>
      @else
        <div class="post-grid">
          @foreach($posts as $post)
          <a href="{{ route('blog.show', $post->slug) }}" style="text-decoration:none;">
          <div class="post-card">
            @if($post->getFirstMediaUrl('featured'))
              <img class="post-card-img" src="{{ $post->getFirstMediaUrl('featured') }}" alt="{{ $post->title }}" loading="lazy">
            @else
              <div class="post-card-img-placeholder"></div>
            @endif
            <div class="post-card-body">
              <span class="post-title">{{ $post->title }}</span>
              @if($post->excerpt)<p class="post-excerpt">{{ Str::limit($post->excerpt, 100) }}</p>@endif
              <div class="post-meta">
                <span>{{ $post->published_at?->format('d M Y') }}</span>
                <span>·</span>
                <span>{{ $post->read_time }}</span>
              </div>
            </div>
          </div>
          </a>
          @endforeach
        </div>
        @if($posts->hasPages())
          <div style="margin-top:30px;">{{ $posts->links() }}</div>
        @endif
      @endif
    </div>

    <aside>
      <div class="sidebar-card">
        <div class="sidebar-title">Categories</div>
        <ul class="cat-list">
          <li><a href="{{ route('blog') }}"><span>All posts</span></a></li>
          @foreach($categories as $cat)
          <li>
            <a href="{{ route('blog.category', $cat->slug) }}" class="{{ $cat->id === $category->id ? 'active' : '' }}">
              <span>{{ $cat->name }}</span>
              <span style="color:#94a3b8;">{{ $cat->posts_count }}</span>
            </a>
          </li>
          @endforeach
        </ul>
      </div>
    </aside>
  </div>
</div>
@endsection
