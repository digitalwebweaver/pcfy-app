@extends('layouts.site')
@section('title', 'Create Account — pcfy.in')
@section('screen_label', 'Account Register')

@section('content')
<div style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding:40px 16px;background:var(--bg2);">
  <div style="width:100%;max-width:460px;">

    <div style="text-align:center;margin-bottom:28px;">
      <div style="font-family:var(--font-head);font-size:32px;font-weight:800;color:var(--navy);letter-spacing:-1px;">Create account</div>
      <div style="font-size:13.5px;color:var(--muted);margin-top:6px;">Track your quotes and save products for later</div>
    </div>

    <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:28px;">

      @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:11px 14px;border-radius:8px;margin-bottom:18px;font-size:13px;">
          @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
        </div>
      @endif

      <form action="{{ route('account.register.post') }}" method="POST" style="display:flex;flex-direction:column;gap:16px;">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
          <div style="display:flex;flex-direction:column;gap:5px;grid-column:span 2;">
            <label style="font-size:12px;font-weight:600;">Full name <span style="color:#dc2626;">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   placeholder="Rahul Kapoor"
                   style="height:42px;padding:0 13px;border:1.5px solid var(--border);border-radius:9px;font-size:14px;font-family:inherit;outline:none;"
                   onfocus="this.style.borderColor='var(--navy)'" onblur="this.style.borderColor='var(--border)'">
          </div>

          <div style="display:flex;flex-direction:column;gap:5px;grid-column:span 2;">
            <label style="font-size:12px;font-weight:600;">Email address <span style="color:#dc2626;">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   placeholder="rahul@company.com"
                   style="height:42px;padding:0 13px;border:1.5px solid var(--border);border-radius:9px;font-size:14px;font-family:inherit;outline:none;"
                   onfocus="this.style.borderColor='var(--navy)'" onblur="this.style.borderColor='var(--border)'">
          </div>

          <div style="display:flex;flex-direction:column;gap:5px;">
            <label style="font-size:12px;font-weight:600;">Phone <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
            <input type="tel" name="phone" value="{{ old('phone') }}"
                   placeholder="+91 98765 43210"
                   style="height:42px;padding:0 13px;border:1.5px solid var(--border);border-radius:9px;font-size:14px;font-family:inherit;outline:none;"
                   onfocus="this.style.borderColor='var(--navy)'" onblur="this.style.borderColor='var(--border)'">
          </div>

          <div style="display:flex;flex-direction:column;gap:5px;">
            <label style="font-size:12px;font-weight:600;">Company <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
            <input type="text" name="company" value="{{ old('company') }}"
                   placeholder="Acme Corp"
                   style="height:42px;padding:0 13px;border:1.5px solid var(--border);border-radius:9px;font-size:14px;font-family:inherit;outline:none;"
                   onfocus="this.style.borderColor='var(--navy)'" onblur="this.style.borderColor='var(--border)'">
          </div>

          <div style="display:flex;flex-direction:column;gap:5px;">
            <label style="font-size:12px;font-weight:600;">Password <span style="color:#dc2626;">*</span></label>
            <input type="password" name="password" required
                   placeholder="Min 8 characters"
                   style="height:42px;padding:0 13px;border:1.5px solid var(--border);border-radius:9px;font-size:14px;font-family:inherit;outline:none;"
                   onfocus="this.style.borderColor='var(--navy)'" onblur="this.style.borderColor='var(--border)'">
          </div>

          <div style="display:flex;flex-direction:column;gap:5px;">
            <label style="font-size:12px;font-weight:600;">Confirm password <span style="color:#dc2626;">*</span></label>
            <input type="password" name="password_confirmation" required
                   placeholder="Repeat password"
                   style="height:42px;padding:0 13px;border:1.5px solid var(--border);border-radius:9px;font-size:14px;font-family:inherit;outline:none;"
                   onfocus="this.style.borderColor='var(--navy)'" onblur="this.style.borderColor='var(--border)'">
          </div>
        </div>

        <button type="submit"
                style="height:44px;background:var(--navy);color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:700;font-family:inherit;cursor:pointer;margin-top:4px;"
                onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
          Create account
        </button>
      </form>

      <div style="text-align:center;margin-top:20px;font-size:13px;color:var(--muted);">
        Already have an account? <a href="{{ route('account.login') }}" style="color:var(--navy);font-weight:600;text-decoration:none;">Sign in →</a>
      </div>
    </div>
  </div>
</div>
@endsection
