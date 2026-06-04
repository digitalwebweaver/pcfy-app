@extends('admin.layouts.app')
@section('title', 'Add User')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Add User</div>
    <div class="page-sub">Create a new admin team member</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.users.index') }}" class="btn btn-ghost">← Back</a>
  </div>
</div>

<div class="page-body">
  <div class="card" style="max-width:580px;">
    <div class="card-header"><div class="card-title">User details</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('adminhu.users.store') }}">
        @csrf
        <div class="form-grid" style="gap:12px;">
          <div class="field">
            <label>Full name <span class="req">*</span></label>
            <input class="input {{ $errors->has('name') ? 'error' : '' }}" type="text" name="name" value="{{ old('name') }}" required placeholder="Jane Doe">
            @error('name')<div class="field-error">{{ $message }}</div>@enderror
          </div>
          <div class="field">
            <label>Email address <span class="req">*</span></label>
            <input class="input {{ $errors->has('email') ? 'error' : '' }}" type="email" name="email" value="{{ old('email') }}" required placeholder="jane@pcfy.in">
            @error('email')<div class="field-error">{{ $message }}</div>@enderror
          </div>
          <div class="field">
            <label>Password <span class="req">*</span></label>
            <input class="input {{ $errors->has('password') ? 'error' : '' }}" type="password" name="password" required placeholder="Min. 8 characters">
            @error('password')<div class="field-error">{{ $message }}</div>@enderror
          </div>
          <div class="field">
            <label>Confirm password <span class="req">*</span></label>
            <input class="input" type="password" name="password_confirmation" required placeholder="Repeat password">
          </div>
          <div class="field span-2">
            <label>Role <span class="req">*</span></label>
            <select class="select {{ $errors->has('role') ? 'error' : '' }}" name="role" required>
              <option value="">— Select role —</option>
              @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                  {{ str_replace('_', ' ', ucfirst($role->name)) }}
                </option>
              @endforeach
            </select>
            <div class="field-hint">
              Super Admin: full access · Admin: all except users · Sales: quotes only · Content: blog/pages · Products: catalogue only
            </div>
            @error('role')<div class="field-error">{{ $message }}</div>@enderror
          </div>
        </div>
        <div style="margin-top:16px;display:flex;gap:8px;">
          <button type="submit" class="btn btn-primary">Create user</button>
          <a href="{{ route('adminhu.users.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
