@extends('layouts.site')
@section('title', $product->getSeoTitle($product->name).' — pcfy.in')
@section('meta_description', $product->getSeoDescription($product->short_description ?? ''))
@section('og_image', $product->getOgImage())
@section('screen_label', 'Product Detail')
@if($product->isNoindex())@section('meta_robots', '<meta name="robots" content="noindex">')@endif
@section('schema_json')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Product",
  "name": "{{ addslashes($product->name) }}",
  "description": "{{ addslashes($product->short_description ?? '') }}",
  "brand": { "@@type": "Brand", "name": "{{ addslashes($product->brand?->name ?? 'pcfy.in') }}" },
  "offers": {
    "@@type": "Offer",
    "priceCurrency": "INR",
    "price": "{{ $product->price / 100 }}",
    "availability": "{{ $product->stock_status === 'in_stock' ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}"
  }
}
</script>
@endsection
@section('page_css')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endsection

@section('content')

<!-- BREADCRUMB -->
<div class="crumb">
  <a href="{{ url('/') }}">Home</a>
  <span class="sep">›</span>
  @if($product->category)
    <a href="{{ route('shop.category', $product->category->slug) }}">{{ $product->category->name }}</a>
    <span class="sep">›</span>
  @endif
  @if($product->brand)
    <a href="{{ route('shop', ['brand_id' => $product->brand_id]) }}">{{ $product->brand->name }}</a>
    <span class="sep">›</span>
  @endif
  <span class="cur">{{ $product->name }}</span>
</div>

<!-- PDP TOP -->
<div class="pdp">
  <div class="pdp-grid">

    <!-- GALLERY -->
    <div class="gallery">
      @php $images = $product->getMedia('images'); @endphp
      <div class="stage">
        @if($images->count())
          <img src="{{ $images->first()->getUrl('medium') }}" alt="{{ $product->name }}" id="mainImg"
               style="width:100%;height:100%;object-fit:contain;">
        @else
          <div class="ph">
            <span class="ph-label">{{ strtolower($product->brand?->name ?? 'pcfy') }} · product</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="13" rx="2"/><path d="M1 20h22"/></svg>
          </div>
        @endif
        <span class="stage-stamp gs-{{ strtolower($product->grade) }}">GRADE {{ $product->grade }}</span>
        <button class="stage-fav" id="favBtn" onclick="this.classList.toggle('on')">
          <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
        </button>
      </div>

      @if($images->count() > 1)
      <div class="thumbs">
        @foreach($images as $i => $img)
        <div class="thumb {{ $i===0 ? 'on' : '' }}" onclick="switchImg('{{ $img->getUrl('medium') }}', this)">
          <img src="{{ $img->getUrl('thumb') }}" alt="Image {{ $i+1 }}" style="width:100%;height:100%;object-fit:contain;padding:4px;background:#fff;">
        </div>
        @endforeach
      </div>
      @endif
    </div>

    <!-- BUY PANEL -->
    <div class="buy">
      <div class="b-brand">
        @if($product->brand)<span class="b">{{ $product->brand->name }}</span>@endif
        <span class="cert">
          <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
          Certified refurbished
        </span>
      </div>

      <div class="b-name">{{ $product->name }}</div>

      <div class="b-rate">
        <div class="stars">
          @for($i = 1; $i <= 5; $i++)
            <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" {{ $i > 4 ? 'style="fill:var(--bg2)"' : '' }}/></svg>
          @endfor
        </div>
        <span class="num">4.6</span>
        <span class="cnt">312 reviews</span>
      </div>

      <div class="b-price">
        <div class="b-now">{{ $product->price_inr }}</div>
        @if($product->mrp)
          <div class="b-mrp">{{ $product->mrp_inr }}</div>
          <div class="b-off">{{ $product->discount_percent }}% off</div>
        @endif
      </div>
      <div class="b-gst">
        <svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Inclusive of all taxes · GST invoice provided
      </div>

      <div class="divide"></div>

      <!-- Quick specs -->
      @if($product->quick_specs)
      <div class="blk-label">Key specs</div>
      <div class="prod-chips" style="margin-bottom:18px;">
        @foreach((array)$product->quick_specs as $chip)
          <span class="prod-chip">{{ $chip }}</span>
        @endforeach
      </div>
      @endif

      <!-- Grade info -->
      <div class="blk-label">Condition grade</div>
      <div class="grade-opts" style="margin-bottom:18px;">
        @php
          $grades = [
            'A' => ['Like new', 'Minimal signs of use — faint marks only under bright light.'],
            'B' => ['Good condition', 'Light cosmetic marks, fully functional — same performance as A.'],
            'C' => ['Functional', 'Noticeable wear but 100% working. Great for bulk / IT.'],
          ];
          $gLabel = $grades[$product->grade] ?? ['Grade '.$product->grade, ''];
        @endphp
        <div class="gopt sel {{ strtolower($product->grade) }}">
          <div class="gopt-l">{{ $product->grade }}</div>
          <div>
            <div class="gopt-t">{{ $gLabel[0] }}
              @if($product->grade === 'B')<span class="pop">Best value</span>@endif
            </div>
            <div class="gopt-d">{{ $gLabel[1] }}</div>
          </div>
          <div class="gopt-p">
            <div class="p">{{ $product->price_inr }}</div>
            @if($product->mrp)<div class="s">{{ $product->mrp_inr }}</div>@endif
          </div>
        </div>
      </div>

      <!-- Stock status -->
      <div class="blk-label">
        Availability
        <span style="font-size:12px;font-weight:600;text-transform:none;
          color: {{ $product->stock_status === 'in_stock' ? 'var(--green)' : ($product->stock_status === 'low_stock' ? 'var(--accent-dark)' : 'var(--red)') }};">
          {{ match($product->stock_status) {
            'in_stock'     => '✓ In stock',
            'low_stock'    => '⚡ Low stock',
            'out_of_stock' => '✗ Out of stock',
            default        => $product->stock_status,
          } }}
        </span>
      </div>

      <!-- Add to quote -->
      @if($product->stock_status !== 'out_of_stock')
      <form action="{{ route('cart.add') }}" method="POST" id="buyForm">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="grade" value="{{ $product->grade }}">
        <div style="display:flex;align-items:center;gap:10px;margin:14px 0;">
          <span style="font-size:13px;font-weight:600;">Qty:</span>
          <input type="number" name="qty" value="1" min="1" max="999"
                 style="width:70px;height:38px;text-align:center;border:1.5px solid var(--border);border-radius:8px;font-size:14px;font-family:inherit;outline:none;">
        </div>
        <div class="actions">
          <button type="submit" class="act-cart">
            <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.7a2 2 0 002-1.6L23 6H6"/></svg>
            Add to quote
          </button>
          <button type="button" class="act-buy">
            Request quote →
          </button>
        </div>
      </form>
      @else
        <div class="actions" style="margin-top:14px;">
          <button class="act-cart" disabled style="opacity:.5;cursor:not-allowed;">Out of stock</button>
        </div>
      @endif

      <!-- Trust row -->
      <div class="trustrow">
        <div class="tr-it">
          <div class="tr-ic"><svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
          <div class="tr-t">30-point QC tested</div>
        </div>
        <div class="tr-it">
          <div class="tr-ic"><svg viewBox="0 0 24 24"><path d="M12 2l8 4v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6z"/></svg></div>
          <div class="tr-t">{{ $product->warranty_months }}mo warranty</div>
        </div>
        <div class="tr-it">
          <div class="tr-ic"><svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
          <div class="tr-t">GST invoice</div>
        </div>
        <div class="tr-it">
          <div class="tr-ic"><svg viewBox="0 0 24 24"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg></div>
          <div class="tr-t">Pan-India delivery</div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- DETAIL SECTIONS -->
