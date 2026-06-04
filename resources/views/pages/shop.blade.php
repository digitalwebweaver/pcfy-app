@extends('layouts.site')
@section('title', ($category ? $category->name . ' — ' : '') . 'Shop Refurbished Hardware — pcfy.in')
@section('screen_label', 'Shop')
@section('page_css')
<link rel="stylesheet" href="{{ asset('css/shop.css') }}">
@endsection

@section('content')

<!-- LISTING HERO -->
<div class="lhero">
  <div class="lhero-inner">
    <div class="lh-crumb">
      <a href="{{ url('/') }}">Home</a>
      <span>›</span>
      @if($category)
        <a href="{{ route('shop') }}">All Products</a>
        <span>›</span>
        <span class="cur">{{ $category->name }}</span>
      @else
        <span class="cur">All Products</span>
      @endif
    </div>
    <h1 class="lh-title">
      @if($category)
        Refurbished <span>{{ $category->name }}</span>
      @else
        Shop <span>All Refurbished</span>
      @endif
    </h1>
    <p class="lh-sub">
      {{ $category?->description ?: 'Certified, 30-point tested hardware — honestly graded and warranty-backed. Filter by brand, spec, grade & budget.' }}
    </p>
  </div>
</div>

<!-- MOBILE TOOLBAR -->
<div class="mtoolbar">
  <button class="mtool-btn" id="openFilters">
    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
    Filters
    @if($activeFilters > 0)
      <span class="badge">{{ $activeFilters }}</span>
    @endif
  </button>
  <button class="mtool-btn" id="openSort">
    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h12M3 12h9M3 18h6M17 8V18M17 18l-3-3M17 18l3-3"/></svg>
    Sort
  </button>
</div>

