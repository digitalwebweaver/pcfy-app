@extends('layouts.site')
@section('title', 'Blog — pcfy.in')
@section('meta_description', 'Tips, guides and news about refurbished hardware from the pcfy.in team.')
@section('screen_label', 'Blog')

@push('styles')
<style>
.blog-layout { display:grid; grid-template-columns:1fr 280px; gap:32px; max-width:1200px; margin:0 auto; padding:40px 20px; }
@media(max-width:768px){ .blog-layout { grid-template-columns:1fr; } }
.post-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:20px; }
.post-card { background:#fff; border:1.5px solid #e2e8f0; border-radius:10px; overflow:hidden; transition:box-shadow .18s,transform .18s; }
.post-card:hover { box-shadow:0 6px 24px rgba(0,0,0,.1); transform:translateY(-2px); }
.post-card-img { width:100%; aspect-ratio:16/9; object-fit:cover; display:block; background:#f1f5f9; }
.post-card-img-placeholder { width:100%; aspect-ratio:16/9; background:linear-gradient(135deg,#1e3a5f 0%,#2d5a8e 100%); display:flex; align-items:center; justify-content:center; }
.post-card-body { padding:16px; }
.post-cat { display:inline-block; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--amber,#f59e0b); margin-bottom:6px; }
.post-title { font-size:16px; font-weight:700; color:#1e3a5f; line-height:1.35; margin-bottom:6px; text-decoration:none; display:block; }
.post-title:hover { color:#f59e0b; }
.post-excerpt { font-size:13px; color:#64748b; line-height:1.5; }
.post-meta { font-size:11px; color:#94a3b8; margin-top:10px; display:flex; gap:10px; align-items:center; }
.featured-card { background:#1e3a5f; color:#fff; border-radius:12px; padding:32px; margin-bottom:32px; display:grid; grid-template-columns:1fr 1fr; gap:24px; align-items:center; }
@media(max-width:640px){ .featured-card { grid-template-columns:1fr; } }
.featured-kicker { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#f59e0b; margin-bottom:8px; }
.featured-title { font-size:24px; font-weight:800; line-height:1.25; margin-bottom:10px; color:#fff; }
.featured-excerpt { font-size:14px; color:#cbd5e1; line-height:1.55; }
.cat-list { list-style:none; padding:0; margin:0; }
.cat-list li a { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e2e8f0; font-size:14px; color:#1e3a5f; text-decoration:none; font-weight:500; }
.cat-list li a:hover { color:#f59e0b; }
.sidebar-card { background:#fff; border:1.5px solid #e2e8f0; border-radius:10px; padding:20px; margin-bottom:20px; }
.sidebar-title { font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:14px; }
</style>
@endpush

@section('content')
<div style="background:#f8fafc; min-height:60vh; padding-bottom:60px;">

  <div style="background:#1e3a5f; padding:48px 20px; text-align:center; color:#fff;">
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#f59e0b;margin-bottom:8px;">Knowledge base</div>
    <h1 style="font-size:clamp(28px,5vw,48px);font-weight:800;margin:0 0 10px;">pcfy.in Blog</h1>
    <p style="color:#94a3b8;font-size:16px;max-width:500px;margin:0 auto;">Guides, tips and news about refurbished hardware for IT buyers.</p>
  </div>

  <div class="blog-layout">
    <div>
      {{-- Featured post --}}
      @if($featured)
      <div class="featured-card" style="margin-bottom:32px;">
        <div>
          <div class="featured-kicker">Featured</div>
          <div class="featured-title">{{ $featured->title }}</div>
          <div class="featured-excerpt">{{ $featured->excerpt }}</div>
          <a href="{{ route('blog.show', $featured->slug) }}" style="display:inline-block;margin-top:16px;background:#f59e0b;color:#1e3a5f;font-weight:700;padding:10px 22px;border-radius:6px;text-decoration:none;font-size:14px;">
            Read article →
          </a>
        </div>
        <div style="border-radius:8px;overflow:hidden;aspect-ratio:4/3;">
          @if($featured->getFirstMediaUrl('featured'))
            <img src="{{ $featured->getFirstMediaUrl('featured') }}" alt="{{ $featured->title }}" style="width:100%;height:100%;object-fit:cover;">
          @else
            <div style="width:100%;height:100%;background:#2d5a8e;display:flex;align-items:center;justify-content:center;">
              <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#4a7ab5" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
          @endif
        </div>
      </div>
      @endif

      {{-- Post grid --}}
      @if($posts->isEmpty())
        <div style="text-align:center;padding:60px 20px;color:#64748b;">
          <p style="font-size:18px;font-weight:600;">No posts yet</p>
          <p>Check back soon for articles from our team.</p>
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
              @if($post->category)
                <span class="post-cat">{{ $post->category->name }}</span>
              @endif
              <span class="post-title">{{ $post->title }}</span>
              @if($post->excerpt)
                <p class="post-excerpt">{{ Str::limit($post->excerpt, 100) }}</p>
              @endif
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

    {{-- Sidebar --}}
    <aside>
      <div class="sidebar-card">
        <div class="sidebar-title">Categories</div>
        @if($categories->isEmpty())
          <p style="font-size:13px;color:#94a3b8;">No categories yet.</p>
        @else
          <ul class="cat-list">
            <li><a href="{{ route('blog') }}"><span>All posts</span><span style="color:#94a3b8;">{{ $posts->total() }}</span></a></li>
            @foreach($categories as $cat)
            <li>
              <a href="{{ route('blog.category', $cat->slug) }}">
                <span>{{ $cat->name }}</span>
                <span style="color:#94a3b8;">{{ $cat->posts_count }}</span>
              </a>
            </li>
            @endforeach
          </ul>
        @endif
      </div>
    </aside>
  </div>
</div>
@endsection
