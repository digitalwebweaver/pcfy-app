@extends('admin.layouts.app')
@section('title', 'Orders')

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
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Orders</div>
    <div class="page-sub">Manage orders from quotes or direct phone/email sales</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.orders.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24" style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Order
    </a>
  </div>
</div>

<div class="page-body">

  {{-- Filters --}}
  <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
    <input class="input" name="search" value="{{ request('search') }}" placeholder="Search order #, name, email…" style="max-width:280px;">
    <select class="select" name="status" style="width:auto;" onchange="this.form.submit()">
      <option value="">All statuses</option>
      @foreach(\App\Models\Order::statuses() as $s)
        <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ \App\Models\Order::statusLabel($s) }}</option>
      @endforeach
    </select>
    <select class="select" name="payment" style="width:auto;" onchange="this.form.submit()">
      <option value="">All payments</option>
      @foreach(\App\Models\Order::paymentStatuses() as $s)
        <option value="{{ $s }}" {{ request('payment')===$s ? 'selected' : '' }}>{{ \App\Models\Order::paymentLabel($s) }}</option>
      @endforeach
    </select>
    <button class="btn btn-ghost" type="submit">Filter</button>
    @if(request('search') || request('status') || request('payment'))
      <a href="{{ route('adminhu.orders.index') }}" class="btn btn-ghost">Clear</a>
    @endif
  </form>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Items</th>
            <th>Total</th>
            <th>Status</th>
            <th>Payment</th>
            <th>Date</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
          @php
            $sc = match($order->status) {
              'pending'    => 'status-pending',
              'confirmed'  => 'status-confirmed',
              'processing' => 'status-processing',
              'shipped'    => 'status-shipped',
              'delivered'  => 'status-delivered',
              'cancelled'  => 'status-cancelled',
              default      => 'badge-gray',
            };
            $pc = match($order->payment_status) {
              'pending' => 'pay-pending',
              'partial' => 'pay-partial',
              'paid'    => 'pay-paid',
              default   => 'badge-gray',
            };
          @endphp
          <tr>
            <td>
              <a href="{{ route('adminhu.orders.show', $order) }}" style="font-family:'DM Mono',monospace;font-size:12.5px;font-weight:700;color:var(--navy);">
                {{ $order->order_number }}
              </a>
              @if($order->quote)
                <div style="font-size:11px;color:var(--muted);">Quote {{ $order->quote->quote_number }}</div>
              @endif
            </td>
            <td>
              <div style="font-weight:600;font-size:13px;">{{ $order->customer_name }}</div>
              @if($order->company_name)<div style="font-size:12px;color:var(--muted);">{{ $order->company_name }}</div>@endif
              <div style="font-size:12px;color:var(--muted);">{{ $order->customer_email }}</div>
            </td>
            <td style="font-size:13px;text-align:center;">{{ $order->items_count ?? $order->items()->count() }}</td>
            <td style="font-weight:700;font-size:13px;color:var(--navy);">
              {{ $order->total_amount > 0 ? $order->total_amount_inr : '—' }}
            </td>
            <td><span class="badge {{ $sc }}">{{ \App\Models\Order::statusLabel($order->status) }}</span></td>
            <td><span class="badge {{ $pc }}">{{ \App\Models\Order::paymentLabel($order->payment_status) }}</span></td>
            <td style="font-size:12px;color:var(--muted);">{{ $order->created_at->format('d M Y') }}</td>
            <td>
              <a href="{{ route('adminhu.orders.show', $order) }}" class="btn btn-ghost btn-sm">View →</a>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--muted);font-size:13px;">
            No orders yet. Convert a won quote to get started.
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($orders->hasPages())
      <div style="padding:12px 16px;border-top:1px solid var(--border);">{{ $orders->links() }}</div>
    @endif
  </div>

</div>
@endsection
