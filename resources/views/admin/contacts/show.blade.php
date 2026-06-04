@extends('admin.layouts.app')
@section('title', 'Contact from '.$contact->name)

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">{{ $contact->name }}</div>
    <div class="page-sub">{{ $contact->email }} &nbsp;·&nbsp; {{ $contact->created_at->format('d M Y, g:i A') }}</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.contacts.index') }}" class="btn btn-ghost">← Inbox</a>
    <form method="POST" action="{{ route('adminhu.contacts.update', $contact) }}">
      @csrf @method('PUT')
      <input type="hidden" name="action" value="{{ $contact->status === 'archived' ? 'unread' : 'archive' }}">
      <button type="submit" class="btn btn-ghost">
        {{ $contact->status === 'archived' ? 'Mark unread' : 'Archive' }}
      </button>
    </form>
    <form method="POST" action="{{ route('adminhu.contacts.destroy', $contact) }}" onsubmit="return confirm('Delete this message?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger">Delete</button>
    </form>
  </div>
</div>

<div class="page-body">
  <div style="display:grid;grid-template-columns:1fr 280px;gap:14px;align-items:start;">

    {{-- Message + Reply --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

      <div class="card">
        <div class="card-header">
          <div class="card-title">{{ $contact->subject ?: 'No subject' }}</div>
          <span class="badge {{ match($contact->status) { 'unread'=>'badge-amber','read'=>'badge-gray','replied'=>'badge-green','archived'=>'badge-gray', default=>'badge-gray' } }}">{{ ucfirst($contact->status) }}</span>
        </div>
        <div class="card-body">
          <div style="font-size:15px;line-height:1.75;color:var(--text);white-space:pre-wrap;">{{ $contact->message }}</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title">Reply by email</div></div>
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success" style="margin-bottom:12px;">{{ session('success') }}</div>
          @endif
          <form method="POST" action="{{ route('adminhu.contacts.update', $contact) }}">
            @csrf @method('PUT')
            <input type="hidden" name="action" value="reply">
            <div class="field" style="margin-bottom:10px;">
              <label>To: <strong>{{ $contact->name }} &lt;{{ $contact->email }}&gt;</strong></label>
            </div>
            <div class="field" style="margin-bottom:10px;">
              <textarea class="input {{ $errors->has('reply_body') ? 'error' : '' }}"
                        name="reply_body" rows="6" required style="resize:vertical;"
                        placeholder="Type your reply…"></textarea>
              @error('reply_body')<div class="field-error">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">
              <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              Send reply
            </button>
          </form>
        </div>
      </div>

    </div>

    {{-- Sidebar --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Sender details</div></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:10px;font-size:13px;">
        <div>
          <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Name</div>
          <div style="font-weight:600;">{{ $contact->name }}</div>
        </div>
        <div>
          <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Email</div>
          <a href="mailto:{{ $contact->email }}" style="color:var(--navy);">{{ $contact->email }}</a>
        </div>
        @if($contact->phone)
        <div>
          <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Phone</div>
          <a href="tel:{{ $contact->phone }}" style="color:var(--navy);">{{ $contact->phone }}</a>
        </div>
        @endif
        <div>
          <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Received</div>
          <div>{{ $contact->created_at->format('d M Y, g:i A') }}</div>
          <div style="color:var(--muted);">{{ $contact->created_at->diffForHumans() }}</div>
        </div>
        @if($contact->read_at)
        <div>
          <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Read at</div>
          <div>{{ $contact->read_at->format('d M Y, g:i A') }}</div>
        </div>
        @endif
        @if($contact->replied_at)
        <div>
          <div style="font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;">Replied at</div>
          <div style="color:#22c55e;font-weight:600;">{{ $contact->replied_at->format('d M Y, g:i A') }}</div>
        </div>
        @endif
        @php $waNumber = \App\Models\Setting::get('whatsapp_number'); @endphp
        @if($contact->phone && $waNumber)
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $contact->phone) }}" target="_blank"
           class="btn btn-ghost" style="margin-top:6px;justify-content:center;background:#25d366;color:#fff;border-color:#25d366;">
          <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M17.47 14.38c-.3-.15-1.77-.87-2.04-.97s-.47-.15-.67.15-.77.97-.94 1.17-.35.22-.65.07a8.16 8.16 0 01-2.4-1.48 9 9 0 01-1.66-2.06c-.17-.3 0-.46.13-.61s.3-.35.45-.52a2 2 0 00.3-.5.55.55 0 000-.52c-.07-.15-.67-1.62-.92-2.22s-.49-.5-.67-.51h-.57a1.1 1.1 0 00-.8.37 3.35 3.35 0 00-1 2.49 5.82 5.82 0 001.22 3.1 13.34 13.34 0 005.1 4.5c.71.31 1.27.49 1.7.63a4.1 4.1 0 001.88.12 3.07 3.07 0 002-1.42 2.5 2.5 0 00.17-1.42c-.07-.12-.27-.19-.57-.34z"/><path d="M12 2a10 10 0 00-8.6 15l-1.4 5 5.15-1.35A10 10 0 1012 2zm0 18.18a8.18 8.18 0 01-4.17-1.14l-.3-.18-3.08.8.82-3-.19-.31A8.19 8.19 0 1112 20.18z"/></svg>
          WhatsApp
        </a>
        @endif
      </div>
    </div>

  </div>
</div>
@endsection
