@extends('admin.layouts.app')
@section('title', 'Banners')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Banners</div>
    <div class="page-sub">{{ $banners->count() }} banners</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.banners.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> New banner
    </a>
  </div>
</div>

<div class="page-body">
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Title</th><th>Theme</th><th>Schedule</th><th>Status</th><th style="width:80px;"></th></tr></thead>
        <tbody>
          @forelse($banners as $banner)
          <tr>
            <td>
              @if($banner->eyebrow)<div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--amber);letter-spacing:.06em;">{{ $banner->eyebrow }}</div>@endif
              <div style="font-weight:600;">{{ $banner->title }}</div>
              @if($banner->subtitle)<div style="font-size:11.5px;color:var(--muted);">{{ Str::limit($banner->subtitle, 60) }}</div>@endif
            </td>
            <td><span class="badge badge-gray">{{ $banner->theme }}</span></td>
            <td style="font-size:11.5px;color:var(--muted);">
              @if($banner->starts_at || $banner->ends_at)
                {{ $banner->starts_at?->format('d M') ?? '∞' }} → {{ $banner->ends_at?->format('d M') ?? '∞' }}
              @else
                Always
              @endif
            </td>
            <td><span class="badge {{ $banner->is_active ? 'badge-green' : 'badge-gray' }}">{{ $banner->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td>
              <div style="display:flex;gap:5px;">
                <a href="{{ route('adminhu.banners.edit', $banner) }}" class="btn btn-ghost btn-icon btn-icon sm">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <form method="POST" action="{{ route('adminhu.banners.destroy', $banner) }}" onsubmit="return confirm('Delete banner?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-icon btn-icon sm"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="5"><div class="empty-state" style="padding:40px;"><div class="t">No banners yet</div><div class="s"><a href="{{ route('adminhu.banners.create') }}" style="color:var(--navy);">Create your first banner →</a></div></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
