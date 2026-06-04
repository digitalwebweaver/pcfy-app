@extends('admin.layouts.app')
@section('title', 'Blog Categories')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Blog Categories</div>
    <div class="page-sub">{{ $categories->count() }} categories</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.blog.index') }}" class="btn btn-ghost">← Posts</a>
  </div>
</div>

<div class="page-body">
  <div style="display:grid;grid-template-columns:1fr 340px;gap:14px;align-items:start;">

    {{-- Category list --}}
    <div class="card">
      <div class="table-wrap">
        <table>
          <thead><tr><th>Name</th><th>Posts</th><th>Status</th><th style="width:60px;"></th></tr></thead>
          <tbody>
            @forelse($categories as $cat)
            <tr>
              <td>
                <div style="font-weight:600;">{{ $cat->name }}</div>
                @if($cat->description)<div style="font-size:11.5px;color:var(--muted);">{{ Str::limit($cat->description, 60) }}</div>@endif
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">/blog/category/{{ $cat->slug }}</div>
              </td>
              <td>{{ $cat->posts_count }}</td>
              <td><span class="badge {{ $cat->is_active ? 'badge-green' : 'badge-gray' }}">{{ $cat->is_active ? 'Active' : 'Hidden' }}</span></td>
              <td>
                <form method="POST" action="{{ route('adminhu.blog.categories.destroy', $cat) }}" onsubmit="return confirm('Delete category?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-icon btn-icon sm" {{ $cat->posts_count ? 'disabled title=Has posts' : '' }}>
                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                  </button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="4"><div class="empty-state" style="padding:30px;"><div class="t">No categories yet</div></div></td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Add category form --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Add category</div></div>
      <div class="card-body">
        <form method="POST" action="{{ route('adminhu.blog.categories.store') }}" style="display:flex;flex-direction:column;gap:12px;">
          @csrf
          <div class="field">
            <label>Name <span class="req">*</span></label>
            <input class="input {{ $errors->has('name') ? 'error' : '' }}"
                   type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Tech News">
            @error('name')<div class="field-error">{{ $message }}</div>@enderror
          </div>
          <div class="field">
            <label>Description</label>
            <textarea class="input" name="description" rows="2" placeholder="Optional description…" style="resize:vertical;">{{ old('description') }}</textarea>
          </div>
          <button type="submit" class="btn btn-primary">Add category</button>
        </form>
      </div>
    </div>

  </div>
</div>
@endsection
