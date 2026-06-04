@extends('layouts.site')
@section('title', 'pcfy.in — Better computers, half the price')
@section('screen_label', 'Homepage')
@section('page_css')
<link rel="stylesheet" href="{{ asset('css/home-b.css') }}">
@endsection

@section('content')

{{-- ══════════════════════════════════════
     1. HERO
══════════════════════════════════════ --}}
<div class="heroB">
  <div class="heroB-inner">

    <div>
      <div class="hb-eyebrow"><span class="dot"></span>Certified refurbished · 30-point tested</div>

      <h1 class="hb-h1">Better computers.<br><span class="u">Half the price.</span><br>Zero compromise.</h1>

      <p class="hb-lead">Laptops, desktops, servers, workstations, storage &amp; spare parts —
        <strong>professionally restored, honestly graded</strong> and backed by a real 6-month warranty.</p>

      {{-- Hero search bar --}}
      <form class="hb-search" action="{{ route('shop') }}" method="GET">
        <input class="hb-search-input" type="text" name="q"
               placeholder="Search laptops, desktops, Dell, 8GB, i5…"
               value="{{ request('q') }}" autocomplete="off">
        <button class="hb-search-btn" type="submit">
          <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4-4"/></svg>
          Search
        </button>
      </form>

      {{-- Category quick-links as pill buttons --}}
      <div class="hb-words">
        @foreach($siteCategories as $cat)
          <a class="hb-word" href="{{ route('shop.category', $cat->slug) }}">{{ $cat->name }}</a>
        @endforeach
      </div>

      {{-- CTAs — primary navy, secondary ghost --}}
      <div class="hb-cta">
        <a href="{{ route('shop') }}" class="btn btn-navy-solid">Shop all refurbished →</a>
        <a href="#grades" class="btn btn-outline" style="font-size:13px;padding:12px 20px;">How grading works</a>
      </div>

      {{-- Proof bar --}}
      <div class="hb-proof">
        <div class="hb-proof-item">
          <div class="n">{{ App\Models\Product::active()->count() }}+</div>
          <div class="l">Products in stock</div>
        </div>
        <div class="hb-proof-item"><div class="n">5,000+</div><div class="l">Businesses served</div></div>
        <div class="hb-proof-item"><div class="n">70%</div><div class="l">Average saving</div></div>
        <div class="hb-proof-item"><div class="n">6mo</div><div class="l">Warranty included</div></div>
      </div>
    </div>

    {{-- CONFIGURATOR (right column) --}}
    <div class="config">
      <div class="config-head">
        <div class="config-kicker">Not sure what you need?</div>
        <div class="config-title">Build your match</div>
      </div>
      <div class="config-body">
        <div class="cfg-step">
          <div class="cfg-label"><span class="num">1</span> What's it for?</div>
          <div class="cfg-opts" data-group="use">
            <div class="cfg-opt sel" data-val="office">Office</div>
            <div class="cfg-opt" data-val="study">Study</div>
            <div class="cfg-opt" data-val="creative">Creative</div>
            <div class="cfg-opt" data-val="server">Server</div>
          </div>
        </div>
        <div class="cfg-step">
          <div class="cfg-label"><span class="num">2</span> Form factor</div>
          <div class="cfg-opts" data-group="type">
            <div class="cfg-opt sel" data-val="laptop">Laptop</div>
            <div class="cfg-opt" data-val="desktop">Desktop</div>
            <div class="cfg-opt" data-val="workstation">Workstation</div>
          </div>
        </div>
        <div class="cfg-step">
          <div class="cfg-label"><span class="num">3</span> Budget</div>
          <div class="cfg-opts" data-group="budget">
            <div class="cfg-opt sel" data-val="low">&lt;₹15k</div>
            <div class="cfg-opt" data-val="mid">₹15–30k</div>
            <div class="cfg-opt" data-val="high">₹30k+</div>
          </div>
        </div>
        <div class="cfg-result">
          <div class="cfg-res-label">Recommended match</div>
          <div class="cfg-match" id="cfgMatch">
            <div class="cfg-match-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="4" width="20" height="13" rx="2"/><path d="M1 20h22"/>
              </svg>
            </div>
            <div>
              <div class="cfg-match-name" id="cfgName">Dell Latitude 5490</div>
              <div class="cfg-match-spec" id="cfgSpec">i5 8th Gen · 8GB · 256GB SSD · 14" FHD · Grade A</div>
            </div>
            <div class="cfg-match-price">
              <div class="p" id="cfgPrice">₹14,999</div>
              <div class="g">67% off MRP</div>
            </div>
          </div>
          <a href="{{ route('shop') }}" class="btn btn-accent cfg-go" id="cfgLink">
            Find matching laptops →
          </a>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- ══════════════════════════════════════
     2. TRUST STRIP (moved above Banner A)
