@extends('admin.layouts.app')
@section('title', 'Media Library')

@push('styles')
<style>
.media-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:12px; }
.media-card { border:1.5px solid var(--border); border-radius:8px; overflow:hidden; background:var(--bg); position:relative; }
.media-card:hover .media-actions { opacity:1; }
.media-thumb { width:100%; aspect-ratio:4/3; object-fit:cover; display:block; background:#f1f5f9; }
.media-thumb-icon { width:100%; aspect-ratio:4/3; display:flex; align-items:center; justify-content:center; background:#f1f5f9; color:var(--muted); }
.media-info { padding:8px 10px; }
.media-name { font-size:11.5px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.media-meta { font-size:10.5px; color:var(--muted); margin-top:2px; }
.media-actions { position:absolute; top:6px; right:6px; opacity:0; transition:opacity .15s; }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Media Library</div>
    <div class="page-sub">{{ $media->total() }} files</div>
  </div>
  <div class="page-actions">
    <button class="btn btn-primary" onclick="document.getElementById('upload-input').click()">
      <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
      Upload files
    </button>
  </div>
</div>

<div class="page-body">

  {{-- Upload form (hidden, triggered by button) --}}
  <form method="POST" action="{{ route('adminhu.media.store') }}" enctype="multipart/form-data" id="upload-form">
    @csrf
    <input type="file" id="upload-input" name="files[]" multiple accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx"
           style="display:none" onchange="document.getElementById('upload-form').submit()">
  </form>

  {{-- Filters --}}
  <form method="GET" style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;">
    <input class="input" type="text" name="q" value="{{ request('q') }}" placeholder="Search filename…" style="width:220px;">
    <select class="select" name="type" style="width:auto;" onchange="this.form.submit()">
      <option value="">All types</option>
      <option value="image" {{ request('type')==='image' ? 'selected' : '' }}>Images</option>
      <option value="application/pdf" {{ request('type')==='application/pdf' ? 'selected' : '' }}>PDFs</option>
    </select>
    <button class="btn btn-ghost" type="submit">Search</button>
    @if(request()->anyFilled(['q','type']))
      <a href="{{ route('adminhu.media.index') }}" class="btn btn-ghost" style="color:var(--red);">Clear</a>
    @endif
  </form>

  @if($media->isEmpty())
    <div class="card">
      <div class="empty-state" style="padding:60px;">
        <div class="t">No files yet</div>
        <div class="s">Upload images and documents using the button above.</div>
      </div>
    </div>
  @else
    <div class="media-grid">
      @foreach($media as $file)
      <div class="media-card">
        @if(str_starts_with($file->mime_type, 'image/'))
          <img class="media-thumb" src="{{ $file->getUrl() }}" alt="{{ $file->file_name }}" loading="lazy">
        @else
          <div class="media-thumb-icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </div>
        @endif

        <div class="media-actions">
          <form method="POST" action="{{ route('adminhu.media.destroy', $file) }}" onsubmit="return confirm('Delete file?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-icon" style="width:26px;height:26px;">
              <svg viewBox="0 0 24 24" width="12" height="12"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
            </button>
          </form>
        </div>

        <div class="media-info">
          <div class="media-name" title="{{ $file->file_name }}">{{ $file->file_name }}</div>
          <div class="media-meta">{{ number_format($file->size / 1024, 0) }} KB</div>
          <div style="margin-top:5px;">
            <button type="button" class="btn btn-ghost btn-icon sm" style="font-size:10px;padding:2px 7px;height:auto;"
                    onclick="navigator.clipboard.writeText('{{ $file->getUrl() }}').then(()=>alert('URL copied'))">
              Copy URL
            </button>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    @if($media->hasPages())
      <div style="margin-top:20px;">{{ $media->appends(request()->query())->links() }}</div>
    @endif
  @endif

</div>
@endsection
