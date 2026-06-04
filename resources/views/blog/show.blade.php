@extends('layouts.site')
@section('title', $post->getSeoTitle($post->title).' — pcfy.in Blog')
@section('meta_description', $post->getSeoDescription($post->excerpt ?? ''))
@section('og_image', $post->getOgImage() ?: $post->getFirstMediaUrl('featured'))
@section('screen_label', 'Blog article')
@if($post->isNoindex())@section('meta_robots', '<meta name="robots" content="noindex">')@endif
@section('schema_json')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "{{ addslashes($post->title) }}",
  "description": "{{ addslashes($post->excerpt ?? '') }}",
  "datePublished": "{{ $post->published_at?->toIso8601String() }}",
  "dateModified": "{{ $post->updated_at->toIso8601String() }}",
  "author": { "@type": "Person", "name": "{{ addslashes($post->author?->name ?? 'pcfy.in') }}" },
  "publisher": { "@type": "Organization", "name": "pcfy.in" }
  @if($post->getFirstMediaUrl('featured')),"image": "{{ $post->getFirstMediaUrl('featured') }}"@endif
}
</script>
@endsection

@push('styles')
<style>
.article-wrap { max-width:760px; margin:0 auto; padding:48px 20px 60px; }
.article-header { margin-bottom:32px; }
.article-kicker { display:flex; gap:12px; align-items:center; margin-bottom:12px; flex-wrap:wrap; }
.article-cat { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#f59e0b; text-decoration:none; }
.article-cat:hover { text-decoration:underline; }
.article-title { font-size:clamp(24px,4vw,40px); font-weight:800; color:#1e3a5f; line-height:1.2; margin-bottom:12px; }
.article-meta { font-size:13px; color:#94a3b8; display:flex; gap:14px; flex-wrap:wrap; }
.article-excerpt { font-size:17px; color:#475569; line-height:1.65; margin:16px 0 24px; font-weight:500; }
.article-featured-img { width:100%; border-radius:10px; margin-bottom:32px; display:block; }
.article-body { font-size:16px; line-height:1.75; color:#334155; }
.article-body h2,.article-body h3,.article-body h4 { color:#1e3a5f; margin:1.5em 0 .5em; font-weight:700; }
.article-body h2 { font-size:22px; }
.article-body h3 { font-size:18px; }
.article-body p { margin:0 0 1em; }
.article-body ul,.article-body ol { padding-left:1.5em; margin:0 0 1em; }
.article-body li { margin-bottom:.4em; }
.article-body blockquote { border-left:4px solid #f59e0b; margin:1.5em 0; padding:.75em 1.2em; background:#fefce8; color:#78350f; border-radius:0 6px 6px 0; font-style:italic; }
.article-body a { color:#1e3a5f; text-decoration:underline; }
.article-body img { max-width:100%; border-radius:8px; margin:1em 0; }
.article-body code { background:#f1f5f9; padding:2px 6px; border-radius:4px; font-size:.9em; font-family:monospace; }
.article-body pre { background:#1e293b; color:#e2e8f0; padding:16px; border-radius:8px; overflow-x:auto; margin:1em 0; }
.article-tags { display:flex; flex-wrap:wrap; gap:8px; margin-top:32px; padding-top:24px; border-top:1.5px solid #e2e8f0; }
.article-tag { font-size:12px; font-weight:600; background:#f1f5f9; color:#475569; padding:4px 12px; border-radius:20px; text-decoration:none; }
.article-tag:hover { background:#1e3a5f; color:#fff; }
.related-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:16px; margin-top:16px; }
.related-card { border:1.5px solid #e2e8f0; border-radius:8px; overflow:hidden; text-decoration:none; display:block; transition:box-shadow .15s; }
.related-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); }
.related-img { width:100%; aspect-ratio:16/9; object-fit:cover; background:#f1f5f9; display:block; }
.related-body { padding:12px; }
.related-title { font-size:14px; font-weight:700; color:#1e3a5f; line-height:1.3; }
</style>
@endpush

@section('content')
<div style="background:#f8fafc; padding-bottom:60px;">

  {{-- Breadcrumb --}}
  <div style="background:#fff; border-bottom:1px solid #e2e8f0; padding:10px 20px;">
    <div style="max-width:760px; margin:0 auto; font-size:13px; color:#94a3b8;">
      <a href="{{ url('/') }}" style="color:#64748b;text-decoration:none;">Home</a>
      <span style="margin:0 6px;">›</span>
      <a href="{{ route('blog') }}" style="color:#64748b;text-decoration:none;">Blog</a>
      @if($post->category)
        <span style="margin:0 6px;">›</span>
        <a href="{{ route('blog.category', $post->category->slug) }}" style="color:#64748b;text-decoration:none;">{{ $post->category->name }}</a>
      @endif
      <span style="margin:0 6px;">›</span>
      <span style="color:#1e3a5f;font-weight:600;">{{ Str::limit($post->title, 50) }}</span>
    </div>
  </div>

  <div class="article-wrap">
    <div class="article-header">
      <div class="article-kicker">
        @if($post->category)
          <a class="article-cat" href="{{ route('blog.category', $post->category->slug) }}">{{ $post->category->name }}</a>
        @endif
        @if($post->is_featured)
          <span style="font-size:11px;font-weight:700;background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:12px;">Featured</span>
        @endif
      </div>
      <h1 class="article-title">{{ $post->title }}</h1>
      <div class="article-meta">
        @if($post->author)<span>By {{ $post->author->name }}</span><span>·</span>@endif
        <span>{{ $post->published_at?->format('d M Y') }}</span>
        <span>·</span>
        <span>{{ $post->read_time }}</span>
      </div>
      @if($post->excerpt)
        <p class="article-excerpt">{{ $post->excerpt }}</p>
      @endif
    </div>

    @if($post->getFirstMediaUrl('featured'))
      <img class="article-featured-img" src="{{ $post->getFirstMediaUrl('featured') }}" alt="{{ $post->title }}">
    @endif

    <div class="article-body">
      {!! $post->body !!}
    </div>

    @if($post->tags->isNotEmpty())
      <div class="article-tags">
        @foreach($post->tags as $tag)
          <span class="article-tag">#{{ $tag->name }}</span>
        @endforeach
      </div>
    @endif

    {{-- Related posts --}}
    @if($related->isNotEmpty())
    <div style="margin-top:48px;">
      <h3 style="font-size:18px;font-weight:700;color:#1e3a5f;margin-bottom:4px;">More articles</h3>
      <div class="related-grid">
        @foreach($related as $rel)
        <a class="related-card" href="{{ route('blog.show', $rel->slug) }}">
          @if($rel->getFirstMediaUrl('featured'))
            <img class="related-img" src="{{ $rel->getFirstMediaUrl('featured') }}" alt="{{ $rel->title }}" loading="lazy">
          @else
            <div class="related-img" style="background:linear-gradient(135deg,#1e3a5f,#2d5a8e);"></div>
          @endif
          <div class="related-body">
            <div class="related-title">{{ $rel->title }}</div>
            <div style="font-size:11px;color:#94a3b8;margin-top:4px;">{{ $rel->published_at?->format('d M Y') }}</div>
          </div>
        </a>
        @endforeach
      </div>
    </div>
    @endif

    <div style="margin-top:40px;">
      <a href="{{ route('blog') }}" style="color:#1e3a5f;font-weight:600;text-decoration:none;font-size:14px;">← Back to blog</a>
    </div>
  </div>
</div>
@endsection
