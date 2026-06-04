@extends('admin.layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Edit User</div>
    <div class="page-sub">{{ $user->name }} · {{ $user->email }}</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.users.index') }}" class="btn btn-ghost">← Back</a>
  </div>
</div>

<div class="page-body">
  <div class="card" style="max-width:580px;">
    <div class="card-header"><div class="card-title">User details</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('adminhu.users.update', $user) }}">
        @csrf @method('PUT')
        <div class="form-grid" style="gap:12px;">
          <div class="field">
            <label>Full name <span class="req">*</span></label>
            <input class="input {{ $errors->has('name') ? 'error' : '' }}" type="text" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name')<div class="field-error">{{ $message }}</div>@enderror
          </div>
          <div class="field">
            <label>Email address <span class="req">*</span></label>
            <input class="input {{ $errors->has('email') ? 'error' : '' }}" type="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')<div class="field-error">{{ $message }}</div>@enderror
          </div>
          <div class="field">
            <label>New password <span style="color:var(--muted);font-weight:400;">(leave blank to keep)</span></label>
            <input class="input {{ $errors->has('password') ? 'error' : '' }}" type="password" name="password" placeholder="Min. 8 characters">
            @error('password')<div class="field-error">{{ $message }}</div>@enderror
          </div>
          <div class="field">
            <label>Confirm new password</label>
            <input class="input" type="password" name="password_confirmation" placeholder="Repeat if changing">
          </div>
          <div class="field span-2">
            <label>Role <span class="req">*</span></label>
            <select class="select {{ $errors->has('role') ? 'error' : '' }}" name="role" required>
              @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) === $role->name ? 'selected' : '' }}>
                  {{ str_replace('_', ' ', ucfirst($role->name)) }}
                </option>
              @endforeach
            </select>
            @error('role')<div class="field-error">{{ $message }}</div>@enderror
          </div>
        </div>
        <div style="margin-top:16px;display:flex;gap:8px;">
          <button type="submit" class="btn btn-primary">Save changes</button>
          <a href="{{ route('adminhu.users.index') }}" class="btn btn-ghost">Cancel</a>
          @if($user->id !== auth()->id())
          <form method="POST" action="{{ route('adminhu.users.destroy', $user) }}" style="margin-left:auto;" onsubmit="return confirm('Delete this user permanently?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete user</button>
          </form>
          @endif
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
