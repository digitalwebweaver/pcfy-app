@extends('admin.layouts.app')
@section('title', 'Categories')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Categories</div>
    <div class="page-sub">{{ $categories->total() }} categories</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.categories.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Add category
    </a>
  </div>
</div>

<div class="page-body">
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Category</th>
            <th>Parent</th>
            <th>Products</th>
            <th>Visible</th>
            <th style="width:80px;"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($categories as $cat)
          <tr>
            <td style="color:var(--muted);font-size:12px;">{{ $cat->sort_order }}</td>
            <td>
              <div style="display:flex;align-items:center;gap:9px;">
                @if($cat->icon_svg)
                  <div style="width:28px;height:28px;flex-shrink:0;color:var(--navy);">{!! $cat->icon_svg !!}</div>
                @endif
                <div>
                  <div style="font-weight:600;">{{ $cat->name }}</div>
                  <div style="font-size:11px;color:var(--muted);">{{ $cat->slug }}</div>
                </div>
              </div>
            </td>
            <td style="color:var(--muted);">{{ $cat->parent?->name ?? '—' }}</td>
            <td><span class="badge badge-gray">{{ $cat->products_count }}</span></td>
            <td>
              <span class="badge {{ $cat->is_visible ? 'badge-green' : 'badge-gray' }}">
                {{ $cat->is_visible ? 'Yes' : 'Hidden' }}
              </span>
            </td>
            <td>
              <div style="display:flex;gap:6px;">
                <a href="{{ route('adminhu.categories.edit', $cat) }}" class="btn btn-ghost btn-icon btn-icon sm">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <form method="POST" action="{{ route('adminhu.categories.destroy', $cat) }}" onsubmit="return confirm('Delete {{ $cat->name }}?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-icon btn-icon sm">
                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6">
            <div class="empty-state" style="padding:40px;">
              <div class="t">No categories yet</div>
              <div class="s">Add your first category — Laptops, Desktops, Servers…</div>
            </div>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($categories->hasPages())
    <div style="padding:10px 16px;border-top:1px solid var(--border);">{{ $categories->links() }}</div>
    @endif
  </div>
</div>
@endsection
