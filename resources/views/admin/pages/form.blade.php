@extends('admin.layouts.app')
@section('title', $page->exists ? 'Edit Page' : 'New Page')

@push('styles')
<style>
#quill-editor { min-height:300px; font-size:14px; font-family: inherit; }
.ql-toolbar.ql-snow { border-color:var(--border); border-radius:6px 6px 0 0; background:var(--bg); }
.ql-container.ql-snow { border-color:var(--border); border-radius:0 0 6px 6px; }
.ql-editor { min-height:280px; }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">{{ $page->exists ? 'Edit: '.$page->title : 'New Page' }}</div>
    @if($page->exists)
      <div class="page-sub">/{{ $page->slug }}</div>
    @endif
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.pages.index') }}" class="btn btn-ghost">← Back</a>
    @if($page->exists)
    <form method="POST" action="{{ route('adminhu.pages.destroy', $page) }}" onsubmit="return confirm('Delete this page?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger">Delete</button>
    </form>
    @endif
  </div>
</div>

<div class="page-body">
<form method="POST"
      action="{{ $page->exists ? route('adminhu.pages.update', $page) : route('adminhu.pages.store') }}"
      id="page-form">
  @csrf
  @if($page->exists) @method('PUT') @endif

  <div style="display:grid;grid-template-columns:1fr 260px;gap:14px;align-items:start;">

    {{-- LEFT --}}
    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="card">
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
          <div class="field">
            <label>Page title <span class="req">*</span></label>
            <input class="input {{ $errors->has('title') ? 'error' : '' }}"
                   type="text" name="title" value="{{ old('title', $page->title) }}" required placeholder="Page title…">
            @error('title')<div class="field-error">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title">Content</div></div>
        <div class="card-body">
          <div id="quill-editor">{!! old('body', $page->body) !!}</div>
          <input type="hidden" name="body" id="body-input">
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title">SEO</div></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
          <div class="field">
            <label>Meta title <span style="color:var(--muted);font-weight:400;">(leave blank to use page title)</span></label>
            <input class="input" type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" maxlength="160">
          </div>
          <div class="field">
            <label>Meta description</label>
            <textarea class="input" name="meta_description" rows="2" maxlength="320" style="resize:vertical;">{{ old('meta_description', $page->meta_description) }}</textarea>
          </div>
        </div>
      </div>
    </div>

    {{-- RIGHT --}}
    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="card">
        <div class="card-header"><div class="card-title">Settings</div></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">

          <div class="field">
            <label>Template</label>
            <select class="select" name="template" style="width:100%;">
              @foreach(['default' => 'Default', 'full-width' => 'Full Width', 'landing' => 'Landing'] as $val => $lbl)
                <option value="{{ $val }}" {{ old('template', $page->template ?? 'default') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
              @endforeach
            </select>
          </div>

          <div class="field">
            <label>Sort order</label>
            <input class="input" type="number" name="sort_order" value="{{ old('sort_order', $page->sort_order ?? 0) }}" min="0">
          </div>

          <div style="display:flex;flex-direction:column;gap:8px;">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500;">
              <input type="hidden" name="is_published" value="0">
              <input type="checkbox" name="is_published" value="1" style="width:16px;height:16px;"
                     {{ old('is_published', $page->is_published) ? 'checked' : '' }}>
              Published
            </label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500;">
              <input type="hidden" name="show_in_nav" value="0">
              <input type="checkbox" name="show_in_nav" value="1" style="width:16px;height:16px;"
                     {{ old('show_in_nav', $page->show_in_nav) ? 'checked' : '' }}>
              Show in nav
            </label>
          </div>

          <button type="submit" class="btn btn-primary" style="width:100%;">
            {{ $page->exists ? 'Save changes' : 'Create page' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</form>
</div>
@endsection

@push('scripts')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
const quill = new Quill('#quill-editor', {
  theme: 'snow',
  placeholder: 'Page content…',
  modules: {
    toolbar: [
      [{ header: [2, 3, 4, false] }],
      ['bold', 'italic', 'underline'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      ['blockquote', 'link'],
      ['clean']
    ]
  }
});
document.getElementById('page-form').addEventListener('submit', () => {
  document.getElementById('body-input').value = quill.root.innerHTML;
});
</script>
@endpush