══════════════════════════════════════ --}}
<div class="trust-strip">
  <div class="trust-inner">
    <a href="{{ route('shop') }}" class="ts-item-link" title="See our QC process">
      <div class="ts-icon"><svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div><div class="ts-title">30-point QC test</div><div class="ts-sub">Every unit individually checked</div></div>
    </a>
    <a href="{{ route('contact') }}" class="ts-item-link" title="Warranty details">
      <div class="ts-icon"><svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 4v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6z"/></svg></div>
      <div><div class="ts-title">6-month warranty</div><div class="ts-sub">In-house service team</div></div>
    </a>
    <a href="#grades" class="ts-item-link" title="How grading works">
      <div class="ts-icon"><svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg></div>
      <div><div class="ts-title">Grade certified</div><div class="ts-sub">A / B / C — fully transparent</div></div>
    </a>
    <div class="ts-item">
      <div class="ts-icon"><svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg></div>
      <div><div class="ts-title">Fast shipping</div><div class="ts-sub">Pan-India · dispatch in 24hrs</div></div>
    </div>
    <div class="ts-item">
      <div class="ts-icon"><svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
      <div><div class="ts-title">GST invoice</div><div class="ts-sub">Valid for business claims</div></div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════
     3. BANNER ZONE A
══════════════════════════════════════ --}}
<div class="banner-zone-a">
  <a href="{{ route('shop') }}" title="Shop all refurbished hardware">
    <img src="https://placehold.co/1400x280/1a3c6e/FBBF24?text=Banner+A+%E2%80%94+Replace+with+your+image+%281400+%C3%97+280px%29"
         alt="Promotional banner" width="1400" height="280">
  </a>
</div>

{{-- ══════════════════════════════════════
     4. CATEGORY INDEX
══════════════════════════════════════ --}}
<div class="section alt">
  <div class="container">
    <div class="sec-head">
      <div>
        <div class="sec-label">The full catalogue</div>
        <div class="sec-title">Everything we <span>stock</span></div>
        <div class="sec-sub">Tested, graded and warranted — every category, one standard.</div>
      </div>
      <a class="sec-link" href="{{ route('shop') }}">Browse all →</a>
    </div>
    <div class="idx">
      @forelse($categories as $i => $cat)
      <a class="idx-row" href="{{ route('shop.category', $cat->slug) }}">
        <div class="idx-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="idx-name">{{ $cat->name }}</div>
        <div class="idx-desc">{{ $cat->description ?: 'Certified, tested and warranted — ready to ship.' }}</div>
        <div class="idx-meta">
          <div class="idx-count">{{ $cat->total_products > 0 ? $cat->total_products . '+' : 'Coming soon' }} units</div>
          @if($cat->min_price_inr)
            <div class="idx-from">From {{ $cat->min_price_inr }}</div>
          @endif
        </div>
        <div class="idx-arrow">→</div>
      </a>
      @empty
      <div style="padding:40px;color:var(--muted);text-align:center;">No categories yet — add some in the admin panel.</div>
      @endforelse
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════
     5. FEATURED PRODUCTS (moved up)
══════════════════════════════════════ --}}
@if($featured->count())
<div class="section">
  <div class="container">
    <div class="sec-head">
      <div>
        <div class="sec-label">Hand-picked for you</div>
        <div class="sec-title">This week's <span>best deals</span></div>
        <div class="sec-sub">Graded, tested &amp; warranted — ready to ship today.</div>
      </div>
      <a class="sec-link" href="{{ route('shop') }}">See all {{ App\Models\Product::active()->count() }}+ deals →</a>
    </div>
    <div class="product-grid">
      @foreach($featured as $product)
        <x-site.product-card :product="$product" />
      @endforeach
    </div>
  </div>
</div>
@endif

