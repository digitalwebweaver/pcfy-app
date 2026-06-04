@extends('admin.layouts.app')
@section('title', 'Order ' . $order->order_number)

@push('styles')
<style>
.status-pending    { background:#f1f5f9; color:#475569; }
.status-confirmed  { background:#dbeafe; color:#1d4ed8; }
.status-processing { background:#fef9c3; color:#854d0e; }
.status-shipped    { background:#e0e7ff; color:#4338ca; }
.status-delivered  { background:#dcfce7; color:#166534; }
.status-cancelled  { background:#fee2e2; color:#991b1b; }
.pay-pending { background:#f1f5f9; color:#475569; }
.pay-partial { background:#fef9c3; color:#854d0e; }
.pay-paid    { background:#dcfce7; color:#166534; }
.ship-preparing        { background:#f1f5f9; color:#475569; }
.ship-dispatched       { background:#dbeafe; color:#1d4ed8; }
.ship-in_transit       { background:#e0e7ff; color:#4338ca; }
.ship-out_for_delivery { background:#fef9c3; color:#92400e; }
.ship-delivered        { background:#dcfce7; color:#166534; }
.ship-exception        { background:#fee2e2; color:#991b1b; }
.price-input { height:30px; padding:0 8px; border:1.5px solid var(--border); border-radius:5px; font-size:12.5px; font-family:inherit; width:110px; outline:none; }
.price-input:focus { border-color:var(--navy); }
.timeline { position:relative; padding-left:24px; }
.timeline::before { content:''; position:absolute; left:7px; top:6px; bottom:6px; width:2px; background:var(--border); }
.tl-item { position:relative; margin-bottom:16px; }
.tl-dot { position:absolute; left:-24px; top:4px; width:14px; height:14px; border-radius:50%; border:2px solid var(--border); background:var(--bg); }
.tl-dot.active { background:var(--navy); border-color:var(--navy); }
.tl-dot.success { background:#16a34a; border-color:#16a34a; }
.tl-dot.warn { background:#f59e0b; border-color:#f59e0b; }
</style>
@endpush

@section('content')
@php
  $sc = match($order->status) {
    'pending'=>'status-pending','confirmed'=>'status-confirmed','processing'=>'status-processing',
    'shipped'=>'status-shipped','delivered'=>'status-delivered','cancelled'=>'status-cancelled',
    default=>'badge-gray'
  };
  $pc = match($order->payment_status) {
    'pending'=>'pay-pending','partial'=>'pay-partial','paid'=>'pay-paid',default=>'badge-gray'
  };
@endphp

<div class="page-header">
  <div>
    <div class="page-title" style="display:flex;align-items:center;gap:10px;">
      <span style="font-family:'DM Mono',monospace;font-size:16px;">{{ $order->order_number }}</span>
      <span class="badge {{ $sc }}">{{ \App\Models\Order::statusLabel($order->status) }}</span>
      <span class="badge {{ $pc }}">{{ \App\Models\Order::paymentLabel($order->payment_status) }}</span>
    </div>
    <div class="page-sub">
      {{ $order->customer_name }}
      @if($order->company_name) · {{ $order->company_name }} @endif
      · Created {{ $order->created_at->diffForHumans() }}
      @if($order->quote) · from Quote <a href="{{ route('adminhu.quotes.show', $order->quote) }}" style="color:var(--navy);">{{ $order->quote->quote_number }}</a> @endif
    </div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.orders.index') }}" class="btn btn-ghost">← All orders</a>
  </div>
</div>

<div class="page-body">
<div style="display:grid;grid-template-columns:1fr 300px;gap:14px;align-items:start;">

  {{-- LEFT --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    {{-- Customer & addresses --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Customer &amp; Addresses</div></div>
      <form action="{{ route('adminhu.orders.update', $order) }}" method="POST">
        @csrf @method('PATCH')
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
          @if($errors->any())
            <div style="background:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:8px;font-size:13px;">
              {{ $errors->first() }}
            </div>
          @endif
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div>
              <label class="label">Customer name *</label>
              <input class="input" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required>
            </div>
            <div>
              <label class="label">Email *</label>
              <input class="input" type="email" name="customer_email" value="{{ old('customer_email', $order->customer_email) }}" required>
            </div>
            <div>
              <label class="label">Phone</label>
              <input class="input" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}">
            </div>
            <div>
              <label class="label">Company</label>
              <input class="input" name="company_name" value="{{ old('company_name', $order->company_name) }}">
            </div>
            <div>
              <label class="label">GSTIN</label>
              <input class="input" name="gstin" value="{{ old('gstin', $order->gstin) }}">
            </div>
            <div>
              <label class="label">Est. delivery date</label>
              <input class="input" type="date" name="estimated_delivery_date"
                     value="{{ old('estimated_delivery_date', $order->estimated_delivery_date?->format('Y-m-d')) }}">
            </div>
            <div>
              <label class="label">Payment status</label>
              <select class="select" name="payment_status">
                @foreach(\App\Models\Order::paymentStatuses() as $s)
                  <option value="{{ $s }}" {{ $order->payment_status===$s?'selected':'' }}>{{ \App\Models\Order::paymentLabel($s) }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="label">Payment notes</label>
              <input class="input" name="payment_notes" value="{{ old('payment_notes', $order->payment_notes) }}" placeholder="UPI, bank transfer ref…">
            </div>
            <div style="grid-column:span 2;">
              <label class="label">Billing address</label>
              <textarea class="textarea" name="billing_address" rows="3" placeholder="Full billing address…">{{ old('billing_address', $order->billing_address) }}</textarea>
            </div>
            <div style="grid-column:span 2;">
              <label class="label">Shipping address</label>
              <textarea class="textarea" name="shipping_address" rows="3" placeholder="Full shipping address…">{{ old('shipping_address', $order->shipping_address) }}</textarea>
            </div>
            <div style="grid-column:span 2;">
              <label class="label">Internal notes</label>
              <textarea class="textarea" name="notes" rows="2" placeholder="Any internal notes…">{{ old('notes', $order->notes) }}</textarea>
            </div>
          </div>
          <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="btn btn-primary">Save details</button>
          </div>
        </div>
      </form>
    </div>

    {{-- Items --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title">Order Items</div>
        @if($order->total_amount > 0)
          <span style="font-size:14px;font-weight:700;color:var(--navy);">Total: {{ $order->total_amount_inr }}</span>
        @endif
      </div>
      <form action="{{ route('adminhu.orders.items', $order) }}" method="POST">
        @csrf
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Grade</th>
                <th style="width:80px;">Qty</th>
                <th style="width:140px;">Unit price (₹)</th>
                <th style="width:120px;">Line total</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              @foreach($order->items as $i => $item)
              <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
              <tr>
                <td>
                  <div style="font-weight:600;font-size:13px;">{{ $item->product_name }}</div>
                  @if($item->product_sku)<div style="font-size:11px;color:var(--muted);font-family:'DM Mono',monospace;">{{ $item->product_sku }}</div>@endif
                </td>
                <td>
                  @if($item->grade)
                    <span class="badge {{ $item->grade==='A'?'badge-green':($item->grade==='B'?'badge-amber':'badge-gray') }}">{{ $item->grade }}</span>
                  @else —
                  @endif
                </td>
                <td><input type="number" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}" min="1" max="999" class="price-input" style="width:60px;"></td>
                <td>
                  <div style="display:flex;align-items:center;gap:4px;">
                    <span style="font-size:12px;color:var(--muted);">₹</span>
                    <input type="number" name="items[{{ $i }}][unit_price]"
                           value="{{ $item->unit_price ? $item->unit_price/100 : '' }}"
                           min="0" step="0.01" placeholder="—" class="price-input">
                  </div>
                </td>
                <td style="font-size:13px;font-weight:600;color:var(--navy);">
                  {{ $item->line_total > 0 ? $item->line_total_inr : '—' }}
                </td>
                <td><input type="text" name="items[{{ $i }}][notes]" value="{{ $item->notes }}" placeholder="Optional" class="price-input" style="width:100%;"></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div style="padding:12px 16px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;">
          <button type="submit" class="btn btn-primary">Save items</button>
        </div>
      </form>
    </div>

    {{-- Shipments --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Shipment Tracking</div></div>
      <div style="padding:14px 16px;">

        {{-- Timeline of existing shipments --}}
        @if($order->shipments->count())
        <div class="timeline" style="margin-bottom:20px;">
          @foreach($order->shipments as $sh)
          @php
            $shc = 'ship-'.$sh->status;
            $dotClass = match($sh->status) {
              'delivered' => 'success',
              'exception' => 'warn',
              'dispatched','in_transit','out_for_delivery' => 'active',
              default => '',
            };
          @endphp
          <div class="tl-item">
            <div class="tl-dot {{ $dotClass }}"></div>
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;flex-wrap:wrap;">
              <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                  <span class="badge {{ $shc }}">{{ \App\Models\OrderShipment::statusLabel($sh->status) }}</span>
                  @if($sh->courier_name)<span style="font-size:13px;font-weight:600;">{{ $sh->courier_name }}</span>@endif
                  @if($sh->tracking_number)
                    @if($sh->tracking_url)
                      <a href="{{ $sh->tracking_url }}" target="_blank" style="font-size:12px;font-family:'DM Mono',monospace;color:var(--navy);">{{ $sh->tracking_number }} ↗</a>
                    @else
                      <span style="font-size:12px;font-family:'DM Mono',monospace;color:var(--muted);">{{ $sh->tracking_number }}</span>
                    @endif
                  @endif
                </div>
                @if($sh->notes)<div style="font-size:13px;color:var(--text);margin-bottom:4px;">{{ $sh->notes }}</div>@endif
                <div style="font-size:11.5px;color:var(--muted);">
                  Added {{ $sh->created_at->format('d M Y, g:ia') }}
                  @if($sh->estimated_delivery) · Est. delivery: {{ $sh->estimated_delivery->format('d M Y') }}@endif
                </div>
              </div>
              {{-- Inline update form --}}
              <form action="{{ route('adminhu.orders.shipments.update', [$order, $sh]) }}" method="POST" style="display:flex;gap:6px;align-items:center;">
                @csrf @method('PATCH')
                <input type="hidden" name="courier_name" value="{{ $sh->courier_name }}">
                <input type="hidden" name="tracking_number" value="{{ $sh->tracking_number }}">
                <input type="hidden" name="tracking_url" value="{{ $sh->tracking_url }}">
                <input type="hidden" name="estimated_delivery" value="{{ $sh->estimated_delivery?->format('Y-m-d') }}">
                <input type="hidden" name="notes" value="{{ $sh->notes }}">
                <select class="select" name="status" style="height:30px;font-size:12px;padding:0 8px;">
                  @foreach(\App\Models\OrderShipment::statuses() as $s)
                    <option value="{{ $s }}" {{ $sh->status===$s?'selected':'' }}>{{ \App\Models\OrderShipment::statusLabel($s) }}</option>
                  @endforeach
                </select>
                <button type="submit" class="btn btn-ghost btn-sm">Update</button>
              </form>
            </div>
          </div>
          @endforeach
        </div>
        @else
        <p style="color:var(--muted);font-size:13px;margin-bottom:16px;">No shipment entries yet.</p>
        @endif

        {{-- Add new shipment --}}
        <details style="border:1.5px solid var(--border);border-radius:10px;overflow:hidden;">
          <summary style="padding:11px 14px;font-size:13px;font-weight:600;cursor:pointer;list-style:none;display:flex;align-items:center;gap:8px;">
            <svg viewBox="0 0 24 24" style="width:15px;height:15px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add shipment entry
          </summary>
          <form action="{{ route('adminhu.orders.shipments.store', $order) }}" method="POST" style="padding:14px;border-top:1px solid var(--border);display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            @csrf
            <div>
              <label class="label">Courier / carrier</label>
              <input class="input" name="courier_name" placeholder="Delhivery, BlueDart, DTDC…">
            </div>
            <div>
              <label class="label">Tracking number</label>
              <input class="input" name="tracking_number" placeholder="AWB number">
            </div>
            <div style="grid-column:span 2;">
              <label class="label">Tracking URL</label>
              <input class="input" type="url" name="tracking_url" placeholder="https://tracking.courier.com/...">
            </div>
            <div>
              <label class="label">Shipment status</label>
              <select class="select" name="status">
                @foreach(\App\Models\OrderShipment::statuses() as $s)
                  <option value="{{ $s }}">{{ \App\Models\OrderShipment::statusLabel($s) }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="label">Estimated delivery</label>
              <input class="input" type="date" name="estimated_delivery">
            </div>
            <div style="grid-column:span 2;">
              <label class="label">Notes</label>
              <textarea class="textarea" name="notes" rows="2" placeholder="Any shipment notes…"></textarea>
            </div>
            <div style="grid-column:span 2;display:flex;justify-content:flex-end;">
              <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                Add shipment
              </button>
            </div>
          </form>
        </details>

      </div>
    </div>

  </div>

  {{-- RIGHT sidebar --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    {{-- Order status --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Order Status</div></div>
      <div class="card-body">
        <form action="{{ route('adminhu.orders.status', $order) }}" method="POST">
          @csrf @method('PATCH')
          <select class="select" name="status" onchange="this.form.submit()" style="margin-bottom:8px;">
            @foreach(\App\Models\Order::statuses() as $s)
              <option value="{{ $s }}" {{ $order->status===$s?'selected':'' }}>{{ \App\Models\Order::statusLabel($s) }}</option>
            @endforeach
          </select>
        </form>
        <div style="font-size:12px;color:var(--muted);line-height:1.7;margin-top:6px;">
          Created: {{ $order->created_at->format('d M Y, g:ia') }}<br>
          @if($order->confirmed_at) Confirmed: {{ $order->confirmed_at->format('d M Y') }}<br> @endif
          @if($order->shipped_at)   Shipped: {{ $order->shipped_at->format('d M Y') }}<br> @endif
          @if($order->delivered_at) Delivered: {{ $order->delivered_at->format('d M Y') }}<br> @endif
          @if($order->estimated_delivery_date) Est. delivery: {{ $order->estimated_delivery_date->format('d M Y') }} @endif
        </div>
      </div>
    </div>

    {{-- Assign --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Assigned to</div></div>
      <div class="card-body">
        <form action="{{ route('adminhu.orders.update', $order) }}" method="POST">
          @csrf @method('PATCH')
          <input type="hidden" name="customer_name"  value="{{ $order->customer_name }}">
          <input type="hidden" name="customer_email" value="{{ $order->customer_email }}">
          <input type="hidden" name="payment_status" value="{{ $order->payment_status }}">
          <select class="select" name="assigned_to" onchange="this.form.submit()">
            <option value="">— Unassigned —</option>
            @foreach($users as $u)
              <option value="{{ $u->id }}" {{ $order->assigned_to==$u->id?'selected':'' }}>{{ $u->name }}</option>
            @endforeach
          </select>
        </form>
      </div>
    </div>

    {{-- Quick info --}}
    @if($order->quote)
    <div class="card">
      <div class="card-header"><div class="card-title">Source Quote</div></div>
      <div class="card-body" style="font-size:13px;">
        <a href="{{ route('adminhu.quotes.show', $order->quote) }}" style="font-family:'DM Mono',monospace;font-weight:700;color:var(--navy);">
          {{ $order->quote->quote_number }}
        </a>
        <div style="color:var(--muted);font-size:12px;margin-top:4px;">{{ $order->quote->created_at->format('d M Y') }}</div>
      </div>
    </div>
    @endif

  </div>

</div>
</div>
@endsection
