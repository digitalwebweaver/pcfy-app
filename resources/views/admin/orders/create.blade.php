@extends('admin.layouts.app')
@section('title', 'New Order')

@push('styles')
<style>
/* Items grid: product(flex) | grade(70px) | qty(80px) | price(130px) | notes(1fr) | remove(34px) */
.item-row { display:grid; grid-template-columns:1fr 70px 80px 130px 1fr 34px; gap:8px; align-items:start; padding:10px 0; border-bottom:1px solid var(--border); }
.item-row:last-child { border-bottom:none; }
.item-row .input { height:34px; font-size:13px; }
.items-hdr  { display:grid; grid-template-columns:1fr 70px 80px 130px 1fr 34px; gap:8px; }
.remove-btn { height:34px; width:34px; display:flex; align-items:center; justify-content:center; border:1.5px solid var(--border); border-radius:7px; background:none; cursor:pointer; color:var(--muted); transition:all .15s; flex-shrink:0; margin-top:1px; }
.remove-btn:hover { border-color:#ef4444; color:#ef4444; background:#fef2f2; }
/* Dropdown — positioned relative to the product-name wrapper; overflow must be visible on ancestors */
.prod-wrap { position:relative; }
.product-dropdown { position:absolute; z-index:9999; background:var(--bg,#fff); border:1.5px solid var(--border); border-radius:10px; box-shadow:0 8px 28px rgba(0,0,0,.14); max-height:280px; overflow-y:auto; width:100%; min-width:320px; top:calc(100% + 3px); left:0; }
.pd-item { padding:10px 13px; cursor:pointer; border-bottom:1px solid var(--border); font-size:13px; }
.pd-item:last-child { border-bottom:none; }
.pd-item:hover, .pd-item.focused { background:var(--bg2,#f5f7fa); }
.pd-name { font-weight:600; color:var(--navy,#1a1a2e); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.pd-meta { font-size:11.5px; color:var(--muted,#888); margin-top:2px; }
.col-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">New Order</div>
    <div class="page-sub">Create an order directly — for phone/email sales without a quote</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.orders.index') }}" class="btn btn-ghost">← Cancel</a>
  </div>
</div>

<div class="page-body">
<form action="{{ route('adminhu.orders.store') }}" method="POST" id="orderForm">
@csrf
<div style="display:grid;grid-template-columns:1fr 280px;gap:14px;align-items:start;">

  {{-- LEFT --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    {{-- Customer --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Customer Details</div></div>
      <div class="card-body">
        @if($errors->any())
          <div style="background:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:12px;">
            {{ $errors->first() }}
          </div>
        @endif
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div>
            <label class="label">Name *</label>
            <input class="input" name="customer_name" value="{{ old('customer_name') }}" required placeholder="Full name">
          </div>
          <div>
            <label class="label">Email *</label>
            <input class="input" type="email" name="customer_email" value="{{ old('customer_email') }}" required placeholder="email@company.com">
          </div>
          <div>
            <label class="label">Phone</label>
            <input class="input" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="+91 98765 43210">
          </div>
          <div>
            <label class="label">Company</label>
            <input class="input" name="company_name" value="{{ old('company_name') }}" placeholder="Company Pvt Ltd">
          </div>
          <div>
            <label class="label">GSTIN</label>
            <input class="input" name="gstin" value="{{ old('gstin') }}" placeholder="22AAAAA0000A1Z5">
          </div>
          <div>
            <label class="label">Est. delivery date</label>
            <input class="input" type="date" name="estimated_delivery_date" value="{{ old('estimated_delivery_date') }}">
          </div>
          <div style="grid-column:span 2;">
            <label class="label">Billing address</label>
            <textarea class="textarea" name="billing_address" rows="2" placeholder="Building, Street, City, State, PIN">{{ old('billing_address') }}</textarea>
          </div>
          <div style="grid-column:span 2;">
            <label class="label">Shipping address <span style="font-weight:400;color:var(--muted);">(leave blank if same as billing)</span></label>
            <textarea class="textarea" name="shipping_address" rows="2" placeholder="Building, Street, City, State, PIN">{{ old('shipping_address') }}</textarea>
          </div>
        </div>
      </div>
    </div>

    {{-- Items --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title">Order Items</div>
        <span id="orderTotal" style="font-size:14px;font-weight:700;color:var(--navy);"></span>
      </div>
      <div class="card-body" style="padding-bottom:0;overflow:visible;">

        {{-- Column headers --}}
        <div class="items-hdr" style="margin-bottom:4px;">
          <div class="col-label">Product name</div>
          <div class="col-label">Grade</div>
          <div class="col-label">Qty</div>
          <div class="col-label">Unit price (₹)</div>
          <div class="col-label">Notes</div>
          <div></div>
        </div>

        <div id="itemsContainer" style="overflow:visible;">
          {{-- rows injected by JS --}}
        </div>

        <div style="padding:12px 0 4px;">
          <button type="button" id="addItemBtn" class="btn btn-ghost btn-sm">
            <svg viewBox="0 0 24 24" style="width:13px;height:13px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add item
          </button>
        </div>
      </div>
    </div>

  </div>

  {{-- RIGHT --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    <div class="card">
      <div class="card-header"><div class="card-title">Order Settings</div></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
        <div>
          <label class="label">Payment status</label>
          <select class="select" name="payment_status">
            @foreach(\App\Models\Order::paymentStatuses() as $s)
              <option value="{{ $s }}" {{ old('payment_status')===$s?'selected':'' }}>{{ \App\Models\Order::paymentLabel($s) }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Payment notes</label>
          <input class="input" name="payment_notes" value="{{ old('payment_notes') }}" placeholder="UPI ref, bank transfer…">
        </div>
        <div>
          <label class="label">Assign to</label>
          <select class="select" name="assigned_to">
            <option value="">— Unassigned —</option>
            @foreach($users as $u)
              <option value="{{ $u->id }}" {{ old('assigned_to')==$u->id?'selected':'' }}>{{ $u->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Internal notes</label>
          <textarea class="textarea" name="notes" rows="3" placeholder="Source: phone call with XYZ…">{{ old('notes') }}</textarea>
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;height:44px;font-size:14px;">
      Create Order →
    </button>

  </div>

</div>
</form>
</div>

@push('scripts')
@php
$productsJson = json_encode($products->map(fn($p) => [
    'id'    => $p->id,
    'name'  => $p->name,
    'sku'   => $p->sku,
    'grade' => $p->grade,
    'price' => $p->price ? $p->price / 100 : null,
    'brand' => $p->brand?->name,
]));
@endphp
<script>
const PRODUCTS = {!! $productsJson !!};

let rowIdx = 0;

function addRow(prefill) {
  prefill = prefill || {};
  const idx = rowIdx++;
  const row = document.createElement('div');
  row.className = 'item-row';
  row.dataset.idx = idx;
  row.innerHTML = `
    <div class="prod-wrap">
      <input class="input prod-name-input" style="height:34px;font-size:13px;width:100%;"
             name="items[${idx}][product_name]"
             value="${esc(prefill.name||'')}"
             placeholder="Search or type product name…"
             autocomplete="off"
             data-idx="${idx}">
      <input type="hidden" name="items[${idx}][product_id]"  value="${esc(prefill.id||'')}">
      <input type="hidden" name="items[${idx}][product_sku]" value="${esc(prefill.sku||'')}">
      <div class="product-dropdown" id="pd_${idx}" style="display:none;"></div>
    </div>
    <input class="input" style="height:34px;font-size:13px;width:100%;"
           name="items[${idx}][grade]" value="${esc(prefill.grade||'')}" placeholder="A/B/C" maxlength="3">
    <input class="input" type="number" style="height:34px;font-size:13px;width:100%;"
           name="items[${idx}][quantity]" value="${prefill.qty||1}" min="1" max="999">
    <input class="input" type="number" style="height:34px;font-size:13px;width:100%;"
           name="items[${idx}][unit_price]" value="${prefill.price||''}" min="0" step="0.01" placeholder="—">
    <input class="input" style="height:34px;font-size:13px;width:100%;"
           name="items[${idx}][notes]" value="${esc(prefill.notes||'')}" placeholder="Optional">
    <button type="button" class="remove-btn" onclick="removeRow(this)">
      <svg viewBox="0 0 24 24" style="width:13px;height:13px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>`;
  document.getElementById('itemsContainer').appendChild(row);
  wireSearch(row.querySelector('.prod-name-input'));
  wireTotal();
}

function removeRow(btn) {
  const container = document.getElementById('itemsContainer');
  if (container.children.length > 1) {
    btn.closest('.item-row').remove();
    updateTotal();
  }
}

function wireSearch(input) {
  const idx  = input.dataset.idx;
  const drop = document.getElementById('pd_' + idx);
  let timer;

  input.addEventListener('input', function() {
    clearTimeout(timer);
    const q = this.value.trim().toLowerCase();
    if (q.length < 1) { drop.style.display = 'none'; return; }
    timer = setTimeout(function() {
      const matches = PRODUCTS.filter(p =>
        p.name.toLowerCase().includes(q) ||
        (p.brand && p.brand.toLowerCase().includes(q)) ||
        (p.sku && p.sku.toLowerCase().includes(q))
      ).slice(0, 8);
      if (!matches.length) { drop.style.display = 'none'; return; }
      drop.innerHTML = matches.map(p =>
        `<div class="pd-item" data-id="${p.id}" data-name="${esc(p.name)}" data-sku="${esc(p.sku||'')}" data-grade="${esc(p.grade||'')}" data-price="${p.price||''}">
          <div class="pd-name">${esc(p.name)}</div>
          <div class="pd-meta">${p.brand ? esc(p.brand)+' · ' : ''}Grade ${esc(p.grade||'—')} · ${p.sku ? esc(p.sku)+' · ' : ''}${p.price ? '₹'+Number(p.price).toLocaleString('en-IN') : 'No price set'}</div>
        </div>`
      ).join('');
      drop.style.display = 'block';
      drop.querySelectorAll('.pd-item').forEach(item => {
        item.addEventListener('click', function() {
          fillRow(idx, this.dataset);
          drop.style.display = 'none';
        });
      });
    }, 200);
  });

  document.addEventListener('click', function(e) {
    if (!drop.contains(e.target) && e.target !== input) drop.style.display = 'none';
  });

  input.addEventListener('keydown', function(e) {
    const items = drop.querySelectorAll('.pd-item');
    const focused = drop.querySelector('.pd-item.focused');
    const fi = focused ? Array.from(items).indexOf(focused) : -1;
    if (e.key === 'ArrowDown' && drop.style.display !== 'none') {
      e.preventDefault();
      if (fi >= 0) focused.classList.remove('focused');
      if (fi < items.length - 1) items[fi + 1].classList.add('focused');
    } else if (e.key === 'ArrowUp' && drop.style.display !== 'none') {
      e.preventDefault();
      if (fi > 0) { focused.classList.remove('focused'); items[fi - 1].classList.add('focused'); }
    } else if (e.key === 'Enter') {
      const f = drop.querySelector('.pd-item.focused');
      if (f) { e.preventDefault(); fillRow(idx, f.dataset); drop.style.display = 'none'; }
    } else if (e.key === 'Escape') {
      drop.style.display = 'none';
    }
  });
}

function fillRow(idx, d) {
  const row = document.querySelector('.item-row[data-idx="' + idx + '"]');
  if (!row) return;
  row.querySelector('[name="items[' + idx + '][product_name]"]').value = d.name || '';
  row.querySelector('[name="items[' + idx + '][product_id]"]').value  = d.id   || '';
  row.querySelector('[name="items[' + idx + '][product_sku]"]').value = d.sku  || '';
  row.querySelector('[name="items[' + idx + '][grade]"]').value       = d.grade|| '';
  if (d.price) row.querySelector('[name="items[' + idx + '][unit_price]"]').value = d.price;
  updateTotal();
}

function wireTotal() {
  document.getElementById('itemsContainer').addEventListener('input', updateTotal);
}

function updateTotal() {
  let total = 0;
  document.querySelectorAll('.item-row').forEach(row => {
    const idx  = row.dataset.idx;
    const qty  = parseFloat(row.querySelector('[name*="[quantity]"]')?.value) || 0;
    const price= parseFloat(row.querySelector('[name*="[unit_price]"]')?.value) || 0;
    total += qty * price;
  });
  const el = document.getElementById('orderTotal');
  el.textContent = total > 0 ? '₹' + total.toLocaleString('en-IN', {maximumFractionDigits:2}) : '';
}

function esc(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.getElementById('addItemBtn').addEventListener('click', function() { addRow(); });
addRow(); // start with one row
</script>
@endpush
@endsection
