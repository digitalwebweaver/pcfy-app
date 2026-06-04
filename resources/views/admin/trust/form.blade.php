@extends('admin.layouts.app')
@section('title', $item->exists ? 'Edit Trust Item' : 'New Trust Item')

@section('content')
<div class="page-header">
  <div><div class="page-title">{{ $item->exists ? 'Edit Trust Item' : 'New Trust Item' }}</div></div>
  <div class="page-actions">
    <a href="{{ route('adminhu.trust.index') }}" class="btn btn-ghost">← Back</a>
    @if($item->exists)
    <form method="POST" action="{{ route('adminhu.trust.destroy', $item) }}" onsubmit="return confirm('Delete?')">
      @csrf @method('DELETE')
      <button class="btn btn-danger">Delete</button>
    </form>
    @endif
  </div>
</div>

<div class="page-body">
<form method="POST"
      action="{{ $item->exists ? route('adminhu.trust.update', $item) : route('adminhu.trust.store') }}"
      style="max-width:560px;">
  @csrf
  @if($item->exists) @method('PUT') @endif

  <div class="card">
    <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">

      <div class="field">
        <label>Title <span class="req">*</span></label>
        <input class="input" type="text" name="title"
               value="{{ old('title', $item->title) }}" required placeholder="e.g. Free Delivery">
        @error('title')<div class="field-error">{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label>Subtitle</label>
        <input class="input" type="text" name="subtitle"
               value="{{ old('subtitle', $item->subtitle) }}" placeholder="e.g. On orders above ₹5000">
      </div>

      <div class="field">
        <label>Icon SVG <span style="font-weight:400;color:var(--muted);">(paste full &lt;svg&gt; tag)</span></label>
        <textarea class="input" name="icon_svg" rows="4" style="resize:vertical;font-family:monospace;font-size:12px;"
                  placeholder="&lt;svg viewBox=&quot;0 0 24 24&quot;&gt;…&lt;/svg&gt;">{{ old('icon_svg', $item->icon_svg) }}</textarea>
        @if($item->icon_svg)
          <div style="margin-top:8px;display:flex;align-items:center;gap:8px;">
            <span style="font-size:12px;color:var(--muted);">Preview:</span>
            <div style="width:28px;height:28px;color:var(--navy);">{!! $item->icon_svg !!}</div>
          </div>
        @endif
      </div>

      <div style="display:grid;grid-template-columns:100px 1fr;gap:10px;align-items:end;">
        <div class="field">
          <label>Sort order</label>
          <input class="input" type="number" name="sort_order"
                 value="{{ old('sort_order', $item->sort_order ?? 0) }}" min="0">
        </div>
        <div style="padding-bottom:4px;">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500;">
            <input type="hidden" name="is_visible" value="0">
            <input type="checkbox" name="is_visible" value="1" style="width:16px;height:16px;"
                   {{ old('is_visible', $item->is_visible ?? true) ? 'checked' : '' }}>
            Visible on site
          </label>
        </div>
      </div>

      <div style="display:flex;gap:8px;margin-top:4px;">
        <button type="submit" class="btn btn-primary">{{ $item->exists ? 'Save changes' : 'Add item' }}</button>
        <a href="{{ route('adminhu.trust.index') }}" class="btn btn-ghost">Cancel</a>
      </div>

    </div>
  </div>
</form>
</div>
@endsection
