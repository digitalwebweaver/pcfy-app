@extends('admin.layouts.app')
@section('title', $category->exists ? 'Edit Category' : 'Add Category')

@section('content')
<div class="page-header">
  <div><div class="page-title">{{ $category->exists ? 'Edit Category' : 'Add Category' }}</div></div>
  <div class="page-actions">
    <a href="{{ route('adminhu.categories.index') }}" class="btn btn-ghost">← Back</a>
  </div>
</div>

<div class="page-body">
  <div class="card" style="max-width:640px;">
    <div class="card-header"><div class="card-title">Category details</div></div>
    <div class="card-body">
      <form method="POST"
            action="{{ $category->exists ? route('adminhu.categories.update', $category) : route('adminhu.categories.store') }}">
        @csrf
        @if($category->exists) @method('PUT') @endif

        <div class="form-grid" style="gap:12px;">

          <div class="field">
            <label>Category name <span class="req">*</span></label>
            <input class="input {{ $errors->has('name') ? 'error' : '' }}"
                   type="text" name="name" value="{{ old('name', $category->name) }}"
                   required placeholder="e.g. Laptops">
            @error('name')<div class="field-error">{{ $message }}</div>@enderror
          </div>

          <div class="field">
            <label>Parent category</label>
            <select class="select" name="parent_id">
              <option value="">— None (top-level) —</option>
              @foreach($parents as $parent)
                <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                  {{ $parent->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="field span-2">
            <label>Description</label>
            <textarea class="textarea" name="description" rows="2"
                      placeholder="Short description shown on category pages">{{ old('description', $category->description) }}</textarea>
          </div>

          <div class="field span-2">
            <label>Icon SVG</label>
            <textarea class="textarea" name="icon_svg" rows="3"
                      placeholder='<svg viewBox="0 0 24 24" ...>...</svg>'>{{ old('icon_svg', $category->icon_svg) }}</textarea>
            <div class="field-hint">Paste a raw SVG string. Used in category grid on homepage and shop page.</div>
          </div>

          <div class="field">
            <label>Sort order</label>
            <input class="input" type="number" name="sort_order"
                   value="{{ old('sort_order', $category->sort_order ?? 0) }}" min="0" style="width:120px;">
          </div>

          <div class="field" style="align-self:center;">
            <label class="toggle">
              <input type="hidden" name="is_visible" value="0">
              <input type="checkbox" name="is_visible" value="1"
                     {{ old('is_visible', $category->is_visible ?? true) ? 'checked' : '' }}>
              <div class="toggle-track"></div>
              <div class="toggle-thumb"></div>
              <span class="toggle-label">Visible on website</span>
            </label>
          </div>

          <div class="form-section-title span-2" style="margin-top:4px;">SEO (optional)</div>

          <div class="field span-2">
            <label>Meta title</label>
            <input class="input" type="text" name="meta_title"
                   value="{{ old('meta_title', $category->meta_title) }}"
                   placeholder="Defaults to category name">
          </div>

          <div class="field span-2">
            <label>Meta description</label>
            <textarea class="textarea" name="meta_description" rows="2"
                      placeholder="160 chars max">{{ old('meta_description', $category->meta_description) }}</textarea>
          </div>

        </div>

        <div style="margin-top:18px;display:flex;gap:8px;">
          <button type="submit" class="btn btn-primary">
            {{ $category->exists ? 'Save changes' : 'Create category' }}
          </button>
          <a href="{{ route('adminhu.categories.index') }}" class="btn btn-ghost">Cancel</a>
          @if($category->exists)
          <form method="POST" action="{{ route('adminhu.categories.destroy', $category) }}"
                style="margin-left:auto;" onsubmit="return confirm('Delete this category?')">
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
