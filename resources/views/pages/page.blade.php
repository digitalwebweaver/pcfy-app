@extends('layouts.site')
@section('title', ($page->meta_title ?: $page->title).' — pcfy.in')
@section('meta_description', $page->meta_description)
@section('screen_label', $page->title)

@push('styles')
<style>
.page-hero { background:#1e3a5f; color:#fff; padding:48px 20px 40px; text-align:center; }
.page-hero h1 { font-size:clamp(26px,4vw,44px); font-weight:800; margin:0; }
.page-content-wrap { max-width:820px; margin:0 auto; padding:48px 20px 80px; }
.page-body-content { font-size:16px; line-height:1.8; color:#334155; }
.page-body-content h2,.page-body-content h3,.page-body-content h4 { color:#1e3a5f; margin:1.5em 0 .5em; font-weight:700; }
.page-body-content h2 { font-size:22px; }
.page-body-content h3 { font-size:18px; }
.page-body-content p { margin:0 0 1em; }
.page-body-content ul,.page-body-content ol { padding-left:1.5em; margin:0 0 1em; }
.page-body-content li { margin-bottom:.4em; }
.page-body-content a { color:#1e3a5f; text-decoration:underline; }
.page-body-content img { max-width:100%; border-radius:8px; margin:1em 0; }
.page-body-content blockquote { border-left:4px solid #f59e0b; margin:1.5em 0; padding:.75em 1.2em; background:#fefce8; border-radius:0 6px 6px 0; }
.page-body-content table { width:100%; border-collapse:collapse; margin:1em 0; }
.page-body-content th,.page-body-content td { border:1px solid #e2e8f0; padding:10px 14px; text-align:left; }
.page-body-content th { background:#f8fafc; font-weight:700; color:#1e3a5f; }
</style>
@endpush

@section('content')
<div class="page-hero">
  <h1>{{ $page->title }}</h1>
</div>

<div class="page-content-wrap">
  @if($page->body)
    <div class="page-body-content">{!! $page->body !!}</div>
  @else
    <p style="color:#94a3b8;text-align:center;padding:40px 0;">Content coming soon.</p>
  @endif
</div>
@endsection
