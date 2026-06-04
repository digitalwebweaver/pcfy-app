@extends('layouts.site')
@section('title', 'Submit Quote Request — pcfy.in')
@section('screen_label', 'Quote Submit')
@section('page_css')
<link rel="stylesheet" href="{{ asset('css/home-b.css') }}">
@endsection

@section('content')

<div style="background:var(--navy-xdark);color:#fff;padding:28px 0 24px;">
  <div class="container">
    <div style="font-size:12px;color:rgba(255,255,255,.5);margin-bottom:8px;">
      <a href="{{ url('/') }}" style="color:rgba(255,255,255,.5);text-decoration:none;">Home</a> ›
      <a href="{{ route('quote.cart') }}" style="color:rgba(255,255,255,.5);text-decoration:none;">Quote Cart</a> ›
      Submit Request
    </div>
    <h1 style="font-family:var(--font-head);font-size:38px;font-weight:800;text-transform:uppercase;letter-spacing:-1px;">
      Request a <span style="color:var(--accent);">Quote</span>
    </h1>
    <p style="color:rgba(255,255,255,.6);font-size:14px;margin-top:8px;">
      Fill in your details and we'll respond with pricing within 24 hours.
    </p>
  </div>
</div>

<div class="container" style="padding-top:32px;padding-bottom:60px;">
  <div style="display:grid;grid-template-columns:1fr 360px;gap:28px;align-items:start;">

    <!-- FORM -->
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:14px;overflow:hidden;">
      <div style="padding:20px 24px;border-bottom:1px solid var(--border);">
        <div style="font-size:16px;font-weight:700;">Your details</div>
        <div style="font-size:12.5px;color:var(--muted);margin-top:2px;">We use this to send your quote and follow up.</div>
      </div>
      <div style="padding:24px;">
        <form action="{{ route('quote.store') }}" method="POST">
          @csrf

          @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:18px;font-size:13px;">
              @foreach($errors->all() as $err) <div>• {{ $err }}</div> @endforeach
            </div>
          @endif

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">

            <div style="display:flex;flex-direction:column;gap:4px;">
              <label style="font-size:12px;font-weight:600;">Full name <span style="color:var(--red, #dc2626);">*</span></label>
              <input type="text" name="customer_name" value="{{ old('customer_name', $authUser?->name) }}" required
                     placeholder="Rahul Kapoor"
                     style="height:40px;padding:0 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13.5px;font-family:inherit;outline:none;">
            </div>

            <div style="display:flex;flex-direction:column;gap:4px;">
              <label style="font-size:12px;font-weight:600;">Email address <span style="color:var(--red, #dc2626);">*</span></label>
              <input type="email" name="customer_email" value="{{ old('customer_email', $authUser?->email) }}" required
                     placeholder="rahul@company.com"
                     style="height:40px;padding:0 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13.5px;font-family:inherit;outline:none;">
            </div>

            <div style="display:flex;flex-direction:column;gap:4px;">
              <label style="font-size:12px;font-weight:600;">Phone number</label>
              <input type="tel" name="customer_phone" value="{{ old('customer_phone', $authUser?->phone) }}"
                     placeholder="+91 98765 43210"
                     style="height:40px;padding:0 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13.5px;font-family:inherit;outline:none;">
            </div>

            <div style="display:flex;flex-direction:column;gap:4px;">
              <label style="font-size:12px;font-weight:600;">Company name</label>
              <input type="text" name="company_name" value="{{ old('company_name', $authUser?->company) }}"
                     placeholder="Acme Corp Pvt Ltd"
                     style="height:40px;padding:0 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13.5px;font-family:inherit;outline:none;">
            </div>

            <div style="display:flex;flex-direction:column;gap:4px;grid-column:span 2;">
              <label style="font-size:12px;font-weight:600;">GSTIN <span style="color:var(--muted);font-weight:400;">(for GST invoice)</span></label>
              <input type="text" name="gstin" value="{{ old('gstin') }}"
                     placeholder="22AAAAA0000A1Z5" maxlength="15"
                     style="height:40px;padding:0 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13.5px;font-family:inherit;outline:none;max-width:280px;">
            </div>

            <div style="display:flex;flex-direction:column;gap:4px;grid-column:span 2;">
              <label style="font-size:12px;font-weight:600;">Additional message</label>
              <textarea name="message" rows="4"
                        placeholder="Any specific requirements, delivery timeline, bulk quantity needs…"
                        style="padding:10px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13.5px;font-family:inherit;outline:none;resize:vertical;">{{ old('message') }}</textarea>
            </div>

          </div>

          <div style="margin-top:20px;display:flex;gap:12px;align-items:center;">
            <button type="submit" class="btn btn-accent" style="height:48px;font-size:14px;padding:0 32px;">
              🛒 Submit Quote Request →
            </button>
            <a href="{{ route('quote.cart') }}" style="font-size:13px;color:var(--muted);text-decoration:none;">← Back to cart</a>
          </div>

          <div style="margin-top:14px;font-size:12px;color:var(--muted);">
            By submitting, you agree to be contacted by our team regarding your quote. We respond within 24 hours.
          </div>
        </form>
      </div>
    </div>

    <!-- ORDER SUMMARY -->
    <div>
      <div style="background:#fff;border:1.5px solid var(--border);border-radius:14px;overflow:hidden;position:sticky;top:80px;">
        <div style="padding:16px 20px;border-bottom:1px solid var(--border);font-size:14px;font-weight:700;">
          Quote Summary
        </div>
        <div style="padding:16px 20px;">
          @foreach($cart as $key => $item)
          <div style="display:flex;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);align-items:center;">
            <div style="flex:1;min-width:0;">
              <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item['product_name'] }}</div>
              <div style="font-size:11.5px;color:var(--muted);">Grade {{ $item['grade'] }} · Qty {{ $item['qty'] }}</div>
            </div>
          </div>
          @endforeach
          <div style="margin-top:14px;padding:12px;background:var(--bg);border-radius:8px;font-size:12.5px;color:var(--muted);">
            <strong style="color:var(--text);">{{ $cart->count() }} item{{ $cart->count() > 1 ? 's' : '' }}</strong> in your quote.<br>
            Our team will provide pricing after review.
          </div>
        </div>
        <div style="padding:14px 20px;background:var(--bg);border-top:1px solid var(--border);">
          <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--muted);">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;flex-shrink:0;stroke:var(--green);fill:none;stroke-width:2;"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Pricing within 24 hours
          </div>
          <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--muted);margin-top:6px;">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;flex-shrink:0;stroke:var(--green);fill:none;stroke-width:2;"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            GST invoice included
          </div>
          <div style="display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--muted);margin-top:6px;">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;flex-shrink:0;stroke:var(--green);fill:none;stroke-width:2;"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            6-month warranty on all items
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection
