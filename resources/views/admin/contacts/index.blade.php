@extends('admin.layouts.app')
@section('title', 'Contact Inbox')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Contact Inbox</div>
    <div class="page-sub">
      {{ $counts['unread'] }} unread &nbsp;·&nbsp; {{ $counts['replied'] }} replied &nbsp;·&nbsp; {{ $counts['archived'] }} archived
    </div>
  </div>
</div>

<div class="page-body">

  {{-- Status tabs --}}
  <div class="tabs" style="margin-bottom:12px;">
    @foreach(['' => 'Active', 'unread' => 'Unread', 'read' => 'Read', 'replied' => 'Replied', 'archived' => 'Archived'] as $s => $l)
    <a href="{{ request()->fullUrlWithQuery(['status' => $s ?: null, 'page' => null]) }}"
       class="tab {{ request('status', '') === $s ? 'active' : '' }}" style="text-decoration:none;">
      {{ $l }}
      @if($s && isset($counts[$s]) && $counts[$s] > 0)
        <span style="margin-left:4px;background:{{ $s==='unread'?'var(--amber)':'var(--bg)' }};border:1px solid var(--border);border-radius:10px;padding:0 6px;font-size:10px;font-weight:700;color:{{ $s==='unread'?'var(--navy)':'var(--muted)' }}">{{ $counts[$s] }}</span>
      @endif
    </a>
    @endforeach
  </div>

  {{-- Search --}}
  <form method="GET" action="{{ route('adminhu.contacts.index') }}" style="margin-bottom:12px;display:flex;gap:8px;">
    <input type="hidden" name="status" value="{{ request('status') }}">
    <input class="input" type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, message…" style="width:280px;">
    <button type="submit" class="btn btn-ghost">Search</button>
    @if(request('q'))<a href="{{ route('adminhu.contacts.index', ['status'=>request('status')]) }}" class="btn btn-ghost" style="color:var(--red);">Clear</a>@endif
  </form>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead><tr><th>From</th><th>Subject / Message</th><th>Status</th><th>Received</th><th style="width:60px;"></th></tr></thead>
        <tbody>
          @forelse($contacts as $c)
          <tr style="{{ $c->status === 'unread' ? 'background:rgba(245,158,11,.04)' : '' }}">
            <td>
              <div style="font-weight:{{ $c->status==='unread'?'700':'500' }};">
                {{ $c->name }}
                @if($c->status === 'unread')<span style="width:7px;height:7px;background:var(--amber);border-radius:50%;display:inline-block;margin-left:4px;"></span>@endif
              </div>
              <div style="font-size:11.5px;color:var(--muted);">{{ $c->email }}</div>
              @if($c->phone)<div style="font-size:11px;color:var(--muted);">{{ $c->phone }}</div>@endif
            </td>
            <td>
              @if($c->subject)<div style="font-weight:600;font-size:12.5px;">{{ $c->subject }}</div>@endif
              <div style="font-size:12px;color:var(--muted);">{{ Str::limit($c->message, 80) }}</div>
            </td>
            <td>
              <span class="badge {{ match($c->status) { 'unread'=>'badge-amber','read'=>'badge-gray','replied'=>'badge-green','archived'=>'badge-gray', default=>'badge-gray' } }}">
                {{ ucfirst($c->status) }}
              </span>
            </td>
            <td style="font-size:11.5px;color:var(--muted);">{{ $c->created_at->diffForHumans() }}</td>
            <td>
              <div style="display:flex;gap:5px;">
                <a href="{{ route('adminhu.contacts.show', $c) }}" class="btn btn-ghost btn-icon btn-icon sm">
                  <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <form method="POST" action="{{ route('adminhu.contacts.destroy', $c) }}" onsubmit="return confirm('Delete?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-icon btn-icon sm"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="5"><div class="empty-state" style="padding:40px;"><div class="t">No messages</div><div class="s">Contact form submissions will appear here.</div></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($contacts->hasPages())
      <div style="padding:10px 16px;border-top:1px solid var(--border);">{{ $contacts->appends(request()->query())->links() }}</div>
    @endif
  </div>
</div>
@endsection
