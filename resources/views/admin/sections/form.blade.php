@extends('admin.layouts.app')
@section('title', $section->exists ? 'Edit Section' : 'New Section')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">{{ $section->exists ? 'Edit: '.$section->label : 'New Section' }}</div>
    @if($section->exists)<div class="page-sub">{{ $section->page }} / {{ $section->section_key }}</div>@endif
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.sections.index') }}" class="btn btn-ghost">← Back</a>
    @if($section->exists)
    <form method="POST" action="{{ route('adminhu.sections.destroy', $section) }}" onsubmit="return confirm('Delete section?')">
      @csrf @method('DELETE')
      <button class="btn btn-danger">Delete</button>
    </form>
    @endif
  </div>
</div>

<div class="page-body">
<form method="POST"
      action="{{ $section->exists ? route('adminhu.sections.update', $section) : route('adminhu.sections.store') }}">
  @csrf
  @if($section->exists) @method('PUT') @endif

  <div style="display:grid;grid-template-columns:1fr 260px;gap:14px;align-items:start;">

    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="card">
        <div class="card-header"><div class="card-title">Identity</div></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div class="field">
              <label>Page <span class="req">*</span></label>
              <select class="select" name="page" style="width:100%;">
                @foreach(['home'=>'Homepage','about'=>'About','shop'=>'Shop','contact'=>'Contact'] as $val=>$lbl)
                  <option value="{{ $val }}" {{ old('page', $section->page) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
              </select>
            </div>
            <div class="field">
              <label>Section key <span class="req">*</span></label>
              <input class="input" type="text" name="section_key"
                     value="{{ old('section_key', $section->section_key) }}"
                     required placeholder="e.g. hero, features">
              @error('section_key')<div class="field-error">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="field">
            <label>Label <span class="req">*</span></label>
            <input class="input" type="text" name="label"
                   value="{{ old('label', $section->label) }}" required placeholder="Admin display name">
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title">Content fields</div></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
          @php $content = old('content', $section->content ?? []); @endphp
          @foreach(['heading' => 'Heading', 'subheading' => 'Subheading', 'body' => 'Body text', 'btn_text' => 'Button text', 'btn_url' => 'Button URL'] as $key => $label)
          <div class="field">
            <label>{{ $label }}</label>
            @if($key === 'body')
              <textarea class="input" name="content_{{ $key }}" rows="3" style="resize:vertical;">{{ $content[$key] ?? '' }}</textarea>
            @else
              <input class="input" type="text" name="content_{{ $key }}" value="{{ $content[$key] ?? '' }}">
            @endif
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="card">
        <div class="card-header"><div class="card-title">Settings</div></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
          <div class="field">
            <label>Sort order</label>
            <input class="input" type="number" name="sort_order"
                   value="{{ old('sort_order', $section->sort_order ?? 0) }}" min="0">
          </div>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500;">
            <input type="hidden" name="is_visible" value="0">
            <input type="checkbox" name="is_visible" value="1" style="width:16px;height:16px;"
                   {{ old('is_visible', $section->is_visible ?? true) ? 'checked' : '' }}>
            Visible
          </label>
          <button type="submit" class="btn btn-primary" style="width:100%;">
            {{ $section->exists ? 'Save changes' : 'Create section' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</form>
</div>
@endsection