<div class="detail">

  <!-- Description + Specs -->
  <div class="two-col">

    @if($product->description)
    <div class="dsec">
      <div class="sh">About <span>this product</span></div>
      <div class="sh-sub">{{ $product->short_description }}</div>
      <div style="font-size:14px;line-height:1.8;color:var(--text);">{!! $product->description !!}</div>
    </div>
    @endif

    @if($specGroups->count())
    <div class="dsec">
      <div class="sh">Full <span>specifications</span></div>
      <div class="sh-sub" style="margin-bottom:12px;">All specs verified by our QC team.</div>
      @foreach($specGroups as $group => $specs)
      <div class="spec-group open">
        <div class="spec-head">
          <span class="ttl">
            <svg viewBox="0 0 24 24"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2v-4M9 21H5a2 2 0 01-2-2v-4m0 0h18"/></svg>
            {{ $group !== 'General' ? $group : 'Specifications' }}
          </span>
          <svg class="chev" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
        </div>
        <div class="spec-body">
          @foreach($specs as $spec)
          <div class="spec-row">
            <div class="k">{{ $spec->label }}</div>
            <div class="v">{{ $spec->value }}</div>
          </div>
          @endforeach
        </div>
      </div>
      @endforeach
    </div>
    @endif

  </div>

</div>

<!-- RELATED PRODUCTS -->
@if($related->count())
<div class="detail" style="padding-top:0;">
  <div class="dsec">
    <div class="sh">You might also <span>like</span></div>
    @if($product->category)
    <div class="sh-sub"><a href="{{ route('shop.category', $product->category->slug) }}" style="color:var(--navy);">See all {{ $product->category->name }} →</a></div>
    @endif
    <div class="rel-grid">
      @foreach($related as $rel)
        <x-site.product-card :product="$rel" />
      @endforeach
    </div>
  </div>
</div>
@endif

<!-- STICKY MOBILE BUY BAR -->
<div class="buybar">
  <div class="bb-price">
    <span class="now">{{ $product->price_inr }}</span>
    @if($product->discount_percent > 0)
      <span class="off">{{ $product->discount_percent }}% off · Grade {{ $product->grade }}</span>
    @endif
  </div>
  <div class="bb-cart">
    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.7a2 2 0 002-1.6L23 6H6"/></svg>
  </div>
  <form action="{{ route('cart.add') }}" method="POST" class="ajax-cart-form" style="flex:1;">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="grade" value="{{ $product->grade }}">
    <button type="submit" class="bb-buy">Add to quote</button>
  </form>
</div>

@endsection

@push('scripts')
<script>
function switchImg(src, thumb) {
  document.getElementById('mainImg').src = src;
  document.querySelectorAll('.thumb').forEach(t => t.classList.remove('on'));
  thumb.classList.add('on');
}

document.querySelectorAll('.spec-head').forEach(h => {
  h.addEventListener('click', () => h.closest('.spec-group').classList.toggle('open'));
});

@if(session('cart_added'))
  showToast("{{ session('cart_added') }}");
@endif

// Buy-panel AJAX (Add to quote + Request quote →)
var _buyForm = document.getElementById('buyForm');
if(_buyForm){
  _buyForm.addEventListener('submit', function(e){
    e.preventDefault();
    window.pcfyAddToCart(this, null);
  });
  var _reqBtn = _buyForm.querySelector('.act-buy');
  if(_reqBtn){
    _reqBtn.addEventListener('click', function(){
      window.pcfyAddToCart(_buyForm, '{{ route("quote.cart") }}');
    });
  }
}
</script>
<script src="{{ asset('js/product.js') }}"></script>
@endpush