{{-- ══════════════════════════════════════
     6. TESTIMONIALS (new — shows only if data exists)
══════════════════════════════════════ --}}
@if($testimonials->count())
<div class="section" style="background:var(--surface);border-top:1px solid var(--border);">
  <div class="container">
    <div class="sec-head">
      <div>
        <div class="sec-label">What our customers say</div>
        <div class="sec-title">Real <span>experiences</span></div>
        <div class="sec-sub">Businesses and professionals who made the switch to certified refurbished.</div>
      </div>
    </div>
    <div class="testi-grid">
      @php $avClasses = ['av-navy', 'av-green', 'av-amber']; @endphp
      @foreach($testimonials as $i => $t)
      <div class="testi-card">
        @if($t->is_verified)
          <span class="verified">✓ Verified buyer</span>
        @endif
        <div class="testi-stars">
          @for($s = 1; $s <= 5; $s++){{ $s <= $t->rating ? '★' : '☆' }}@endfor
        </div>
        <div class="testi-text">{{ $t->body }}</div>
        <div class="testi-author">
          <div class="testi-av {{ $avClasses[$i % 3] }}">{{ $t->initials }}</div>
          <div>
            <div class="testi-name">{{ $t->name }}</div>
            <div class="testi-role">{{ $t->role }}@if($t->city), {{ $t->city }}@endif</div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endif

{{-- ══════════════════════════════════════
     7. BANNER ZONE B
══════════════════════════════════════ --}}
<div class="banner-zone-b-wrap">
  <div class="banner-zone-b">
    <a href="{{ route('shop') }}" title="Shop deals">
      <img src="https://placehold.co/1200x220/0f172a/FBBF24?text=Banner+B+%E2%80%94+Replace+with+your+image+%281200+%C3%97+220px%29"
           alt="Promotional banner" width="1200" height="220">
    </a>
  </div>
</div>

{{-- ══════════════════════════════════════
     8. GRADE SCALE (with per-grade CTAs)
══════════════════════════════════════ --}}
<div class="gradescale" id="grades">
  <div class="gs-inner">
    <div class="gs-head">
      <div class="gs-tag">How grading works</div>
      <div class="gs-title">One honest scale. <span>No surprises.</span></div>
      <p class="gs-sub">Every device is inspected and assigned a grade based purely on cosmetic condition — performance is always fully tested and guaranteed.</p>
    </div>
    <div class="gs-track">
      <div class="gs-cell gs-a">
        <div class="gs-bar gs-bar-a"></div>
        <div class="gs-letter">A</div>
        <div class="gs-cell-title">Like new</div>
        <div class="gs-cell-cond">Excellent condition</div>
        <p class="gs-cell-desc">Minimal to no signs of use — faint marks only under bright light. All ports, keyboard, screen &amp; battery fully tested.</p>
        <div class="gs-cell-foot">Best for professionals &amp; students</div>
        <a href="{{ route('shop', ['grade' => 'A']) }}" class="gs-cell-cta">Shop Grade A →</a>
      </div>
      <div class="gs-cell gs-b">
        <div class="gs-bar gs-bar-b"></div>
        <div class="gs-letter">B</div>
        <div class="gs-cell-title">Good condition</div>
        <div class="gs-cell-cond">Light cosmetic marks</div>
        <p class="gs-cell-desc">Minor scratches on the body, screen &amp; keyboard in good shape. Fully functional — performance identical to Grade A.</p>
        <div class="gs-cell-foot">Best value · most popular grade</div>
        <a href="{{ route('shop', ['grade' => 'B']) }}" class="gs-cell-cta">Shop Grade B →</a>
      </div>
      <div class="gs-cell gs-c">
        <div class="gs-bar gs-bar-c"></div>
        <div class="gs-letter">C</div>
        <div class="gs-cell-title">Functional</div>
        <div class="gs-cell-cond">Visible character marks</div>
        <p class="gs-cell-desc">Visible wear marks — 100% functional and fully tested. Ideal for office workloads, server rooms or bulk IT deployments.</p>
        <div class="gs-cell-foot">Best for bulk &amp; IT deployments</div>
        <a href="{{ route('shop', ['grade' => 'C']) }}" class="gs-cell-cta">Shop Grade C →</a>
      </div>
    </div>
    <div class="gs-savebar">
      <div class="big">Up to 70% off</div>
      <div class="txt">vs buying new — <strong>same hardware, same performance, real warranty.</strong></div>
      <a href="{{ route('shop') }}" class="gs-savebar-cta">See today's deals →</a>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════
     9. B2B SECTION (moved before eco)
