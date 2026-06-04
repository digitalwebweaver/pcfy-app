@extends('admin.layouts.app')
@section('title', 'Brands')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Brands</div>
    <div class="page-sub">{{ $brands->total() }} brands in catalogue</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.brands.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Add brand
    </a>
  </div>
</div>

<div class="page-body">
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:40px;">#</th>
            <th>Brand</th>
            <th>Products</th>
            <th>Status</th>
            <th style="width:90px;"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($brands as $brand)
          <tr>
            <td style="color:var(--muted);font-size:12px;">{{ $brand->sort_order }}</td>
            <td>
              <div style="display:flex;align-items:center;gap:10px;">
                @if($brand->logo)
                  <img src="{{ Storage::url($brand->logo) }}" alt="{{ $brand->name }}"
                       style="width:36px;height:36px;object-fit:contain;border:1px solid var(--border);border-radius:4px;background:#fff;padding:3px;">
                @else
                  <div style="width:36px;height:36px;border:1px solid var(--border);border-radius:4px;background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--muted);">
                    {{ strtoupper(substr($brand->name,0,1)) }}
                  </div>
                @endif
                <span style="font-weight:600;">{{ $brand->name }}</span>
              </div>
            </td>
            <td>
              <span class="badge badge-gray">{{ $brand->products_count }}</span>
            </td>
            <td>
              <span class="badge {{ $brand->is_active ? 'badge-green' : 'badge-gray' }}">
                {{ $brand->is_active ? 'Active' : 'Hidden' }}
              </span>
            </td>
            <td>
              <div style="display:flex;gap:6px;">
                <a href="{{ route('adminhu.brands.edit', $brand) }}" class="btn btn-ghost btn-icon btn-icon sm" title="Edit">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <form method="POST" action="{{ route('adminhu.brands.destroy', $brand) }}" onsubmit="return confirm('Delete {{ $brand->name }}?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-icon btn-icon sm" title="Delete">
                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="5">
            <div class="empty-state" style="padding:40px;">
              <div class="t">No brands yet</div>
              <div class="s">Add your first brand — Dell, HP, Lenovo…</div>
            </div>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($brands->hasPages())
    <div style="padding:10px 16px;border-top:1px solid var(--border);">{{ $brands->links() }}</div>
    @endif
  </div>
</div>
@endsection
