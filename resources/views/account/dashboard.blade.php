@extends('layouts.site')
@section('title', 'My Account — pcfy.in')
@section('screen_label', 'Account Dashboard')

@section('page_css')
<style>
.acc-wrap{max-width:1080px;margin:0 auto;padding:32px 20px 60px;}
.acc-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:14px;}
.acc-name{font-family:var(--font-head);font-size:30px;font-weight:800;color:var(--navy);letter-spacing:-0.5px;}
.acc-email{font-size:13px;color:var(--muted);margin-top:2px;}
.acc-tabs{display:flex;gap:4px;border-bottom:2px solid var(--border);margin-bottom:24px;}
.acc-tab{padding:10px 20px;font-size:13.5px;font-weight:600;color:var(--muted);cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px;transition:color .15s,border-color .15s;background:none;border-top:none;border-left:none;border-right:none;font-family:inherit;}
.acc-tab.on{color:var(--navy);border-bottom-color:var(--navy);}
.acc-panel{display:none;}.acc-panel.on{display:block;}

/* Quote rows */
.q-row{display:grid;grid-template-columns:auto 1fr auto auto;gap:16px;align-items:center;padding:16px 20px;border:1.5px solid var(--border);border-radius:12px;margin-bottom:10px;background:#fff;transition:border-color .15s;}
.q-row:hover{border-color:var(--navy);}
.q-num{font-family:var(--font-head);font-size:15px;font-weight:800;color:var(--navy);}
.q-date{font-size:11.5px;color:var(--muted);margin-top:2px;}
.q-items{font-size:13px;color:var(--text);}
.q-badge{font-size:10.5px;font-weight:700;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:.04em;}
.q-badge.new{background:#eff6ff;color:#2563eb;}
.q-badge.reviewing{background:#fefce8;color:#a16207;}
.q-badge.quoted{background:#f0fdf4;color:#16a34a;}
.q-badge.won{background:#16a34a;color:#fff;}
.q-badge.lost{background:#f1f5f9;color:#94a3b8;}

/* Wishlist grid */
.wl-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
@media(max-width:700px){.wl-grid{grid-template-columns:repeat(2,1fr);}.q-row{grid-template-columns:1fr auto;}}
.wl-card{background:#fff;border:1.5px solid var(--border);border-radius:12px;overflow:hidden;position:relative;}
.wl-img{height:140px;background:#fff;display:flex;align-items:center;justify-content:center;}
.wl-img img{width:100%;height:100%;object-fit:contain;padding:8px;}
.wl-body{padding:12px 14px;}
.wl-brand{font-size:10px;color:var(--navy);font-weight:700;text-transform:uppercase;letter-spacing:.08em;}
.wl-name{font-size:13px;font-weight:600;color:var(--text);margin:3px 0 10px;line-height:1.3;}
.wl-price{font-family:var(--font-head);font-size:18px;font-weight:800;color:var(--navy);}
.wl-rm{position:absolute;top:8px;right:8px;width:28px;height:28px;background:rgba(255,255,255,.9);border:1px solid var(--border);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:13px;line-height:1;}
</style>
@endsection

@section('content')
<div style="background:var(--navy-xdark);padding:28px 0 24px;color:#fff;">
  <div class="acc-wrap" style="padding-top:0;padding-bottom:0;">
    <div style="font-size:12px;color:rgba(255,255,255,.45);margin-bottom:8px;">Home › My Account</div>
    <div style="font-family:var(--font-head);font-size:36px;font-weight:800;letter-spacing:-1px;">
      My <span style="color:var(--accent);">Account</span>
    </div>
  </div>
</div>

<div class="acc-wrap">

  @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13.5px;">
      {{ session('success') }}
    </div>
  @endif

  <div class="acc-head">
    <div>
      <div class="acc-name">{{ $user->name }}</div>
      <div class="acc-email">{{ $user->email }}@if($user->company) · {{ $user->company }}@endif</div>
    </div>
    <form action="{{ route('account.logout') }}" method="POST">
      @csrf
      <button type="submit"
              style="height:36px;padding:0 16px;border:1.5px solid var(--border);border-radius:8px;background:#fff;font-size:12.5px;font-weight:600;color:var(--muted);cursor:pointer;font-family:inherit;">
        Sign out
      </button>
    </form>
  </div>

  <div class="acc-tabs">
    <button class="acc-tab on" onclick="switchTab('quotes', this)">
      Quotes <span style="background:var(--bg2);color:var(--navy);padding:1px 8px;border-radius:20px;font-size:11px;margin-left:4px;">{{ $quotes->count() }}</span>
    </button>
    <button class="acc-tab" onclick="switchTab('wishlist', this)">
      Saved items <span style="background:var(--bg2);color:var(--navy);padding:1px 8px;border-radius:20px;font-size:11px;margin-left:4px;">{{ $wishlist->count() }}</span>
    </button>
  </div>

  {{-- QUOTES PANEL --}}
  <div class="acc-panel on" id="panel-quotes">
    @forelse($quotes as $q)
      <div class="q-row">
        <div>
          <div class="q-num">{{ $q->quote_number }}</div>
          <div class="q-date">{{ $q->created_at->format('d M Y') }}</div>
        </div>
        <div class="q-items">
          {{ $q->items->count() }} item{{ $q->items->count() == 1 ? '' : 's' }}
          @if($q->items->count())
            — {{ $q->items->first()->product_name }}{{ $q->items->count() > 1 ? ' +'.($q->items->count()-1).' more' : '' }}
          @endif
        </div>
        <span class="q-badge {{ $q->status }}">{{ \App\Models\Quote::statusLabel($q->status) }}</span>
        @if($q->estimated_total)
          <div style="font-family:var(--font-head);font-size:17px;font-weight:800;color:var(--navy);white-space:nowrap;">{{ $q->estimated_total_inr }}</div>
        @else
          <div style="font-size:12px;color:var(--muted);">Pending pricing</div>
        @endif
      </div>
    @empty
      <div style="text-align:center;padding:48px 20px;color:var(--muted);">
        <div style="font-size:36px;margin-bottom:12px;">📋</div>
        <div style="font-weight:600;margin-bottom:6px;">No quotes yet</div>
        <div style="font-size:13px;">Add products to your quote cart and submit a request.</div>
        <a href="{{ route('shop') }}" style="display:inline-block;margin-top:16px;padding:10px 22px;background:var(--navy);color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">Browse products →</a>
      </div>
    @endforelse
  </div>

  {{-- WISHLIST PANEL --}}
  <div class="acc-panel" id="panel-wishlist">
    @forelse($wishlist as $product)
      <div class="wl-grid" style="display:grid;">
    @endforelse
    @if($wishlist->isEmpty())
      <div style="text-align:center;padding:48px 20px;color:var(--muted);">
        <div style="font-size:36px;margin-bottom:12px;">♡</div>
        <div style="font-weight:600;margin-bottom:6px;">No saved items</div>
        <div style="font-size:13px;">Hit the heart on any product to save it here.</div>
        <a href="{{ route('shop') }}" style="display:inline-block;margin-top:16px;padding:10px 22px;background:var(--navy);color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;">Browse products →</a>
      </div>
    @else
      <div class="wl-grid">
        @foreach($wishlist as $product)
          <div class="wl-card" id="wl-{{ $product->id }}">
            <a href="{{ route('product', $product->slug) }}" style="text-decoration:none;color:inherit;">
              <div class="wl-img">
                @if($product->thumb)
                  <img src="{{ $product->thumb }}" alt="{{ $product->name }}">
                @endif
              </div>
              <div class="wl-body">
                <div class="wl-brand">{{ $product->brand?->name }}</div>
                <div class="wl-name">{{ $product->name }}</div>
                <div class="wl-price">{{ $product->price_inr }}</div>
              </div>
            </a>
            <button class="wl-rm" title="Remove" onclick="removeWishlist({{ $product->id }}, this)">✕</button>
          </div>
        @endforeach
      </div>
    @endif
  </div>

</div>

<script>
function switchTab(name, btn) {
  document.querySelectorAll('.acc-tab').forEach(t => t.classList.remove('on'));
  document.querySelectorAll('.acc-panel').forEach(p => p.classList.remove('on'));
  btn.classList.add('on');
  document.getElementById('panel-' + name).classList.add('on');
}

function removeWishlist(productId, btn) {
  fetch('{{ route('wishlist.toggle') }}', {
    method: 'POST',
    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content},
    body: JSON.stringify({product_id: productId})
  }).then(r => r.json()).then(() => {
    document.getElementById('wl-' + productId)?.remove();
  });
}
</script>
@endsection
