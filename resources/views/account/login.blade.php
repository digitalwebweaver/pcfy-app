@extends('layouts.site')
@section('title', 'Sign In — pcfy.in')
@section('screen_label', 'Account Login')

@section('content')
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding:40px 16px;background:var(--bg2);">
  <div style="width:100%;max-width:420px;">

    <div style="text-align:center;margin-bottom:28px;">
      <div style="font-family:var(--font-head);font-size:32px;font-weight:800;color:var(--navy);letter-spacing:-1px;">Welcome back</div>
      <div style="font-size:13.5px;color:var(--muted);margin-top:6px;">Sign in to view your quotes &amp; saved items</div>
    </div>

    <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:28px;">

      @if(session('info'))
        <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;padding:11px 14px;border-radius:8px;margin-bottom:18px;font-size:13px;">
          {{ session('info') }}
        </div>
      @endif

      @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:11px 14px;border-radius:8px;margin-bottom:18px;font-size:13px;">
          @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
        </div>
      @endif

      <form action="{{ route('account.login.post') }}" method="POST" style="display:flex;flex-direction:column;gap:16px;">
        @csrf

        <div style="display:flex;flex-direction:column;gap:5px;">
          <label style="font-size:12px;font-weight:600;">Email address</label>
          <input type="email" name="email" value="{{ old('email') }}" required autofocus
                 placeholder="you@example.com"
                 style="height:42px;padding:0 13px;border:1.5px solid var(--border);border-radius:9px;font-size:14px;font-family:inherit;outline:none;transition:border-color .15s;"
                 onfocus="this.style.borderColor='var(--navy)'" onblur="this.style.borderColor='var(--border)'">
        </div>

        <div style="display:flex;flex-direction:column;gap:5px;">
          <label style="font-size:12px;font-weight:600;">Password</label>
          <input type="password" name="password" required
                 placeholder="••••••••"
                 style="height:42px;padding:0 13px;border:1.5px solid var(--border);border-radius:9px;font-size:14px;font-family:inherit;outline:none;transition:border-color .15s;"
                 onfocus="this.style.borderColor='var(--navy)'" onblur="this.style.borderColor='var(--border)'">
        </div>

        <label style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer;">
          <input type="checkbox" name="remember" style="width:15px;height:15px;accent-color:var(--navy);">
          Keep me signed in
        </label>

        <button type="submit"
                style="height:44px;background:var(--navy);color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:700;font-family:inherit;cursor:pointer;transition:opacity .15s;"
                onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
          Sign in
        </button>
      </form>

      <div style="text-align:center;margin-top:20px;font-size:13px;color:var(--muted);">
        No account yet? <a href="{{ route('account.register') }}" style="color:var(--navy);font-weight:600;text-decoration:none;">Create one free →</a>
      </div>
    </div>

  </div>
</div>
@endsection