══════════════════════════════════════ --}}
<div class="b2b-section">
  <div class="b2b-inner">
    <div>
      <div class="b2b-tag">B2B &amp; bulk orders</div>
      <div class="b2b-title">Equip your <span>entire team</span> for less</div>
      <p class="b2b-desc">We supply schools, IT companies, startups and enterprises with bulk certified refurbished hardware. Dedicated account managers, GST billing &amp; custom warranty plans.</p>
      <div class="b2b-cta">
        <a href="{{ route('contact') }}" class="btn btn-navy">Request bulk quote (50+ units) →</a>
        <a href="{{ route('contact') }}" class="btn btn-ghost">Talk to sales</a>
      </div>
    </div>
    <div class="b2b-feats">
      <div class="b2b-feat">
        <div class="b2b-feat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8v8m-4-5v5m-4-2v2m-5 4h18a1 1 0 001-1V4a1 1 0 00-1-1H3a1 1 0 00-1 1v15a1 1 0 001 1z"/></svg></div>
        <div class="b2b-feat-title">Volume discounts</div>
        <div class="b2b-feat-text">Save 10–30% on orders of 50+ units</div>
      </div>
      <div class="b2b-feat">
        <div class="b2b-feat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
        <div class="b2b-feat-title">GST invoicing</div>
        <div class="b2b-feat-text">Proper tax invoices on all purchases</div>
      </div>
      <div class="b2b-feat">
        <div class="b2b-feat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg></div>
        <div class="b2b-feat-title">Priority dispatch</div>
        <div class="b2b-feat-text">Dedicated queue for bulk B2B orders</div>
      </div>
      <div class="b2b-feat">
        <div class="b2b-feat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><path d="M7 7h.01"/></svg></div>
        <div class="b2b-feat-title">AMC options</div>
        <div class="b2b-feat-text">Annual maintenance for enterprises</div>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════
     10. HOW IT WORKS (new process strip)
══════════════════════════════════════ --}}
<div class="section" style="background:var(--bg2);border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
  <div class="container">
    <div class="sec-head">
      <div>
        <div class="sec-label">Simple &amp; transparent</div>
        <div class="sec-title">How it <span>works</span></div>
        <div class="sec-sub">From shortlisting to delivery — here's exactly what to expect.</div>
      </div>
    </div>
    <div class="proc-grid">
      <div class="proc-card">
        <div class="proc-num">01</div>
        <div class="proc-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4-4"/></svg>
        </div>
        <div class="proc-step">Browse &amp; shortlist</div>
        <div class="proc-text">Filter by type, grade and budget. Every listing shows real photos, honest grade details and full verified specs.</div>
      </div>
      <div class="proc-card">
        <div class="proc-num">02</div>
        <div class="proc-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 1h4l2.7 13.4a2 2 0 002 1.6h9.7a2 2 0 002-1.6L23 6H6"/></svg>
        </div>
        <div class="proc-step">Add to quote cart</div>
        <div class="proc-text">No upfront payment. Add items and submit your requirements — tell us your budget and quantity in one click.</div>
      </div>
      <div class="proc-card">
        <div class="proc-num">03</div>
        <div class="proc-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div class="proc-step">Receive GST quote</div>
        <div class="proc-text">Our team reviews and sends a detailed GST invoice within 4 business hours, including warranty terms and delivery timeline.</div>
      </div>
      <div class="proc-card">
        <div class="proc-num">04</div>
        <div class="proc-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
        </div>
        <div class="proc-step">Fast pan-India delivery</div>
        <div class="proc-text">Confirm and we dispatch within 24hrs with full tracking. All units are packed with warranty documentation included.</div>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════
     11. BANNER ZONE C
══════════════════════════════════════ --}}
<div class="banner-zone-c">
  <a href="{{ route('contact') }}" title="Bulk &amp; B2B enquiries">
    <img src="https://placehold.co/1400x180/FBBF24/1a3c6e?text=Banner+C+%E2%80%94+Replace+with+your+image+%281400+%C3%97+180px%29"
         alt="Promotional banner" width="1400" height="180">
  </a>
</div>

