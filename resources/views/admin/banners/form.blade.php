@extends('admin.layouts.app')
@section('title', $banner->exists ? 'Edit Banner' : 'New Banner')

@section('content')
<div class="page-header">
  <div><div class="page-title">{{ $banner->exists ? 'Edit Banner' : 'New Banner' }}</div></div>
  <div class="page-actions">
    <a href="{{ route('adminhu.banners.index') }}" class="btn btn-ghost">← Back</a>
    @if($banner->exists)
    <form method="POST" action="{{ route('adminhu.banners.destroy', $banner) }}" onsubmit="return confirm('Delete banner?')">
      @csrf @method('DELETE')
      <button class="btn btn-danger">Delete</button>
    </form>
    @endif
  </div>
</div>

<div class="page-body">
<form method="POST"
      action="{{ $banner->exists ? route('adminhu.banners.update', $banner) : route('adminhu.banners.store') }}">
  @csrf
  @if($banner->exists) @method('PUT') @endif

  <div style="display:grid;grid-template-columns:1fr 260px;gap:14px;align-items:start;">

    <div class="card">
      <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
        <div class="field">
          <label>Eyebrow <span style="font-weight:400;color:var(--muted);">(small text above title)</span></label>
          <input class="input" type="text" name="eyebrow"
                 value="{{ old('eyebrow', $banner->eyebrow) }}" placeholder="e.g. Limited offer">
        </div>
        <div class="field">
          <label>Title <span class="req">*</span></label>
          <input class="input" type="text" name="title"
                 value="{{ old('title', $banner->title) }}" required placeholder="Main headline">
          @error('title')<div class="field-error">{{ $message }}</div>@enderror
        </div>
        <div class="field">
          <label>Subtitle</label>
          <textarea class="input" name="subtitle" rows="2" style="resize:vertical;"
                    placeholder="Supporting text…">{{ old('subtitle', $banner->subtitle) }}</textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="field">
            <label>Button text</label>
            <input class="input" type="text" name="btn_text"
                   value="{{ old('btn_text', $banner->btn_text) }}" placeholder="e.g. Shop now">
          </div>
          <div class="field">
            <label>Button URL</label>
            <input class="input" type="text" name="btn_url"
                   value="{{ old('btn_url', $banner->btn_url) }}" placeholder="/shop">
          </div>
        </div>
      </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="card">
        <div class="card-header"><div class="card-title">Settings</div></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
          <div class="field">
            <label>Theme</label>
            <select class="select" name="theme" style="width:100%;">
              @foreach(['navy'=>'Navy','amber'=>'Amber','white'=>'White','dark'=>'Dark'] as $val=>$lbl)
                <option value="{{ $val }}" {{ old('theme', $banner->theme ?? 'navy') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label>Sort order</label>
            <input class="input" type="number" name="sort_order"
                   value="{{ old('sort_order', $banner->sort_order ?? 0) }}" min="0">
          </div>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500;">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" style="width:16px;height:16px;"
                   {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}>
            Active
          </label>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title">Schedule <span style="font-weight:400;color:var(--muted);font-size:12px;">(optional)</span></div></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
          <div class="field">
            <label>Start date</label>
            <input class="input" type="datetime-local" name="starts_at"
                   value="{{ old('starts_at', $banner->starts_at?->format('Y-m-d\TH:i')) }}">
          </div>
          <div class="field">
            <label>End date</label>
            <input class="input" type="datetime-local" name="ends_at"
                   value="{{ old('ends_at', $banner->ends_at?->format('Y-m-d\TH:i')) }}">
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;">
            {{ $banner->exists ? 'Save changes' : 'Create banner' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</form>
</div>
@endsection
