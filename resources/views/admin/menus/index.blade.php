@extends('admin.layouts.app')
@section('title', 'Menus')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Menu Manager</div>
    <div class="page-sub">{{ $menus->count() }} menus</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.menus.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> New menu
    </a>
  </div>
</div>

<div class="page-body">
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Location</th><th>Items</th><th style="width:100px;"></th></tr></thead>
        <tbody>
          @forelse($menus as $menu)
          <tr>
            <td style="font-weight:600;">{{ $menu->name }}</td>
            <td><span class="badge badge-gray">{{ $menu->location }}</span></td>
            <td style="color:var(--muted);">{{ $menu->all_items_count }}</td>
            <td>
              <div style="display:flex;gap:5px;">
                <a href="{{ route('adminhu.menus.items', $menu) }}" class="btn btn-ghost btn-icon btn-icon sm" title="Manage items">
                  <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                </a>
                <a href="{{ route('adminhu.menus.edit', $menu) }}" class="btn btn-ghost btn-icon btn-icon sm">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <form method="POST" action="{{ route('adminhu.menus.destroy', $menu) }}" onsubmit="return confirm('Delete menu?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-icon btn-icon sm"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="4"><div class="empty-state" style="padding:40px;"><div class="t">No menus yet</div><div class="s"><a href="{{ route('adminhu.menus.create') }}" style="color:var(--navy);">Create your first menu →</a></div></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
