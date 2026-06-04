@extends('admin.layouts.app')
@section('title', $brand->exists ? 'Edit Brand' : 'Add Brand')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">{{ $brand->exists ? 'Edit Brand' : 'Add Brand' }}</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.brands.index') }}" class="btn btn-ghost">← Back</a>
  </div>
</div>

<div class="page-body">
  <div class="card" style="max-width:520px;">
    <div class="card-header"><div class="card-title">Brand details</div></div>
    <div class="card-body">
      <form method="POST"
            action="{{ $brand->exists ? route('adminhu.brands.update', $brand) : route('adminhu.brands.store') }}"
            enctype="multipart/form-data">
        @csrf
        @if($brand->exists) @method('PUT') @endif

        <div class="form-grid cols-1" style="gap:12px;">

          <div class="field">
            <label>Brand name <span class="req">*</span></label>
            <input class="input {{ $errors->has('name') ? 'error' : '' }}"
                   type="text" name="name" value="{{ old('name', $brand->name) }}"
                   required placeholder="e.g. Dell">
            @error('name')<div class="field-error">{{ $message }}</div>@enderror
          </div>

          <div class="field">
            <label>Logo <span style="color:var(--muted);font-weight:400;">(PNG/JPG, max 2MB)</span></label>
            @if($brand->logo)
              <div style="margin-bottom:8px;display:flex;align-items:center;gap:10px;">
                <img src="{{ Storage::url($brand->logo) }}" alt="{{ $brand->name }}"
                     style="height:40px;width:auto;object-fit:contain;border:1px solid var(--border);border-radius:4px;background:#fff;padding:4px;">
                <span style="font-size:11.5px;color:var(--muted);">Current logo</span>
              </div>
            @endif
            <input class="input" type="file" name="logo" accept="image/*" style="height:auto;padding:6px 10px;">
            @error('logo')<div class="field-error">{{ $message }}</div>@enderror
          </div>

          <div class="field">
            <label>Sort order</label>
            <input class="input" type="number" name="sort_order" value="{{ old('sort_order', $brand->sort_order ?? 0) }}"
                   min="0" style="width:120px;">
            <div class="field-hint">Lower number = appears first. 0 = auto.</div>
          </div>

          <div class="field">
            <label class="toggle">
              <input type="hidden" name="is_active" value="0">
              <input type="checkbox" name="is_active" value="1" {{ old('is_active', $brand->is_active ?? true) ? 'checked' : '' }}>
              <div class="toggle-track"></div>
              <div class="toggle-thumb"></div>
              <span class="toggle-label">Active (visible on website)</span>
            </label>
          </div>

        </div>

        <div style="margin-top:18px;display:flex;gap:8px;">
          <button type="submit" class="btn btn-primary">
            {{ $brand->exists ? 'Save changes' : 'Create brand' }}
          </button>
          <a href="{{ route('adminhu.brands.index') }}" class="btn btn-ghost">Cancel</a>
          @if($brand->exists)
          <form method="POST" action="{{ route('adminhu.brands.destroy', $brand) }}"
                style="margin-left:auto;" onsubmit="return confirm('Delete this brand?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
          </form>
          @endif
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