{{-- ══════════════════════════════════════
     12. ECO SECTION (CTA fixed)
══════════════════════════════════════ --}}
<div class="eco-section">
  <div class="eco-inner">
    <div class="eco-top">
      <div class="eco-left">
        <div class="eco-tag">
          <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 00-7 17c1.5-2 4-3 7-3s5.5 1 7 3a10 10 0 00-7-17z"/></svg>
          Eco-friendly tech
        </div>
        <div class="eco-title">Every purchase<br><span class="eco-green">saves</span> the planet</div>
        <p class="eco-desc">When you buy refurbished from pcfy.in, you're not just saving money — you're making a <strong>real, measurable environmental impact</strong>. Buying refurbished also counts toward your company's ESG &amp; sustainability reporting.</p>
        <a href="{{ route('contact') }}" class="eco-cta">Learn about our green mission →</a>
      </div>
      <div class="eco-right">
        <div class="eco-stat-card">
          <div class="eco-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></div>
          <div><div class="eco-stat-num">12,000+</div><div class="eco-stat-label">Devices kept out of landfill</div></div>
        </div>
        <div class="eco-stat-card">
          <div class="eco-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h7v8l10-12h-7z"/></svg></div>
          <div><div class="eco-stat-num">48 Tonnes</div><div class="eco-stat-label">CO₂ equivalent saved since 2020</div></div>
        </div>
        <div class="eco-stat-card">
          <div class="eco-stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></div>
          <div><div class="eco-stat-num">6+ Years</div><div class="eco-stat-label">Average device life extended</div></div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════
     13. NEWSLETTER (form functional + privacy)
══════════════════════════════════════ --}}
<div class="newsletter">
  <div class="nl-inner">
    <div>
      <div class="nl-title">Get deals before everyone else</div>
      <div class="nl-sub">New stock alerts, flash sales &amp; B2B offers — straight to your inbox.</div>
    </div>
    <div>
      <form class="nl-form" id="nlForm">
        <input class="nl-input" type="email" name="email" placeholder="Enter your email address…" required>
        <button class="btn btn-accent" type="submit">Subscribe →</button>
      </form>
      <div class="nl-success" id="nlSuccess">
        <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        You're subscribed! Check your inbox for a confirmation.
      </div>
      <div class="nl-privacy">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l8 4v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6z"/></svg>
        No spam. Unsubscribe anytime. Your data is safe with us.
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════
     WhatsApp floating button
══════════════════════════════════════ --}}
@php $waNum = \App\Models\Setting::get('whatsapp_number', ''); @endphp
@if($waNum)
<a href="https://wa.me/{{ $waNum }}" target="_blank" rel="noopener noreferrer"
   class="wa-float" title="Chat with us on WhatsApp">
  <span class="wa-float-pulse"></span>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
  </svg>
</a>
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/home-b.js') }}"></script>
<script>
// ── Configurator: update href dynamically ──────────────────
(function () {
  var URLS = {
    laptop:      '{{ route("shop.category", "laptops") }}',
    desktop:     '{{ route("shop.category", "desktops") }}',
    workstation: '{{ route("shop.category", "desktops") }}',
    server:      '{{ route("shop.category", "desktops") }}'
  };
  var BUDGET = {
    low:  '?max_price=15000',
    mid:  '?min_price=15000&max_price=30000',
    high: '?min_price=30000'
  };
  var LABELS = {
    laptop: 'laptops', desktop: 'desktops',
    workstation: 'workstations', server: 'servers'
  };

  function patchCfgLink() {
    var typeSel   = document.querySelector('[data-group="type"] .sel');
    var useSel    = document.querySelector('[data-group="use"] .sel');
    var budgetSel = document.querySelector('[data-group="budget"] .sel');
    var type   = typeSel   ? typeSel.dataset.val   : 'laptop';
    var use    = useSel    ? useSel.dataset.val    : 'office';
    var budget = budgetSel ? budgetSel.dataset.val : 'low';
    var key    = (use === 'server') ? 'server' : type;
    var link   = document.getElementById('cfgLink');
    if (!link) return;
    link.href        = (URLS[key] || '{{ route("shop") }}') + (BUDGET[budget] || '');
    link.textContent = 'Find matching ' + (LABELS[key] || 'machines') + ' →';
  }

  // Fire after the original home-b.js handler updates the .sel class
  document.querySelectorAll('.cfg-opts .cfg-opt').forEach(function (opt) {
    opt.addEventListener('click', function () { setTimeout(patchCfgLink, 15); });
  });
  patchCfgLink();
})();

// ── Newsletter form ────────────────────────────────────────
document.getElementById('nlForm').addEventListener('submit', function (e) {
  e.preventDefault();
  this.style.display = 'none';
  var success = document.getElementById('nlSuccess');
  success.style.display = 'flex';
});
</script>
@endpush
