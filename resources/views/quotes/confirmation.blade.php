@extends('layouts.site')
@section('title', 'Quote Submitted — ' . $quote->quote_number . ' — pcfy.in')
@section('screen_label', 'Quote Confirmation')
@section('page_css')
<link rel="stylesheet" href="{{ asset('css/home-b.css') }}">
@endsection

@section('content')

<div style="background:var(--navy-xdark);color:#fff;padding:40px 0 36px;text-align:center;">
  <div class="container">
    <div style="width:64px;height:64px;border-radius:50%;background:var(--green);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
      <svg viewBox="0 0 24 24" style="width:32px;height:32px;stroke:#fff;fill:none;stroke-width:2.5;"><path d="M20 6L9 17l-5-5"/></svg>
    </div>
    <h1 style="font-family:var(--font-head);font-size:38px;font-weight:800;text-transform:uppercase;letter-spacing:-1px;margin-bottom:10px;">
      Quote <span style="color:var(--accent);">Submitted!</span>
    </h1>
    <p style="color:rgba(255,255,255,.7);font-size:15px;max-width:480px;margin:0 auto;">
      Thank you, <strong style="color:#fff;">{{ $quote->customer_name }}</strong>! We've received your request and will get back to you within 24 hours.
    </p>
  </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:60px;max-width:700px;">

  <!-- Quote number card -->
  <div style="background:var(--navy);color:#fff;border-radius:16px;padding:28px 32px;text-align:center;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background:radial-gradient(ellipse 50% 80% at 90% 50%,var(--accent-14),transparent 70%);"></div>
    <div style="position:relative;z-index:2;">
      <div style="font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:var(--accent);margin-bottom:10px;">Your Quote Reference</div>
      <div style="font-family:'DM Mono',monospace;font-size:36px;font-weight:700;letter-spacing:2px;color:#fff;">{{ $quote->quote_number }}</div>
      <div style="font-size:13px;color:rgba(255,255,255,.6);margin-top:8px;">Save this number to track your quote status</div>
    </div>
  </div>

  <!-- What happens next -->
  <div style="background:#fff;border:1.5px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:24px;">
    <div style="padding:18px 24px;border-bottom:1px solid var(--border);font-size:15px;font-weight:700;">What happens next?</div>
    <div style="padding:20px 24px;">
      <div style="display:flex;flex-direction:column;gap:16px;">
        @foreach([
          ['📧', 'Confirmation email sent', "We've sent a confirmation to {$quote->customer_email} with your quote details."],
          ['🔍', 'Our team reviews your request', 'We check availability, grade and stock for all your requested items within 24 hours.'],
          ['💰', 'You receive a detailed quote', 'We\'ll email you with itemised pricing, lead time, and payment options.'],
          ['✅', 'Confirm & we dispatch', 'Once you approve, we process your order and dispatch within 24 hours.'],
        ] as [$icon, $title, $desc])
        <div style="display:flex;gap:14px;align-items:flex-start;">
          <div style="font-size:22px;flex-shrink:0;width:36px;text-align:center;">{{ $icon }}</div>
          <div>
            <div style="font-size:13.5px;font-weight:600;margin-bottom:2px;">{{ $title }}</div>
            <div style="font-size:12.5px;color:var(--muted);">{{ $desc }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>

  <!-- Items summary -->
  @if($quote->items->count())
  <div style="background:#fff;border:1.5px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:24px;">
    <div style="padding:16px 24px;border-bottom:1px solid var(--border);font-size:14px;font-weight:700;">
      Items in your quote ({{ $quote->items->count() }})
    </div>
    <div style="padding:0 24px;">
      @foreach($quote->items as $item)
      <div style="display:flex;align-items:center;justify-content:space-between;padding:13px 0;border-bottom:1px solid var(--border);">
        <div>
          <div style="font-size:13.5px;font-weight:600;">{{ $item->product_name }}</div>
          <div style="font-size:12px;color:var(--muted);">Grade {{ $item->grade }}@if($item->product_sku) · {{ $item->product_sku }}@endif</div>
        </div>
        <div style="font-size:13px;font-weight:600;color:var(--navy);">Qty: {{ $item->quantity }}</div>
      </div>
      @endforeach
    </div>
    <div style="padding:12px 24px;background:var(--bg);font-size:12px;color:var(--muted);">
      Pricing will be confirmed by our team within 24 hours.
    </div>
  </div>
  @endif

  <!-- Actions -->
  <div style="display:flex;gap:12px;flex-wrap:wrap;">
    <a href="{{ route('shop') }}" class="btn btn-accent" style="height:46px;font-size:14px;">
      Continue browsing →
    </a>
    <a href="{{ url('/') }}" class="btn btn-outline" style="height:46px;font-size:14px;">
      Back to homepage
    </a>
  </div>

  <div style="margin-top:24px;padding:16px 20px;background:var(--bg);border-radius:10px;font-size:13px;color:var(--muted);">
    Need urgent help? Call us at <strong style="color:var(--text);">+91 98765 43210</strong>
    or WhatsApp us at <a href="{{ $quote->whatsapp_url }}" target="_blank" style="color:var(--navy);font-weight:600;">wa.me/919876543210</a>
    — mention your quote number <strong>{{ $quote->quote_number }}</strong>.
  </div>

</div>
@endsection
