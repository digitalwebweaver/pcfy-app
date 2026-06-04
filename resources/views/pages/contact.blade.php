@extends('layouts.site')
@section('title', 'Contact Us — pcfy.in')
@section('screen_label', 'Contact')
@section('page_css')
<link rel="stylesheet" href="{{ asset('css/home-b.css') }}">
@endsection

@section('content')
<div style="background:var(--navy-xdark);color:#fff;padding:28px 0 24px;">
  <div class="container">
    <h1 style="font-family:var(--font-head);font-size:38px;font-weight:800;text-transform:uppercase;letter-spacing:-1px;">
      Contact <span style="color:var(--accent);">Us</span>
    </h1>
    <p style="color:rgba(255,255,255,.6);font-size:14px;margin-top:8px;">
      Get in touch for quotes, bulk orders, or any questions.
    </p>
  </div>
</div>
<div class="container" style="padding-top:40px;padding-bottom:60px;">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;max-width:900px;">
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:28px;">
      <div style="font-size:16px;font-weight:700;margin-bottom:20px;">Send us a message</div>
      <form action="#" method="POST" style="display:flex;flex-direction:column;gap:14px;">
        @csrf
        <div><label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Name *</label><input type="text" name="name" required style="width:100%;height:38px;padding:0 12px;border:1.5px solid var(--border);border-radius:6px;font-size:13px;font-family:inherit;outline:none;"></div>
        <div><label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Email *</label><input type="email" name="email" required style="width:100%;height:38px;padding:0 12px;border:1.5px solid var(--border);border-radius:6px;font-size:13px;font-family:inherit;outline:none;"></div>
        <div><label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Phone</label><input type="tel" name="phone" style="width:100%;height:38px;padding:0 12px;border:1.5px solid var(--border);border-radius:6px;font-size:13px;font-family:inherit;outline:none;"></div>
        <div><label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;">Message *</label><textarea name="message" required rows="4" style="width:100%;padding:10px 12px;border:1.5px solid var(--border);border-radius:6px;font-size:13px;font-family:inherit;outline:none;resize:vertical;"></textarea></div>
        <button type="submit" class="btn btn-primary" style="height:44px;justify-content:center;">Send message →</button>
      </form>
    </div>
    <div>
      @php
        $cPhone   = \App\Models\Setting::get('contact_phone', '+91 98765 43210');
        $cEmail   = \App\Models\Setting::get('contact_email', 'hello@pcfy.in');
        $cAddress = \App\Models\Setting::get('contact_address', 'Vadodara, Gujarat, India');
        $cWa      = \App\Models\Setting::get('whatsapp_number', '919876543210');
        $cWaMsg   = urlencode(\App\Models\Setting::get('whatsapp_message', 'Hi, I have a query about pcfy.in'));
      @endphp
      <div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:28px;margin-bottom:16px;">
        <div style="font-size:14px;font-weight:700;margin-bottom:16px;">Get in touch</div>
        <div style="font-size:13.5px;line-height:2.2;color:var(--text);">
          <div>📞 <strong>{{ $cPhone }}</strong></div>
          <div>✉ <strong>{{ $cEmail }}</strong></div>
          <div>📍 {{ $cAddress }}</div>
          <div>🕐 Mon–Sat, 9am–6pm IST</div>
        </div>
        @if($cWa)
        <a href="https://wa.me/{{ $cWa }}?text={{ $cWaMsg }}" target="_blank"
           style="display:inline-flex;align-items:center;gap:6px;margin-top:14px;background:#25d366;color:#fff;padding:8px 16px;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;">
          <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M17.47 14.38c-.3-.15-1.77-.87-2.04-.97s-.47-.15-.67.15-.77.97-.94 1.17-.35.22-.65.07a8.16 8.16 0 01-2.4-1.48 9 9 0 01-1.66-2.06c-.17-.3 0-.46.13-.61s.3-.35.45-.52a2 2 0 00.3-.5.55.55 0 000-.52c-.07-.15-.67-1.62-.92-2.22s-.49-.5-.67-.51h-.57a1.1 1.1 0 00-.8.37 3.35 3.35 0 00-1 2.49 5.82 5.82 0 001.22 3.1 13.34 13.34 0 005.1 4.5c.71.31 1.27.49 1.7.63a4.1 4.1 0 001.88.12 3.07 3.07 0 002-1.42 2.5 2.5 0 00.17-1.42c-.07-.12-.27-.19-.57-.34z"/><path d="M12 2a10 10 0 00-8.6 15l-1.4 5 5.15-1.35A10 10 0 1012 2zm0 18.18a8.18 8.18 0 01-4.17-1.14l-.3-.18-3.08.8.82-3-.19-.31A8.19 8.19 0 1112 20.18z"/></svg>
          Chat on WhatsApp
        </a>
        @endif
      </div>
      <div style="background:var(--navy-xdark);border-radius:12px;padding:24px;color:#fff;">
        <div style="font-size:14px;font-weight:700;margin-bottom:8px;">Bulk / B2B enquiry?</div>
        <div style="font-size:13px;color:rgba(255,255,255,.6);margin-bottom:14px;">For orders of 10+ units, get a dedicated account manager and volume pricing.</div>
        <div style="font-size:13px;font-weight:600;color:var(--accent);">📞 Call: {{ $cPhone }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
