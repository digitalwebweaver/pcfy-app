@extends('admin.layouts.app')
@section('title', $post->exists ? 'Edit Post' : 'New Post')

@push('styles')
<style>
#quill-editor { min-height:340px; font-size:14px; font-family: inherit; }
.ql-toolbar.ql-snow { border-color:var(--border); border-radius:6px 6px 0 0; background:var(--bg); }
.ql-container.ql-snow { border-color:var(--border); border-radius:0 0 6px 6px; }
.ql-editor { min-height:320px; }
.tag-chip { display:inline-flex;align-items:center;gap:4px;background:var(--navy);color:#fff;border-radius:20px;padding:2px 10px;font-size:12px;font-weight:600; }
.tag-chip button { background:none;border:none;color:#fff;cursor:pointer;font-size:14px;line-height:1;padding:0; }
.tag-input-wrap { display:flex;flex-wrap:wrap;gap:6px;align-items:center;padding:6px 10px;border:1px solid var(--border);border-radius:6px;min-height:38px;cursor:text; }
.tag-input-wrap input { border:none;outline:none;background:transparent;font-size:13px;flex:1;min-width:100px; }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">{{ $post->exists ? 'Edit: '.$post->title : 'New Post' }}</div>
    @if($post->exists)
      <div class="page-sub">{{ ucfirst($post->status) }} &nbsp;·&nbsp; Created {{ $post->created_at->diffForHumans() }}</div>
    @endif
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.blog.index') }}" class="btn btn-ghost">← Back</a>
    @if($post->exists)
    <form method="POST" action="{{ route('adminhu.blog.destroy', $post) }}" onsubmit="return confirm('Delete this post?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger">Delete</button>
    </form>
    @endif
  </div>
</div>

<div class="page-body">
<form method="POST"
      action="{{ $post->exists ? route('adminhu.blog.update', $post) : route('adminhu.blog.store') }}"
      enctype="multipart/form-data"
      x-data="blogForm()"
      id="blog-form">
  @csrf
  @if($post->exists) @method('PUT') @endif

  <div style="display:grid;grid-template-columns:1fr 290px;gap:14px;align-items:start;">

    {{-- LEFT --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

      <div class="card">
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
          <div class="field">
            <label>Post title <span class="req">*</span></label>
            <input class="input {{ $errors->has('title') ? 'error' : '' }}"
                   type="text" name="title" value="{{ old('title', $post->title) }}"
                   required placeholder="Your post title…">
            @error('title')<div class="field-error">{{ $message }}</div>@enderror
          </div>

          <div class="field">
            <label>Excerpt <span style="color:var(--muted);font-weight:400;">(short summary, shown in listings)</span></label>
            <textarea class="input" name="excerpt" rows="2" placeholder="Brief summary…" style="resize:vertical;">{{ old('excerpt', $post->excerpt) }}</textarea>
            @error('excerpt')<div class="field-error">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title">Content</div></div>
        <div class="card-body">
          <div id="quill-editor">{!! old('body', $post->body) !!}</div>
          <input type="hidden" name="body" id="body-input">
          @error('body')<div class="field-error" style="margin-top:6px;">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title">SEO</div></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
          <div class="field">
            <label>Meta title <span style="color:var(--muted);font-weight:400;">(leave blank to use post title)</span></label>
            <input class="input" type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" placeholder="SEO title…" maxlength="160">
          </div>
          <div class="field">
            <label>Meta description</label>
            <textarea class="input" name="meta_description" rows="2" placeholder="SEO description…" maxlength="320" style="resize:vertical;">{{ old('meta_description', $post->meta_description) }}</textarea>
          </div>
        </div>
      </div>

    </div>

    {{-- RIGHT SIDEBAR --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

      <div class="card">
        <div class="card-header"><div class="card-title">Publish</div></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
          <div class="field">
            <label>Status</label>
            <select class="select" name="status" x-model="status">
              <option value="draft" {{ old('status', $post->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
              <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
              <option value="scheduled" {{ old('status', $post->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            </select>
          </div>

          <div class="field" x-show="status === 'scheduled' || status === 'published'">
            <label>Publish date</label>
            <input class="input" type="datetime-local" name="published_at"
                   value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
          </div>

          <div style="display:flex;align-items:center;gap:8px;">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" id="is_featured" value="1"
                   {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}
                   style="width:16px;height:16px;">
            <label for="is_featured" style="margin:0;font-weight:500;cursor:pointer;">Featured post</label>
          </div>

          <button type="submit" class="btn btn-primary" style="width:100%;">
            {{ $post->exists ? 'Save changes' : 'Publish post' }}
          </button>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title">Category</div></div>
        <div class="card-body">
          <select class="select" name="blog_category_id" style="width:100%;">
            <option value="">— No category —</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('blog_category_id', $post->blog_category_id) == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
          <div style="margin-top:8px;">
            <a href="{{ route('adminhu.blog.categories') }}" style="font-size:12px;color:var(--navy);">Manage categories →</a>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title">Tags</div></div>
        <div class="card-body" x-data="tagInput('{{ old('tags_input', $post->tags->pluck('name')->implode(', ')) }}')">
          <div class="tag-input-wrap" @click="$refs.tagField.focus()">
            <template x-for="tag in tags" :key="tag">
              <span class="tag-chip"><span x-text="tag"></span><button type="button" @click="remove(tag)">×</button></span>
            </template>
            <input x-ref="tagField" type="text" placeholder="Add tag…"
                   @keydown.enter.prevent="add($event.target.value); $event.target.value=''"
                   @keydown.comma.prevent="add($event.target.value); $event.target.value=''"
                   @blur="if($event.target.value) { add($event.target.value); $event.target.value='' }">
          </div>
          <input type="hidden" name="tags_input" :value="tags.join(', ')">
          <div style="font-size:11px;color:var(--muted);margin-top:5px;">Press Enter or comma to add</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title">Featured image</div></div>
        <div class="card-body" x-data="imagePreview('{{ $post->getFirstMediaUrl('featured') }}')">
          <template x-if="preview">
            <div style="margin-bottom:10px;border-radius:6px;overflow:hidden;border:1px solid var(--border);">
              <img :src="preview" style="width:100%;display:block;max-height:160px;object-fit:cover;">
            </div>
          </template>
          <input type="file" name="featured_image" accept="image/*" class="input" style="padding:5px;"
                 @change="preview = URL.createObjectURL($event.target.files[0])">
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
  placeholder: 'Write your post content…',
  modules: {
    toolbar: [
      [{ header: [2, 3, 4, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      ['blockquote', 'code-block'],
      ['link', 'image'],
      ['clean']
    ]
  }
});

document.getElementById('blog-form').addEventListener('formdata', e => {
  e.formData.set('body', quill.root.innerHTML);
});
document.getElementById('blog-form').addEventListener('submit', () => {
  document.getElementById('body-input').value = quill.root.innerHTML;
});

function blogForm() {
  return { status: '{{ old('status', $post->status ?? 'draft') }}' };
}
function tagInput(initial) {
  return {
    tags: initial ? initial.split(',').map(t => t.trim()).filter(Boolean) : [],
    add(val) { val = val.trim(); if (val && !this.tags.includes(val)) this.tags.push(val); },
    remove(tag) { this.tags = this.tags.filter(t => t !== tag); }
  };
}
function imagePreview(existing) {
  return { preview: existing || null };
}
</script>
@endpush
