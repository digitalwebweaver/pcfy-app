@extends('admin.layouts.app')
@section('title', 'Users')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Users</div>
    <div class="page-sub">Manage admin team members and their roles</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.users.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
      Add user
    </a>
  </div>
</div>

<div class="page-body">
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Joined</th>
            <th style="width:80px;"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:9px;">
                <div style="width:30px;height:30px;border-radius:50%;background:var(--navy);color:#fff;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div style="font-weight:500;">{{ $user->name }}</div>
                @if($user->id === auth()->id())
                  <span class="badge badge-blue" style="font-size:9px;">You</span>
                @endif
              </div>
            </td>
            <td style="color:var(--muted);">{{ $user->email }}</td>
            <td>
              @foreach($user->roles as $role)
                @php
                  $cls = match($role->name) {
                    'super_admin' => 'badge-navy',
                    'admin'       => 'badge-blue',
                    'sales'       => 'badge-green',
                    'content'     => 'badge-amber',
                    'products'    => 'badge-gray',
                    default       => 'badge-gray',
                  };
                @endphp
                <span class="badge {{ $cls }}">{{ str_replace('_', ' ', ucfirst($role->name)) }}</span>
              @endforeach
            </td>
            <td style="color:var(--muted);font-size:11.5px;">{{ $user->created_at->format('d M Y') }}</td>
            <td>
              <div style="display:flex;gap:6px;">
                <a href="{{ route('adminhu.users.edit', $user) }}" class="btn btn-ghost btn-icon btn-icon sm" title="Edit">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('adminhu.users.destroy', $user) }}" onsubmit="return confirm('Delete {{ $user->name }}?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-icon btn-icon sm" title="Delete">
                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                  </button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="5"><div class="empty-state" style="padding:30px;">
            <div class="t">No users yet</div>
            <div class="s">Add your first team member above</div>
          </div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($users->hasPages())
    <div style="padding:10px 16px;border-top:1px solid var(--border);">{{ $users->links() }}</div>
    @endif
  </div>
</div>
@endsection
