@extends('admin.layouts.app')
@section('title', $testimonial->exists ? 'Edit Testimonial' : 'New Testimonial')

@section('content')
<div class="page-header">
  <div><div class="page-title">{{ $testimonial->exists ? 'Edit Testimonial' : 'New Testimonial' }}</div></div>
  <div class="page-actions">
    <a href="{{ route('adminhu.testimonials.index') }}" class="btn btn-ghost">← Back</a>
    @if($testimonial->exists)
    <form method="POST" action="{{ route('adminhu.testimonials.destroy', $testimonial) }}" onsubmit="return confirm('Delete?')">
      @csrf @method('DELETE')
      <button class="btn btn-danger">Delete</button>
    </form>
    @endif
  </div>
</div>

<div class="page-body">
<form method="POST"
      action="{{ $testimonial->exists ? route('adminhu.testimonials.update', $testimonial) : route('adminhu.testimonials.store') }}"
      style="max-width:680px;">
  @csrf
  @if($testimonial->exists) @method('PUT') @endif

  <div class="card">
    <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">

      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
        <div class="field">
          <label>Name <span class="req">*</span></label>
          <input class="input" type="text" name="name"
                 value="{{ old('name', $testimonial->name) }}" required>
          @error('name')<div class="field-error">{{ $message }}</div>@enderror
        </div>
        <div class="field">
          <label>Role / Company</label>
          <input class="input" type="text" name="role"
                 value="{{ old('role', $testimonial->role) }}" placeholder="e.g. IT Manager, TechCorp">
        </div>
        <div class="field">
          <label>City</label>
          <input class="input" type="text" name="city"
                 value="{{ old('city', $testimonial->city) }}" placeholder="e.g. Mumbai">
        </div>
      </div>

      <div class="field">
        <label>Testimonial <span class="req">*</span></label>
        <textarea class="input" name="body" rows="4" required style="resize:vertical;"
                  placeholder="What did they say…">{{ old('body', $testimonial->body) }}</textarea>
        @error('body')<div class="field-error">{{ $message }}</div>@enderror
      </div>

      <div style="display:grid;grid-template-columns:120px 100px 1fr;gap:10px;align-items:end;">
        <div class="field">
          <label>Rating</label>
          <select class="select" name="rating" style="width:100%;">
            <option value="">—</option>
            @foreach([5,4,3,2,1] as $r)
              <option value="{{ $r }}" {{ old('rating', $testimonial->rating) == $r ? 'selected' : '' }}>{{ $r }} ★</option>
            @endforeach
          </select>
        </div>
        <div class="field">
          <label>Sort order</label>
          <input class="input" type="number" name="sort_order"
                 value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}" min="0">
        </div>
        <div style="display:flex;gap:16px;padding-bottom:4px;">
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:500;">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" style="width:15px;height:15px;"
                   {{ old('is_active', $testimonial->is_active ?? true) ? 'checked' : '' }}>
            Active
          </label>
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:500;">
            <input type="hidden" name="is_verified" value="0">
            <input type="checkbox" name="is_verified" value="1" style="width:15px;height:15px;"
                   {{ old('is_verified', $testimonial->is_verified) ? 'checked' : '' }}>
            Verified
          </label>
        </div>
      </div>

      <div style="display:flex;gap:8px;margin-top:4px;">
        <button type="submit" class="btn btn-primary">{{ $testimonial->exists ? 'Save changes' : 'Add testimonial' }}</button>
        <a href="{{ route('adminhu.testimonials.index') }}" class="btn btn-ghost">Cancel</a>
      </div>

    </div>
  </div>
</form>
</div>
@endsection
