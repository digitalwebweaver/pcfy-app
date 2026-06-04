<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign in — pcfy Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;font-family:'Inter',sans-serif;background:#051e3a;-webkit-font-smoothing:antialiased;}
body{display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;}
.login-card{background:#fff;border-radius:10px;width:100%;max-width:380px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.4);}
.login-head{background:linear-gradient(135deg,#051e3a,#0f4c8a);padding:28px 28px 24px;text-align:center;}
.login-logo{font-size:28px;font-weight:700;color:#fff;letter-spacing:-.5px;font-family:'Inter',sans-serif;}
.login-logo span{color:#f5a623;}
.login-tag{font-size:11px;color:rgba(255,255,255,.5);margin-top:4px;letter-spacing:.04em;}
.login-body{padding:28px;}
.login-title{font-size:16px;font-weight:700;color:#111827;margin-bottom:4px;}
.login-sub{font-size:12px;color:#6b7280;margin-bottom:20px;}
.field{margin-bottom:14px;}
label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;}
.input{width:100%;height:38px;padding:0 12px;border:1.5px solid #d1d5db;border-radius:6px;font-size:13px;font-family:inherit;color:#111827;outline:none;transition:border-color .12s,box-shadow .12s;}
.input:focus{border-color:#0f4c8a;box-shadow:0 0 0 3px rgba(15,76,138,.1);}
.input.error{border-color:#dc2626;}
.error-msg{font-size:11px;color:#dc2626;margin-top:4px;}
.row{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;}
.check-row{display:flex;align-items:center;gap:7px;font-size:12px;color:#374151;}
.check-row input{accent-color:#0f4c8a;width:14px;height:14px;}
.btn-login{width:100%;height:40px;background:#0f4c8a;color:#fff;border:none;border-radius:6px;font-size:13.5px;font-weight:700;font-family:inherit;cursor:pointer;transition:background .15s;letter-spacing:.01em;}
.btn-login:hover{background:#08305a;}
.login-foot{padding:14px 28px;background:#f8fafc;border-top:1px solid #e5e7eb;text-align:center;font-size:11px;color:#9ca3af;}
.alert{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:9px 12px;border-radius:6px;font-size:12px;margin-bottom:16px;}
</style>
</head>
<body>
<div class="login-card">
  <div class="login-head">
    <div class="login-logo">pcfy<span>.in</span></div>
    <div class="login-tag">Admin Panel</div>
  </div>
  <div class="login-body">
    <div class="login-title">Welcome back</div>
    <div class="login-sub">Sign in to manage your store</div>

    @if($errors->any())
      <div class="alert">{{ $errors->first() }}</div>
    @endif
    @if(session('error'))
      <div class="alert">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('adminhu.login.post') }}">
      @csrf
      <div class="field">
        <label for="email">Email address</label>
        <input class="input {{ $errors->has('email') ? 'error' : '' }}" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@pcfy.in">
        @error('email')<div class="error-msg">{{ $message }}</div>@enderror
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input class="input {{ $errors->has('password') ? 'error' : '' }}" id="password" type="password" name="password" required placeholder="••••••••">
        @error('password')<div class="error-msg">{{ $message }}</div>@enderror
      </div>
      <div class="row">
        <label class="check-row">
          <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
          Remember me
        </label>
      </div>
      <button type="submit" class="btn-login">Sign in →</button>
    </form>
  </div>
  <div class="login-foot">pcfy.in &copy; {{ date('Y') }} — Secure admin access</div>
</div>
</body>
</html>
