@extends('admin.layouts.app')
@section('title', 'My Profile')
@section('content')
<div class="page-header">
  <div><div class="page-title">My Profile</div><div class="page-sub">Update your name and password</div></div>
</div>
<div class="page-body">
  <div class="card" style="max-width:520px;">
    <div class="card-header"><div class="card-title">Account details</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('adminhu.profile') }}">
        @csrf @method('PUT')
        <div class="form-grid cols-1" style="gap:12px;">
          <div class="field">
            <label>Full name</label>
            <input class="input" type="text" name="name" value="{{ auth()->user()->name }}" required>
          </div>
          <div class="field">
            <label>Email address</label>
            <input class="input" type="email" name="email" value="{{ auth()->user()->email }}" required>
          </div>
          <div class="field">
            <label>New password <span style="color:var(--muted);font-weight:400;">(leave blank to keep)</span></label>
            <input class="input" type="password" name="password" placeholder="••••••••">
          </div>
          <div class="field">
            <label>Confirm new password</label>
            <input class="input" type="password" name="password_confirmation" placeholder="••••••••">
          </div>
        </div>
        <div style="margin-top:16px;"><button type="submit" class="btn btn-primary">Save changes</button></div>
      </form>
    </div>
  </div>
</div>
@endsection
