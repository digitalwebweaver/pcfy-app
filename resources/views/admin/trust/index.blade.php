@extends('admin.layouts.app')
@section('title', 'Trust Items')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Trust Bar Items</div>
    <div class="page-sub">Icons shown in the trust/credibility strip</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.trust.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Add item
    </a>
  </div>
</div>

<div class="page-body">
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Icon</th><th>Title</th><th>Subtitle</th><th>Sort</th><th>Visible</th><th style="width:80px;"></th></tr></thead>
        <tbody>
          @forelse($items as $item)
          <tr>
            <td>
              @if($item->icon_svg)
                <div style="width:28px;height:28px;color:var(--navy);">{!! $item->icon_svg !!}</div>
              @else
                <span style="color:var(--muted);">—</span>
              @endif
            </td>
            <td style="font-weight:600;">{{ $item->title }}</td>
            <td style="font-size:12px;color:var(--muted);">{{ $item->subtitle ?? '—' }}</td>
            <td style="color:var(--muted);">{{ $item->sort_order }}</td>
            <td><span class="badge {{ $item->is_visible ? 'badge-green' : 'badge-gray' }}">{{ $item->is_visible ? 'Yes' : 'No' }}</span></td>
            <td>
              <div style="display:flex;gap:5px;">
                <a href="{{ route('adminhu.trust.edit', $item) }}" class="btn btn-ghost btn-icon btn-icon sm">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <form method="POST" action="{{ route('adminhu.trust.destroy', $item) }}" onsubmit="return confirm('Delete?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-icon btn-icon sm"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6"><div class="empty-state" style="padding:40px;"><div class="t">No trust items yet</div></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
