@php
$_siteName   = \App\Models\Setting::get('site_name', 'pcfy.in');
$_seoTitle   = \App\Models\Setting::get('seo_title', $_siteName.' — Better computers, half the price');
$_seoDesc    = \App\Models\Setting::get('seo_description', "India's trusted marketplace for certified refurbished laptops, desktops, servers & spare parts.");
$_ogImage    = \App\Models\Setting::get('seo_og_image', '');
$_phone      = \App\Models\Setting::get('contact_phone', '+91 98765 43210');
$_email      = \App\Models\Setting::get('contact_email', 'hello@pcfy.in');
$_address    = \App\Models\Setting::get('contact_address', 'Vadodara, Gujarat, India');
$_waNumber   = \App\Models\Setting::get('whatsapp_number', '919876543210');
$_gaId       = \App\Models\Setting::get('ga_id', '');
$_gtmId      = \App\Models\Setting::get('gtm_id', '');
$_annEnabled = \App\Models\Setting::get('announcement_enabled', '1') === '1';
$_annItems   = json_decode(\App\Models\Setting::get('announcement_items', '[]'), true) ?? [];
$_footerCopy = \App\Models\Setting::get('footer_copyright', '© '.date('Y').' pcfy.in — All rights reserved.');
$_ftCol1     = \App\Models\Setting::get('footer_col1_title', 'Quick links');
$_ftCol2     = \App\Models\Setting::get('footer_col2_title', 'Company');
$_ftCol3     = \App\Models\Setting::get('footer_col3_title', 'Support');
$_cartCount  = collect(session('cart', []))->sum('qty');
@endphp
<!DOCTYPE html>
<html lang="en" class="accent-amber density-regular">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Primary meta --}}
<title>@yield('title', $_seoTitle)</title>
<meta name="description" content="@yield('meta_description', $_seoDesc)">
@hasSection('meta_robots')@yield('meta_robots')@else<meta name="robots" content="index, follow">@endif

{{-- Open Graph --}}
<meta property="og:type"        content="website">
<meta property="og:title"       content="@yield('title', $_seoTitle)">
<meta property="og:description" content="@yield('meta_description', $_seoDesc)">
<meta property="og:url"         content="{{ url()->current() }}">
@hasSection('og_image')<meta property="og:image" content="@yield('og_image')">@elseif($_ogImage)<meta property="og:image" content="{{ $_ogImage }}">@endif
<meta property="og:site_name"   content="{{ $_siteName }}">

{{-- Twitter card --}}
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="@yield('title', $_seoTitle)">
<meta name="twitter:description" content="@yield('meta_description', $_seoDesc)">
@hasSection('og_image')<meta name="twitter:image" content="@yield('og_image')">@elseif($_ogImage)<meta name="twitter:image" content="{{ $_ogImage }}">@endif

{{-- Canonical --}}
<link rel="canonical" href="{{ url()->current() }}">

@yield('meta_extra')

{{-- Google Tag Manager --}}
@if($_gtmId)
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $_gtmId }}');</script>
@endif

{{-- Google Analytics --}}
@if($_gaId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $_gaId }}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $_gaId }}');</script>
@endif