<!-- SHOP LAYOUT -->
<div class="shop">

  <!-- FILTERS SIDEBAR -->
  <aside class="filters" id="filters">
    <div class="filters-head">
      <span class="t">
        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
        Filters
      </span>
      <a href="{{ request()->url() }}" class="clear">Clear all</a>
      <button class="close" id="closeFilters">
        <svg viewBox="0 0 24 24" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>

    <form class="filters-body" id="filterForm" method="GET"
          action="{{ $category ? route('shop.category', $category->slug) : route('shop') }}">
      @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif

      <!-- Category -->
      @if(!$category)
      <div class="fgroup open" data-fg>
        <div class="fg-head"><span class="l">Category</span><svg class="chev" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></div>
        <div class="fg-body">
          <div class="fg-inner">
            @foreach($categories as $cat)
            <label class="fopt">
              <input type="radio" name="category_id" value="{{ $cat->id }}"
                     {{ request('category_id') == $cat->id ? 'checked' : '' }}
                     onchange="document.getElementById('filterForm').submit()">
              <div class="fbox"><svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg></div>
              <span class="lbl">{{ $cat->name }}</span>
              <span class="cnt">{{ $cat->products_count }}</span>
            </label>
            @endforeach
          </div>
        </div>
      </div>
      @endif

      <!-- Brand -->
      <div class="fgroup open" data-fg>
        <div class="fg-head"><span class="l">Brand</span><svg class="chev" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></div>
        <div class="fg-body">
          <div class="fg-inner">
            @foreach($brands as $brand)
            <label class="fopt">
              <input type="checkbox" name="brand_id" value="{{ $brand->id }}"
                     {{ request('brand_id') == $brand->id ? 'checked' : '' }}>
              <div class="fbox"><svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg></div>
              <span class="lbl">{{ $brand->name }}</span>
              <span class="cnt">{{ $brand->products_count }}</span>
            </label>
            @endforeach
          </div>
        </div>
      </div>

      <!-- Grade -->
      <div class="fgroup open" data-fg>
        <div class="fg-head"><span class="l">Condition grade</span><svg class="chev" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></div>
        <div class="fg-body">
          <div class="fg-inner">
            @php $grades = ['A' => 'Like new', 'B' => 'Good', 'C' => 'Functional']; @endphp
            <div class="grade-chips">
              @foreach($grades as $g => $label)
              <div class="gchip {{ request('grade') === $g ? 'on' : '' }}"
                   data-g="{{ $g }}"
                   onclick="document.querySelector('[name=grade]').value='{{ request('grade') === $g ? '' : $g }}'; document.getElementById('filterForm').submit()">
                <div class="g">{{ $g }}</div>
                <div class="n">{{ $label }}</div>
              </div>
              @endforeach
            </div>
            <input type="hidden" name="grade" value="{{ request('grade') }}">
          </div>
        </div>
      </div>

      <!-- Processor -->
      @if(!empty($specOptions['processors']))
      <div class="fgroup open" data-fg>
        <div class="fg-head"><span class="l">Processor</span><svg class="chev" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></div>
        <div class="fg-body">
          <div class="fg-inner">
            <div class="spec-chips">
              @foreach($specOptions['processors'] as $proc => $cnt)
              <div class="spec-chip {{ request('processor') === $proc ? 'on' : '' }}"
                   onclick="document.querySelector('[name=processor]').value='{{ request('processor')===$proc ? '' : $proc }}'; document.getElementById('filterForm').submit()">
                {{ $proc }}<span class="cnt">{{ $cnt }}</span>
              </div>
              @endforeach
            </div>
            <input type="hidden" name="processor" value="{{ request('processor') }}">
          </div>
        </div>
      </div>
      @endif

      <!-- RAM -->
      @if(!empty($specOptions['ram']))
      <div class="fgroup open" data-fg>
        <div class="fg-head"><span class="l">RAM</span><svg class="chev" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></div>
        <div class="fg-body">
          <div class="fg-inner">
            <div class="spec-chips">
              @foreach($specOptions['ram'] as $ramVal => $cnt)
              <div class="spec-chip {{ request('ram') === $ramVal ? 'on' : '' }}"
                   onclick="document.querySelector('[name=ram]').value='{{ request('ram')===$ramVal ? '' : $ramVal }}'; document.getElementById('filterForm').submit()">
                {{ $ramVal }}<span class="cnt">{{ $cnt }}</span>
              </div>
              @endforeach
            </div>
            <input type="hidden" name="ram" value="{{ request('ram') }}">
          </div>
        </div>
      </div>
      @endif

      <!-- Storage -->
      @if(!empty($specOptions['storage']))
      <div class="fgroup open" data-fg>
        <div class="fg-head"><span class="l">Storage</span><svg class="chev" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></div>
        <div class="fg-body">
          <div class="fg-inner">
            <div class="spec-chips">
              @foreach($specOptions['storage'] as $storVal => $cnt)
              <div class="spec-chip {{ request('storage') === $storVal ? 'on' : '' }}"
                   onclick="document.querySelector('[name=storage]').value='{{ request('storage')===$storVal ? '' : $storVal }}'; document.getElementById('filterForm').submit()">
                {{ $storVal }}<span class="cnt">{{ $cnt }}</span>
              </div>
              @endforeach
            </div>
            <input type="hidden" name="storage" value="{{ request('storage') }}">
          </div>
        </div>
      </div>
      @endif

      <!-- Price -->
      <div class="fgroup open" data-fg>
        <div class="fg-head"><span class="l">Price</span><svg class="chev" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></div>
        <div class="fg-body">
          <div class="fg-inner">
            <div class="range">
              <div class="range-slider">
                <div class="range-track"></div>
                <div class="range-fill" id="rangeFill"></div>
                <input type="range" id="rMin" name="min_price" min="0" max="200000" step="1000"
                       value="{{ request('min_price', 0) }}" oninput="updateRange()">
                <input type="range" id="rMax" name="max_price" min="0" max="200000" step="1000"
                       value="{{ request('max_price', 200000) }}" oninput="updateRange()">
              </div>
              <div class="range-vals">
                <span class="range-pill" id="rMinV">₹{{ number_format(request('min_price',0)) }}</span>
                <span class="range-pill" id="rMaxV">₹{{ number_format(request('max_price',200000)) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <input type="hidden" name="sort" value="{{ request('sort','newest') }}">
    </form>

    <div class="fapply">
      <a href="{{ request()->url() }}" class="clr">Clear</a>
      <button class="app" onclick="document.getElementById('filterForm').submit()">Show results</button>
    </div>
  </aside>

  <!-- RESULTS -->
  <div class="results">
    @php
      $sortOptions = [
        'newest'     => 'Relevance',
        'price_asc'  => 'Price: Low to High',
        'price_desc' => 'Price: High to Low',
        'name'       => 'Name A-Z',
        'discount'   => 'Biggest discount',
      ];
      $currentSort = request('sort', 'newest');
    @endphp
    <div class="sortbar">
      <div class="res-count">
        <b>{{ $products->total() }}</b> product{{ $products->total() !== 1 ? 's' : '' }}
        @if(request()->hasAny(['ram','storage','processor','brand_id','grade','q']))
          &nbsp;<span style="color:var(--accent-dark);font-size:11px;font-weight:700;">· filtered</span>
        @endif
      </div>
      <div class="sort-wrap desktop-sort">
        <label>Sort by</label>
        <select class="sort-sel" onchange="window.location.href=this.value">
          @foreach($sortOptions as $val => $lbl)
          <option value="{{ request()->fullUrlWithQuery(['sort' => $val]) }}" {{ $currentSort === $val ? 'selected' : '' }}>
            {{ $lbl }}
          </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="p-grid" id="grid">
      @forelse($products as $product)
        <x-site.product-card :product="$product" />
      @empty
        <div class="empty" style="display:block;" id="empty">
          <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4-4M8 11h6"/></svg>
          <div class="t">No products match your filters</div>
          <div class="s">Try removing a filter or widening your price range.</div>
        </div>
      @endforelse
    </div>

    @if($products->hasPages())
    <div style="padding:20px 0;">
      {{ $products->appends(request()->query())->links() }}
    </div>
    @endif
  </div>
</div>

<!-- FILTER BACKDROP + SORT SHEET -->
<div class="fbackdrop" id="fbackdrop"></div>
<div class="sheet-back" id="sortBack"></div>
<div class="sort-sheet" id="sortSheet">
  <div class="grab"></div>
  <div class="st">Sort by</div>
  @foreach($sortOptions as $val => $lbl)
  <div class="sort-opt {{ $currentSort === $val ? 'on' : '' }}"
       onclick="window.location.href='{{ request()->fullUrlWithQuery(['sort' => $val]) }}'">
    {{ $lbl }}
    <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
  </div>
  @endforeach
</div>

@endsection

@push('scripts')
<script>
// Price range
function updateRange() {
  var min=+document.getElementById('rMin').value, max=+document.getElementById('rMax').value;
  if(min>max){var t=min;min=max;max=t;}
  document.getElementById('rMinV').textContent='₹'+min.toLocaleString('en-IN');
  document.getElementById('rMaxV').textContent='₹'+max.toLocaleString('en-IN');
  var pct1=min/2000,pct2=max/2000;
  document.getElementById('rangeFill').style.left=pct1+'%';
  document.getElementById('rangeFill').style.right=(100-pct2)+'%';
}
updateRange();
</script>
<script src="{{ asset('js/shop.js') }}"></script>
@endpush
