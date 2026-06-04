@props(['product'])

<div class="prod-card" onclick="if(!event.target.closest('button,a'))location.href='{{ route('product', $product->slug) }}'">
  <div class="prod-img">
    @if($product->thumb)
      <img src="{{ $product->thumb }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:contain;padding:8px;">
    @else
      <div class="ph">
        <span class="ph-label">{{ strtolower($product->brand?->name ?? 'pcfy') }} · product</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
          <rect x="2" y="4" width="20" height="13" rx="2"/><path d="M1 20h22"/>
        </svg>
      </div>
    @endif
    <span class="grade-stamp gs-{{ strtolower($product->grade) }}">GRADE {{ $product->grade }}</span>
    @if($product->stock_status === 'in_stock')
      <span class="stock-live">In stock</span>
    @elseif($product->stock_status === 'low_stock')
      <span class="stock-live" style="color:var(--accent);">Low stock</span>
    @endif
    <button class="wish-btn" title="Save for later">♡</button>
  </div>
  <div class="prod-body">
    <div class="prod-brand">{{ $product->brand?->name ?? 'pcfy' }}</div>
    <div class="prod-name">{{ $product->name }}</div>
    @php
      $specArr = $product->quick_specs;
      if (is_string($specArr)) { $specArr = json_decode($specArr, true); }
      $chips = array_slice((array)($specArr ?? []), 0, 4);
    @endphp
    @if(!empty($chips))
    <div class="prod-chips">
      @foreach($chips as $chip)
        <span class="prod-chip">{{ $chip }}</span>
      @endforeach
    </div>
    @endif
    <div class="prod-pricing">
      <span class="prod-price">{{ $product->price_inr }}</span>
      @if($product->mrp)
        <span class="prod-mrp">{{ $product->mrp_inr }}</span>
        <span class="prod-save">{{ $product->discount_percent }}% off</span>
      @endif
    </div>
    <div class="prod-warranty">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 2l8 4v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6z"/></svg>
      {{ $product->warranty_months }}-month warranty included
    </div>
    <div class="prod-footer">
      <form action="{{ route('cart.add') }}" method="POST" class="ajax-cart-form" style="flex:1;">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="grade" value="{{ $product->grade }}">
        <button type="submit" class="prod-btn-add" style="width:100%;">Add to quote</button>
      </form>
      <a href="{{ route('product', $product->slug) }}" class="prod-btn-view">View</a>
    </div>
  </div>
</div>
