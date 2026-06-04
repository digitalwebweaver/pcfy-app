<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quote {{ $quote->quote_number }} — pcfy.in</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; background: #fff; }

  .header { background: #051e3a; color: #fff; padding: 24px 32px; display: flex; justify-content: space-between; align-items: flex-start; }
  .logo { font-size: 26px; font-weight: 700; color: #fff; letter-spacing: -0.5px; }
  .logo span { color: #f5a623; }
  .logo-tag { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 2px; }
  .quote-badge { text-align: right; }
  .quote-badge .label { font-size: 9px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: rgba(255,255,255,0.5); }
  .quote-badge .number { font-size: 20px; font-weight: 700; font-family: monospace; color: #f5a623; letter-spacing: 1px; margin-top: 4px; }
  .quote-badge .date { font-size: 10px; color: rgba(255,255,255,0.6); margin-top: 3px; }

  .body { padding: 28px 32px; }

  .two-col { display: flex; gap: 24px; margin-bottom: 24px; }
  .col { flex: 1; }
  .section-title { font-size: 9px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #6b7280; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
  .field { margin-bottom: 6px; font-size: 12px; }
  .field .label { color: #6b7280; font-size: 10.5px; margin-bottom: 1px; }
  .field .val { font-weight: 600; color: #111827; }

  .status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: 700; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }

  table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  thead th { background: #f8fafc; padding: 9px 12px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
  tbody td { padding: 10px 12px; border-bottom: 1px solid #f3f4f6; font-size: 12px; vertical-align: middle; }
  tbody tr:last-child td { border-bottom: 2px solid #e5e7eb; }
  .grade-a { color: #16a34a; font-weight: 700; }
  .grade-b { color: #d97706; font-weight: 700; }
  .grade-c { color: #ea580c; font-weight: 700; }

  .totals { display: flex; justify-content: flex-end; }
  .totals-box { width: 260px; }
  .total-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f3f4f6; font-size: 12px; }
  .total-row.grand { font-weight: 700; font-size: 14px; border-top: 2px solid #051e3a; border-bottom: none; padding-top: 8px; color: #051e3a; }
  .total-row .pending { color: #6b7280; font-style: italic; }

  .notes-box { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 14px; margin-bottom: 20px; font-size: 12px; line-height: 1.6; }
  .notes-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #6b7280; margin-bottom: 6px; }

  .footer { background: #f8fafc; border-top: 2px solid #e5e7eb; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; }
  .footer-left { font-size: 10.5px; color: #6b7280; line-height: 1.7; }
  .footer-right { text-align: right; font-size: 10.5px; color: #6b7280; line-height: 1.7; }

  .watermark { font-size: 10px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #d1d5db; margin-bottom: 8px; }
</style>
</head>
<body>

<div class="header">
  <div>
    <div class="logo">pcfy<span>.in</span></div>
    <div class="logo-tag">Everything your system needs</div>
    <div style="margin-top:12px;font-size:10px;color:rgba(255,255,255,0.5);line-height:1.7;">
      Vadodara, Gujarat — 390001<br>
      hello@pcfy.in · +91 98765 43210
    </div>
  </div>
  <div class="quote-badge">
    <div class="label">Quote Reference</div>
    <div class="number">{{ $quote->quote_number }}</div>
    <div class="date">{{ $quote->created_at->format('d M Y') }}</div>
    <div style="margin-top:8px;">
      <span class="status-badge">{{ \App\Models\Quote::statusLabel($quote->status) }}</span>
    </div>
  </div>
</div>

<div class="body">

  <div class="two-col">
    <div class="col">
      <div class="section-title">Quote For</div>
      <div class="field"><div class="val" style="font-size:14px;">{{ $quote->customer_name }}</div></div>
      @if($quote->company_name)<div class="field"><div class="val">{{ $quote->company_name }}</div></div>@endif
      <div class="field"><div class="label">Email</div><div class="val">{{ $quote->customer_email }}</div></div>
      @if($quote->customer_phone)<div class="field"><div class="label">Phone</div><div class="val">{{ $quote->customer_phone }}</div></div>@endif
      @if($quote->gstin)<div class="field"><div class="label">GSTIN</div><div class="val" style="font-family:monospace;">{{ $quote->gstin }}</div></div>@endif
    </div>
    <div class="col">
      <div class="section-title">Quote Details</div>
      <div class="field"><div class="label">Quote Number</div><div class="val" style="font-family:monospace;">{{ $quote->quote_number }}</div></div>
      <div class="field"><div class="label">Date</div><div class="val">{{ $quote->created_at->format('d F Y') }}</div></div>
      <div class="field"><div class="label">Status</div><div class="val">{{ \App\Models\Quote::statusLabel($quote->status) }}</div></div>
      @if($quote->assignedTo)<div class="field"><div class="label">Account Manager</div><div class="val">{{ $quote->assignedTo->name }}</div></div>@endif
      <div class="field"><div class="label">Valid For</div><div class="val">7 days from quote date</div></div>
    </div>
  </div>

  {{-- Items table --}}
  <div class="section-title" style="margin-bottom:10px;">Quoted Items</div>
  <table>
    <thead>
      <tr>
        <th style="width:40%;">Product</th>
        <th>Grade</th>
        <th style="text-align:center;">Qty</th>
        <th style="text-align:right;">Unit Price</th>
        <th style="text-align:right;">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($quote->items as $item)
      <tr>
        <td>
          <div style="font-weight:600;">{{ $item->product_name }}</div>
          @if($item->product_sku)<div style="font-size:10px;color:#6b7280;font-family:monospace;">{{ $item->product_sku }}</div>@endif
          @if($item->notes)<div style="font-size:10.5px;color:#6b7280;font-style:italic;margin-top:2px;">{{ $item->notes }}</div>@endif
        </td>
        <td class="grade-{{ strtolower($item->grade) }}">Grade {{ $item->grade }}</td>
        <td style="text-align:center;">{{ $item->quantity }}</td>
        <td style="text-align:right;">{{ $item->unit_price ? $item->unit_price_inr : '—' }}</td>
        <td style="text-align:right;font-weight:600;">{{ $item->line_total > 0 ? $item->line_total_inr : '—' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{-- Totals --}}
  <div class="totals">
    <div class="totals-box">
      @php $hasAllPrices = $quote->items->every(fn($i) => $i->unit_price > 0); @endphp
      @if($hasAllPrices)
        @php $subtotal = $quote->items->sum('line_total'); @endphp
        <div class="total-row">
          <span>Subtotal</span>
          <span>{{ '₹' . number_format($subtotal/100, 0, '.', ',') }}</span>
        </div>
        <div class="total-row">
          <span>GST (as applicable)</span>
          <span>Per invoice</span>
        </div>
        <div class="total-row grand">
          <span>Estimated Total</span>
          <span>{{ '₹' . number_format($subtotal/100, 0, '.', ',') }}+</span>
        </div>
      @else
        <div class="total-row">
          <span style="color:#6b7280;">Pricing</span>
          <span class="pending">To be confirmed</span>
        </div>
      @endif
    </div>
  </div>

  @if($quote->message)
  <div class="notes-box">
    <div class="notes-title">Customer Notes</div>
    {{ $quote->message }}
  </div>
  @endif

  <div class="notes-box" style="background:#eff3fb;border-color:#c7d7f5;">
    <div class="notes-title" style="color:#0f4c8a;">Terms & Conditions</div>
    1. All prices are exclusive of GST unless stated. · 2. Prices valid for 7 days from quote date. · 3. Availability subject to stock at time of order. · 4. 6-month warranty included on all certified refurbished hardware. · 5. Payment terms: 100% advance for new customers; Net-30 for approved B2B accounts.
  </div>

</div>

<div class="footer">
  <div class="footer-left">
    <strong>pcfy.in</strong> — Certified Refurbished Hardware<br>
    ISO Certified · QC Certified · E-Waste Certified<br>
    GST Registration: 24AAAAA0000A1Z5
  </div>
  <div class="footer-right">
    hello@pcfy.in<br>
    +91 98765 43210<br>
    www.pcfy.in
  </div>
</div>

</body>
</html>
