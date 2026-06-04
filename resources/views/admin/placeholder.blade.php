@extends('admin.layouts.app')
@section('title', $title)

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">{{ $title }}</div>
    <div class="page-sub">Coming in a future phase</div>
  </div>
</div>
<div class="page-body">
  <div class="card">
    <div class="empty-state" style="padding:60px 20px;">
      <svg viewBox="0 0 24 24"><path d="M12 2l2 7h7l-5.5 4 2 7L12 16l-5.5 4 2-7L3 9h7z"/></svg>
      <div class="t">{{ $title }} — Phase 5</div>
      <div class="s">This section will be built in Phase 5 (Content Management).</div>
    </div>
  </div>
</div>
@endsection