@yield('schema_json')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500;600;700;800&family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/base.css') }}">
<style>
/* ── Live search dropdown ────────────────────────────── */
.search-wrap{position:relative;}
.search-dropdown{display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;
  background:var(--bg,#fff);border:1.5px solid var(--border,#e2e8f0);border-radius:14px;
  box-shadow:0 8px 32px rgba(0,0,0,.12);z-index:310;overflow:hidden;
  max-height:420px;overflow-y:auto;}
.search-dropdown.show{display:block;}
.sd-item{display:flex;align-items:center;gap:12px;padding:9px 14px;text-decoration:none;
  color:var(--text,#333);border-bottom:1px solid var(--border,#e2e8f0);transition:background .13s;}
.sd-item:last-of-type{border-bottom:none;}
.sd-item:hover,.sd-item.focused{background:var(--bg2,#f5f7fa);}
.sd-img{width:50px;height:38px;object-fit:contain;border-radius:7px;flex-shrink:0;background:#fff;padding:3px;border:1px solid var(--border,#e2e8f0);}
.sd-ph{background:var(--bg2,#f5f7fa);}
.sd-info{flex:1;min-width:0;}
.sd-name{font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--navy,#1a1a2e);}
.sd-meta{font-size:11px;color:var(--text-muted,#888);margin-top:2px;}
.sd-all{display:block;padding:9px 14px;text-align:center;font-size:12px;font-weight:600;
  color:var(--navy,#1a1a2e);background:var(--bg2,#f5f7fa);text-decoration:none;transition:background .13s;}
.sd-all:hover{background:var(--border,#e2e8f0);}
/* ── Global toast ────────────────────────────────────── */
.toast{position:fixed;bottom:90px;left:50%;transform:translateX(-50%) translateY(20px);
  width:max-content;max-width:80%;background:var(--navy-xdark,#0d0d20);color:#fff;
  font-size:13px;font-weight:600;padding:12px 20px;border-radius:30px;z-index:500;
  opacity:0;transition:all .3s;display:flex;align-items:center;gap:9px;
  box-shadow:0 4px 24px rgba(0,0,0,.25);pointer-events:none;}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0);}
.toast svg{width:16px;height:16px;stroke:#4ade80;fill:none;stroke-width:2.4;}
</style>
@yield('page_css')
@stack('styles')
</head>
<body data-screen-label="@yield('screen_label', 'Site')">

{{-- GTM noscript --}}
@if($_gtmId)
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $_gtmId }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

<!-- TOPBAR -->
@if($_annEnabled)
<div class="topbar">
  <div class="topbar-inner">
    <div class="tb-left">
      <span class="tb-pill">Certified Refurbished</span>
      @if($_annItems)
        @foreach($_annItems as $_item)<span>{{ $_item }}</span>@endforeach
      @else
        <span>🚚 Free delivery above ₹999</span>
        <span>📞 {{ $_phone }}</span>
        <span>🛡 6-month warranty on every product</span>
      @endif
    </div>
    <div class="tb-right">
      <a href="#">Track Order</a>
      <a href="#">Sell to Us</a>
      <a href="#">B2B / Bulk</a>
      <a href="{{ route('contact') }}">Contact</a>
    </div>
  </div>
</div>
@endif

<!-- HEADER -->
<div class="header">
  <div class="header-inner">
    <a href="{{ url('/') }}" class="logo-wrap">
      <div class="logo-svg-wrap">
        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 100 100">
          <rect x="2" y="2" width="96" height="96" rx="16" fill="none" stroke="var(--navy)" stroke-width="5"/>
          <rect x="20" y="30" width="38" height="24" rx="3" fill="var(--accent)"/>
          <rect x="34" y="58" width="10" height="4" rx="2" fill="var(--accent)"/>
          <rect x="63" y="26" width="14" height="44" rx="2" fill="var(--navy)"/>
          <rect x="66" y="32" width="8" height="2" fill="white"/><rect x="66" y="38" width="8" height="2" fill="white"/><rect x="66" y="44" width="8" height="2" fill="white"/>
          <rect x="20" y="70" width="38" height="6" rx="3" fill="var(--accent)"/>
          <rect x="4" y="28" width="6" height="4" fill="var(--accent)"/><rect x="4" y="52" width="6" height="4" fill="var(--accent)"/>
          <rect x="90" y="28" width="6" height="4" fill="var(--accent)"/><rect x="90" y="52" width="6" height="4" fill="var(--accent)"/>
        </svg>
      </div>
      <div>
        <div class="logo-text">pcfy<span>.in</span></div>
        <div class="logo-tag">Everything your system needs</div>
      </div>
    </a>

    <div class="search-wrap">
      <form action="{{ route('shop') }}" method="GET" class="search-bar">
        <select name="category_id" aria-label="Category">
          <option value="">All</option>
          @foreach($siteCategories ?? [] as $cat)
            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
        <input type="text" name="q" id="searchInput" value="{{ request('q') }}" placeholder="Search laptops, server parts, RAM, HDD…" autocomplete="off">
        <button type="submit">Search</button>
      </form>
      <div class="search-dropdown" id="searchDropdown"></div>
    </div>

    <div class="header-actions">
      <div class="hdr-search-btn" id="mobileSearchToggle">
        <svg viewBox="0 0 24 24" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4-4"/></svg>
      </div>
      <button class="mob-menu-btn" id="mnavOpen" aria-label="Open menu">
        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
      </button>
      @auth
        @if(!auth()->user()->isAdmin())
          <a href="{{ route('account.dashboard') }}" class="btn-account">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            <span>Account</span>
          </a>
        @endif
      @else
        <a href="{{ route('account.login') }}" class="btn-account">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          <span>Sign in</span>
        </a>
      @endauth
      <a href="{{ route('quote.cart') }}" class="btn-cart" id="cartBtn">
        🛒 Quote ({{ $_cartCount }})
      </a>
    </div>
  </div>
</div>

<!-- NAV -->
<div class="nav">
  <div class="nav-inner">
    <a class="nav-item {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
    @foreach($siteCategories ?? [] as $cat)
      <a class="nav-item {{ request()->route('slug') == $cat->slug ? 'active' : '' }}"
         href="{{ route('shop.category', $cat->slug) }}">{{ $cat->name }}</a>
    @endforeach
    <a class="nav-item" href="{{ route('shop') }}">All Products</a>
    <a class="nav-item {{ request()->routeIs('blog*') ? 'active' : '' }}" href="{{ route('blog') }}">Blog</a>
    <a class="nav-item deals" href="{{ route('shop', ['featured' => 1]) }}">🔥 Hot Deals</a>
  </div>
</div>

<!-- CONTENT -->
@yield('content')

<!-- FOOTER -->
<div class="footer">
  <div class="footer-grid">
    <div>
      <div class="footer-logo">pcfy<span>.in</span></div>
      <p class="footer-desc">India's trusted marketplace for certified refurbished laptops, desktops, servers, workstations & spare parts. Every product tested, graded & warranty-backed.</p>
      <div class="footer-badges">
        <span class="f-badge">ISO Certified</span>
        <span class="f-badge">QC Certified</span>
        <span class="f-badge eco">♻ Eco-friendly</span>
        <span class="f-badge">E-Waste Certified</span>
      </div>
      <div class="footer-contact">
        <div class="fc">📞 {{ $_phone }}</div>
        <div class="fc">✉ {{ $_email }}</div>
        <div class="fc">📍 {{ $_address }}</div>
      </div>
    </div>
    <div>
      <div class="footer-col-title">Products</div>
      @foreach($siteCategories ?? [] as $cat)
        <a class="footer-link" href="{{ route('shop.category', $cat->slug) }}">{{ $cat->name }}</a>
      @endforeach
    </div>
    <div>
      <div class="footer-col-title">{{ $_ftCol2 }}</div>
      <a class="footer-link" href="{{ route('page', 'about') }}">About Us</a>
      <a class="footer-link" href="{{ route('contact') }}">Contact</a>
      <a class="footer-link" href="{{ route('blog') }}">Blog</a>
      <a class="footer-link" href="{{ route('page', 'warranty') }}">Warranty Policy</a>
      <a class="footer-link" href="{{ route('page', 'returns') }}">Return Policy</a>
    </div>
    <div>
      <div class="footer-col-title">{{ $_ftCol3 }}</div>
      <a class="footer-link" href="{{ route('page', 'privacy') }}">Privacy Policy</a>
      <a class="footer-link" href="{{ route('page', 'terms') }}">Terms of Use</a>
      <a class="footer-link" href="{{ route('quote.cart') }}">Get a Quote</a>
      <a class="footer-link" href="https://wa.me/{{ $_waNumber }}" target="_blank" rel="noopener">WhatsApp Us</a>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="footer-bottom-inner">
      <div>{{ $_footerCopy }}</div>
      <div class="pay-icons">
        <span class="pay-icon">UPI</span><span class="pay-icon">VISA</span>
        <span class="pay-icon">RuPay</span><span class="pay-icon">EMI</span><span class="pay-icon">COD</span>
      </div>
    </div>
  </div>
</div>

<!-- MOBILE NAV DRAWER -->
<div class="mnav-backdrop" id="mnavBackdrop"></div>
<div class="mnav" id="mnav">
  <div class="mnav-head">
    <div class="mnav-logo">pcfy<span>.in</span></div>
    <button class="mnav-close" id="mnavClose">
      <svg viewBox="0 0 24 24" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
    </button>
  </div>
  <div class="mnav-body">
    <div class="mnav-section-label">Browse</div>
    <a class="mnav-item {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a>
    @foreach($siteCategories ?? [] as $cat)
      <a class="mnav-item" href="{{ route('shop.category', $cat->slug) }}">{{ $cat->name }}</a>
    @endforeach
    <a class="mnav-item" href="{{ route('shop') }}">All Products</a>
    <a class="mnav-item" href="{{ route('blog') }}">Blog</a>
    <a class="mnav-item deals" href="{{ route('shop', ['featured' => 1]) }}">🔥 Hot Deals</a>
  </div>
  <div class="mnav-foot">
    <div class="mnav-cta-row">
      <button class="mnav-cta quote">📋 Bulk Quote</button>
      <a href="{{ route('quote.cart') }}" class="mnav-cta cart" style="text-decoration:none;">🛒 Cart ({{ $_cartCount }})</a>
    </div>
    <div class="mnav-contact">📞 {{ $_phone }}<br>✉ {{ $_email }}</div>
  </div>
</div>

<div class="toast" id="toast">
  <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
  <span id="toastMsg">Added to quote cart</span>
</div>

<script>
(function(){
  var _csrf = (document.querySelector('meta[name="csrf-token"]')||{}).content||'';
  var _toastEl  = document.getElementById('toast');
  var _toastMsg = document.getElementById('toastMsg');
  var _toastT;

  window.showToast = function(msg){
    if(!_toastEl) return;
    _toastMsg.textContent = msg;
    _toastEl.classList.add('show');
    clearTimeout(_toastT);
    _toastT = setTimeout(function(){ _toastEl.classList.remove('show'); }, 3500);
  };

  window.updateCartCount = function(n){
    var b = document.getElementById('cartBtn');
    if(b) b.textContent = '🛒 Quote ('+n+')';
    var m = document.querySelector('.mnav-cta.cart');
    if(m) m.textContent = '🛒 Cart ('+n+')';
  };

  window.pcfyAddToCart = function(form, redirectUrl){
    if(form._submitting) return;
    form._submitting = true;
    var params = new URLSearchParams();
    new FormData(form).forEach(function(v,k){ params.append(k,v); });
    fetch(form.action,{
      method:'POST',
      credentials:'same-origin',
      headers:{'Accept':'application/json','X-CSRF-TOKEN':_csrf,'Content-Type':'application/x-www-form-urlencoded'},
      body: params
    })
    .then(function(r){ return r.json(); })
    .then(function(json){
      form._submitting = false;
      if(!json.ok) return;
      window.updateCartCount(json.count);
      if(redirectUrl){ location.href = redirectUrl; }
      else { window.showToast(json.msg); }
    })
    .catch(function(){ form._submitting=false; form.submit(); });
  };

  // AJAX cart — forms with .ajax-cart-form
  document.addEventListener('submit', function(e){
    if(!e.target.classList.contains('ajax-cart-form')) return;
    e.preventDefault();
    window.pcfyAddToCart(e.target, null);
  });

  // Mobile nav
  var _btn=document.getElementById('mnavOpen'),_nav=document.getElementById('mnav'),
      _bd=document.getElementById('mnavBackdrop'),_cl=document.getElementById('mnavClose');
  function _openNav(){ _nav.classList.add('show'); _bd.classList.add('show'); document.body.style.overflow='hidden'; }
  function _closeNav(){ _nav.classList.remove('show'); _bd.classList.remove('show'); document.body.style.overflow=''; }
  if(_btn) _btn.addEventListener('click',_openNav);
  if(_cl)  _cl.addEventListener('click',_closeNav);
  if(_bd)  _bd.addEventListener('click',_closeNav);

  // Live search
  var _si = document.getElementById('searchInput');
  var _sd = document.getElementById('searchDropdown');
  if(!_si || !_sd) return;

  var _sugsUrl = '{{ route("search.suggestions") }}';
  var _shopUrl = '{{ route("shop") }}';
  var _debT;

  _si.addEventListener('input', function(){
    clearTimeout(_debT);
    var q = this.value.trim();
    if(q.length < 2){ _hide(); return; }
    _debT = setTimeout(function(){ _fetch(q); }, 300);
  });

  _si.addEventListener('keydown', function(e){
    var items = _sd.querySelectorAll('[data-url]');
    var idx = Array.from(items).findIndex(function(el){ return el.classList.contains('focused'); });
    if(e.key==='ArrowDown'){
      e.preventDefault();
      if(idx < items.length-1){ if(idx>=0) items[idx].classList.remove('focused'); items[idx+1].classList.add('focused'); }
    } else if(e.key==='ArrowUp'){
      e.preventDefault();
      if(idx > 0){ items[idx].classList.remove('focused'); items[idx-1].classList.add('focused'); }
    } else if(e.key==='Enter'){
      var f = _sd.querySelector('[data-url].focused');
      if(f){ e.preventDefault(); location.href = f.dataset.url; }
    } else if(e.key==='Escape'){
      _hide();
    }
  });

  document.addEventListener('click', function(e){
    if(!_sd.contains(e.target) && e.target !== _si) _hide();
  });

  function _fetch(q){
    fetch(_sugsUrl+'?q='+encodeURIComponent(q))
      .then(function(r){ return r.json(); })
      .then(function(data){ _render(data,q); })
      .catch(function(){ _hide(); });
  }

  function _render(items, q){
    if(!items.length){ _hide(); return; }
    var html='';
    for(var i=0;i<items.length;i++){
      var p=items[i];
      var img = p.thumb
        ? '<img src="'+p.thumb+'" alt="" class="sd-img" loading="lazy">'
        : '<div class="sd-img sd-ph"></div>';
      var surl = _esc(p.url);
      html += '<a class="sd-item" href="'+surl+'" data-url="'+surl+'">'
        +img+'<div class="sd-info">'
        +'<div class="sd-name">'+_esc(p.name)+'</div>'
        +'<div class="sd-meta">'+(p.brand?_esc(p.brand)+' · ':'')+' Grade '+_esc(p.grade)+' · '+_esc(p.price)+'</div>'
        +'</div></a>';
    }
    var seeUrl = _shopUrl+'?q='+encodeURIComponent(q);
    html += '<a class="sd-all" href="'+_esc(seeUrl)+'" data-url="'+_esc(seeUrl)+'">See all results for "'+_esc(q)+'" →</a>';
    _sd.innerHTML = html;
    _sd.classList.add('show');
  }

  function _hide(){
    _sd.classList.remove('show');
    _sd.innerHTML='';
  }

  function _esc(s){
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
  // Wishlist toggle
  var _wishUrl = '{{ route("wishlist.toggle") }}';
  var _wishLogin = '{{ route("account.login") }}';
  var _wishIds = @auth @if(!auth()->user()->isAdmin()) [{{ auth()->user()->wishlistItems->pluck('product_id')->join(',') }}] @else [] @endif @else [] @endauth;

  document.addEventListener('click', function(e){
    var btn = e.target.closest('.wish-btn');
    if(!btn) return;
    e.stopPropagation();
    var card = btn.closest('[data-product-id]') || btn.closest('.prod-card');
    var pid = btn.dataset.productId || (card && card.dataset.productId);
    if(!pid){
      var inp = card && card.querySelector('input[name="product_id"]');
      pid = inp && inp.value;
    }
    if(!pid){ window.location.href = _wishLogin; return; }
    fetch(_wishUrl, {
      method:'POST', credentials:'same-origin',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':_csrf},
      body: JSON.stringify({product_id: parseInt(pid)})
    })
    .then(function(r){
      if(r.status === 401){ window.location.href = _wishLogin; return null; }
      return r.json();
    })
    .then(function(data){
      if(!data) return;
      if(data.saved){ btn.textContent='♥'; btn.style.color='#e11d48'; window.showToast('Saved to wishlist'); }
      else           { btn.textContent='♡'; btn.style.color=''; window.showToast('Removed from wishlist'); }
    });
  });

  // Mark already-saved items on load
  if(_wishIds.length){
    _wishIds.forEach(function(pid){
      document.querySelectorAll('.wish-btn').forEach(function(btn){
        var card = btn.closest('[data-product-id]') || btn.closest('.prod-card');
        var inp  = card && card.querySelector('input[name="product_id"]');
        var id   = inp && parseInt(inp.value);
        if(id === pid){ btn.textContent='♥'; btn.style.color='#e11d48'; }
      });
    });
  }

})();
</script>
@stack('scripts')
</body>
</html>
