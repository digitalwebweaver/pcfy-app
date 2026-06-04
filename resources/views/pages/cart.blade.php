@extends('layouts.site')
@section('title', 'My Quote Cart — pcfy.in')
@section('screen_label', 'Cart')
@section('page_css')
<link rel="stylesheet" href="{{ asset('css/home-b.css') }}">
@endsection

@section('content')

<div style="background:var(--navy-xdark);color:#fff;padding:28px 0 24px;">
  <div class="container">
    <div style="font-size:11px;color:rgba(255,255,255,.5);margin-bottom:8px;">
      <a href="{{ url('/') }}" style="color:rgba(255,255,255,.5);text-decoration:none;">Home</a> › Quote Cart
    </div>
    <h1 style="font-family:var(--font-head);font-size:38px;font-weight:800;text-transform:uppercase;letter-spacing:-1px;">
      Your <span style="color:var(--accent);">Quote Cart</span>
    </h1>
    <p style="color:rgba(255,255,255,.6);font-size:14px;margin-top:8px;">
      Review your items, then submit a quote request — we'll respond within 24 hours.
    </p>
  </div>
</div>

<div class="container" style="padding-top:32px;padding-bottom:60px;">
  @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;">
      ✓ {{ session('success') }}
    </div>
  @endif
  @if(session('cart_added'))
    <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;">
      ✓ {{ session('cart_added') }}
    </div>
  @endif

  @if($items->isEmpty())
    <div style="text-align:center;padding:80px 20px;">
      <svg viewBox="0 0 24 24" style="width:56px;height:56px;stroke:var(--border-dark, #d1d5db);fill:none;stroke-width:1.5;margin:0 auto 16px;display:block;">
        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
        <path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.7a2 2 0 002-1.6L23 6H6"/>
      </svg>
      <div style="font-size:20px;font-weight:700;color:var(--text);margin-bottom:8px;">Your quote cart is empty</div>
      <p style="color:var(--muted);font-size:14px;margin-bottom:24px;">Browse our products and add items you're interested in.</p>
      <a href="{{ route('shop') }}" class="btn btn-navy-solid" style="display:inline-flex;">Browse products →</a>
    </div>
  @else
  <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;">

    <!-- Items -->
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;overflow:hidden;">
      <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:14px;font-weight:700;">{{ $items->count() }} item{{ $items->count() > 1 ? 's' : '' }}</span>
        <form action="{{ route('quote.cart.clear') }}" method="POST">
          @csrf @method('DELETE')
          <button style="font-size:12px;color:var(--red, #dc2626);background:none;border:none;cursor:pointer;font-family:inherit;">Clear all</button>
        </form>
      </div>

      @foreach($items as $key => $item)
      <div style="display:grid;grid-template-columns:56px 1fr auto;gap:14px;padding:16px 20px;border-bottom:1px solid var(--border);align-items:center;">
        <div style="width:56px;height:46px;background:var(--bg);border:1px solid var(--border);border-radius:6px;display:flex;align-items:center;justify-content:center;overflow:hidden;">
          @if(!empty($item['thumb']))
            <img src="{{ $item['thumb'] }}" alt="{{ $item['product_name'] }}" style="max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;padding:3px;">
          @else
            <svg viewBox="0 0 24 24" style="width:22px;height:22px;stroke:var(--muted);fill:none;stroke-width:1.5;"><rect x="2" y="4" width="20" height="13" rx="2"/><path d="M1 20h22"/></svg>
          @endif
        </div>

        <div>
          <div style="font-weight:600;font-size:14px;margin-bottom:3px;">
            <a href="{{ route('product', $item['product_slug']) }}" style="color:var(--text);text-decoration:none;">{{ $item['product_name'] }}</a>
          </div>
          <div style="font-size:12px;color:var(--muted);">
            Grade {{ $item['grade'] }}
            @if(!empty($item['sku'])) · SKU: {{ $item['sku'] }} @endif
          </div>
        </div>

        <div style="display:flex;align-items:center;gap:12px;">
          <form action="{{ route('quote.cart.update', $key) }}" method="POST" style="display:flex;align-items:center;gap:6px;">
            @csrf @method('PATCH')
            <input type="number" name="qty" value="{{ $item['qty'] }}" min="0" max="999"
                   style="width:60px;height:34px;text-align:center;border:1.5px solid var(--border);border-radius:6px;font-size:13px;font-family:inherit;"
                   onchange="this.form.submit()">
          </form>
          <form action="{{ route('quote.cart.remove', $key) }}" method="POST">
            @csrf @method('DELETE')
            <button style="width:28px;height:28px;border-radius:50%;background:var(--bg);border:1px solid var(--border);color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;">×</button>
          </form>
        </div>
      </div>
      @endforeach
    </div>

    <!-- Submit panel -->
    <div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;overflow:hidden;position:sticky;top:80px;">
      <div style="padding:16px 20px;border-bottom:1px solid var(--border);">
        <div style="font-size:14px;font-weight:700;margin-bottom:4px;">Ready to request a quote?</div>
        <div style="font-size:12px;color:var(--muted);">Fill in your details and we'll respond within 24 hours with pricing.</div>
      </div>
      <div style="padding:18px 20px;">
        <div style="font-size:13px;color:var(--muted);margin-bottom:16px;line-height:1.6;">
          Fill in your details and we'll respond with pricing within <strong style="color:var(--text);">24 hours</strong>. GST invoice included on all orders.
        </div>
        <a href="{{ route('quote.submit') }}" class="btn btn-accent" style="width:100%;justify-content:center;height:48px;font-size:14px;display:flex;">
          🛒 Request a Quote →
        </a>
        <div style="margin-top:12px;font-size:11.5px;color:var(--muted);text-align:center;">
          Or call us: <strong>+91 98765 43210</strong>
        </div>
      </div>
    </div>

  </div>
  @endif
</div>

@endsection
