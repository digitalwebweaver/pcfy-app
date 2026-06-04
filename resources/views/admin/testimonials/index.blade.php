@extends('admin.layouts.app')
@section('title', 'Testimonials')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Testimonials</div>
    <div class="page-sub">{{ $testimonials->count() }} entries</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.testimonials.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Add testimonial
    </a>
  </div>
</div>

<div class="page-body">
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Person</th><th>Excerpt</th><th>Rating</th><th>Status</th><th style="width:80px;"></th></tr></thead>
        <tbody>
          @forelse($testimonials as $t)
          <tr>
            <td>
              <div style="font-weight:600;">{{ $t->name }}</div>
              <div style="font-size:11.5px;color:var(--muted);">{{ $t->role }}{{ $t->city ? ' · '.$t->city : '' }}</div>
            </td>
            <td style="font-size:12px;color:var(--muted);max-width:300px;">{{ Str::limit($t->body, 80) }}</td>
            <td>
              @if($t->rating)
                <span style="color:#f59e0b;font-size:13px;">{{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}</span>
              @else —
              @endif
            </td>
            <td>
              <span class="badge {{ $t->is_active ? 'badge-green' : 'badge-gray' }}">{{ $t->is_active ? 'Active' : 'Hidden' }}</span>
              @if($t->is_verified)<span class="badge badge-green" style="margin-left:4px;">✓ Verified</span>@endif
            </td>
            <td>
              <div style="display:flex;gap:5px;">
                <a href="{{ route('adminhu.testimonials.edit', $t) }}" class="btn btn-ghost btn-icon btn-icon sm">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <form method="POST" action="{{ route('adminhu.testimonials.destroy', $t) }}" onsubmit="return confirm('Delete?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-icon btn-icon sm"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="5"><div class="empty-state" style="padding:40px;"><div class="t">No testimonials yet</div></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
