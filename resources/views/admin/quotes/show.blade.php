@extends('admin.layouts.app')
@section('title', 'Quote ' . $quote->quote_number)

@push('styles')
<style>
.thread-item { border-left: 3px solid var(--border); padding: 12px 16px; margin-bottom: 10px; border-radius: 0 8px 8px 0; background: var(--surface); }
.thread-item.email    { border-left-color: var(--navy); }
.thread-item.internal { border-left-color: var(--accent); background: #fffbeb; }
.thread-meta { font-size: 11.5px; color: var(--muted); margin-bottom: 6px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.thread-body { font-size: 13.5px; line-height: 1.65; white-space: pre-wrap; color: var(--text); }
.price-input { height: 30px; padding: 0 8px; border: 1.5px solid var(--border); border-radius: 5px; font-size: 12.5px; font-family: inherit; width: 110px; outline: none; }
.price-input:focus { border-color: var(--navy); }
</style>
@endpush

@section('content')

<div class="page-header">
  <div>
    <div class="page-title" style="display:flex;align-items:center;gap:10px;">
      <span style="font-family:'DM Mono',monospace;font-size:16px;">{{ $quote->quote_number }}</span>
      @php
        $sc = match($quote->status) {
          'new'=>'status-new','reviewing'=>'status-reviewing','quoted'=>'status-quoted',
          'won'=>'status-won','lost'=>'status-lost', default=>'badge-gray'
        };
      @endphp
      <span class="badge {{ $sc }}">{{ \App\Models\Quote::statusLabel($quote->status) }}</span>
    </div>
    <div class="page-sub">
      {{ $quote->customer_name }}
      @if($quote->company_name) · {{ $quote->company_name }} @endif
      · Submitted {{ $quote->created_at->diffForHumans() }}
    </div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.quotes.index') }}" class="btn btn-ghost">← All quotes</a>
    @if($quote->status === 'won')
      @if($quote->order)
        <a href="{{ route('adminhu.orders.show', $quote->order) }}" class="btn btn-primary" style="background:#16a34a;border-color:#16a34a;">
          <svg viewBox="0 0 24 24" style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
          View Order {{ $quote->order->order_number }}
        </a>
      @else
        <form method="POST" action="{{ route('adminhu.quotes.convert', $quote) }}" onsubmit="return confirm('Convert this won quote to an order?')">
          @csrf
          <button type="submit" class="btn btn-primary" style="background:#16a34a;border-color:#16a34a;">
            <svg viewBox="0 0 24 24" style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/></svg>
            Convert to Order
          </button>
        </form>
      @endif
    @endif
    <form method="POST" action="{{ route('adminhu.quotes.duplicate', $quote) }}">
      @csrf
      <button type="submit" class="btn btn-ghost" title="Clone this quote as a new draft">
        <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
        Duplicate
      </button>
    </form>
    <a href="{{ route('adminhu.quotes.pdf', $quote) }}" class="btn btn-ghost" target="_blank">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
      Download PDF
    </a>
  </div>
</div>

<div class="page-body">
<div style="display:grid;grid-template-columns:1fr 300px;gap:14px;align-items:start;">

  {{-- LEFT: Items + Thread --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    {{-- Customer info --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title">Customer</div>
        <a href="{{ $quote->whatsapp_url }}" target="_blank" class="btn btn-ghost btn-sm" style="color:#25d366;border-color:#25d366;">
          <svg viewBox="0 0 24 24" style="width:13px;height:13px;fill:currentColor;stroke:none;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.533 5.855L0 24l6.335-1.652C8.044 23.419 9.974 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.846 0-3.574-.49-5.072-1.348l-.361-.214-3.764.982.998-3.662-.234-.376A9.93 9.93 0 012 12c0-5.514 4.486-10 10-10s10 4.486 10 10-4.486 10-10 10z"/></svg>
          WhatsApp
        </a>
      </div>
      <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13px;">
          <div><span style="color:var(--muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:2px;">Name</span>{{ $quote->customer_name }}</div>
          <div><span style="color:var(--muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:2px;">Email</span><a href="mailto:{{ $quote->customer_email }}" style="color:var(--navy);">{{ $quote->customer_email }}</a></div>
          @if($quote->customer_phone)<div><span style="color:var(--muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:2px;">Phone</span><a href="tel:{{ $quote->customer_phone }}" style="color:var(--navy);">{{ $quote->customer_phone }}</a></div>@endif
          @if($quote->company_name)<div><span style="color:var(--muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:2px;">Company</span>{{ $quote->company_name }}</div>@endif
          @if($quote->gstin)<div><span style="color:var(--muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:2px;">GSTIN</span><span style="font-family:'DM Mono',monospace;">{{ $quote->gstin }}</span></div>@endif
          @if($quote->message)<div style="grid-column:span 2;"><span style="color:var(--muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:4px;">Message</span><div style="background:var(--bg);padding:10px 12px;border-radius:6px;font-size:12.5px;line-height:1.6;white-space:pre-wrap;">{{ $quote->message }}</div></div>@endif
        </div>
      </div>
    </div>

    {{-- Items + pricing --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title">Quoted Items</div>
        @if($quote->estimated_total)
          <span style="font-size:14px;font-weight:700;color:var(--navy);">Total: {{ $quote->estimated_total_inr }}</span>
        @endif
      </div>
      <form action="{{ route('adminhu.quotes.items', $quote) }}" method="POST">
        @csrf
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Product</th>
                <th>Grade</th>
                <th style="width:80px;">Qty</th>
                <th style="width:130px;">Unit price (₹)</th>
                <th style="width:110px;">Line total</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              @foreach($quote->items as $i => $item)
              <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
              <tr>
                <td>
                  <div style="font-weight:600;font-size:13px;">{{ $item->product_name }}</div>
                  @if($item->product_sku)<div style="font-size:11px;color:var(--muted);font-family:'DM Mono',monospace;">{{ $item->product_sku }}</div>@endif
                </td>
                <td><span class="badge {{ $item->grade==='A'?'badge-green':($item->grade==='B'?'badge-amber':'badge-gray') }}">{{ $item->grade }}</span></td>
                <td><input type="number" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}" min="1" max="999" class="price-input" style="width:60px;"></td>
                <td>
                  <div style="display:flex;align-items:center;gap:4px;">
                    <span style="font-size:12px;color:var(--muted);">₹</span>
                    <input type="number" name="items[{{ $i }}][unit_price]"
                           value="{{ $item->unit_price ? $item->unit_price/100 : '' }}"
                           min="0" step="0.01" placeholder="—"
                           class="price-input">
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
        <div style="padding:12px 16px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;">
          <button type="submit" class="btn btn-primary">Save prices &amp; quantities</button>
        </div>
      </form>
    </div>

    {{-- Response thread --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Response Thread</div></div>
      <div style="padding:14px 16px;">

        {{-- Existing responses --}}
        @forelse($quote->responses as $r)
        <div class="thread-item {{ $r->is_internal ? 'internal' : 'email' }}">
          <div class="thread-meta">
            <span style="font-weight:700;color:var(--text);">{{ $r->user?->name ?? 'System' }}</span>
            <span class="badge {{ $r->is_internal ? 'badge-amber' : 'badge-navy' }}" style="font-size:9px;">
              {{ $r->is_internal ? 'Internal note' : 'Email sent' }}
            </span>
            <span>{{ $r->created_at->format('d M Y, g:ia') }}</span>
          </div>
          @if($r->subject && !$r->is_internal)
            <div style="font-size:12px;font-weight:700;margin-bottom:4px;">{{ $r->subject }}</div>
          @endif
          <div class="thread-body">{{ $r->body }}</div>
          @if($r->attachment_name)
            <div style="margin-top:8px;display:inline-flex;align-items:center;gap:6px;padding:5px 10px;background:var(--bg);border:1px solid var(--border);border-radius:6px;font-size:12px;color:var(--muted);">
              <svg viewBox="0 0 24 24" style="width:13px;height:13px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
              {{ $r->attachment_name }}
            </div>
          @endif
        </div>
        @empty
        <div style="color:var(--muted);font-size:13px;padding:10px 0;">No responses yet. Send the first reply below.</div>
        @endforelse

        {{-- New response form --}}
        <div style="margin-top:16px;border-top:1px solid var(--border);padding-top:16px;" x-data="{type:'email',fileName:''}">
          <form action="{{ route('adminhu.quotes.respond', $quote) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="display:flex;gap:8px;margin-bottom:12px;">
              <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                <input type="radio" name="type" value="email" x-model="type" checked> Email to customer
              </label>
              <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                <input type="radio" name="type" value="internal_note" x-model="type"> Internal note
              </label>
            </div>

            <div x-show="type === 'email'" style="margin-bottom:10px;">
              <input class="input" type="text" name="subject"
                     placeholder="Subject (optional — defaults to Re: Quote {{ $quote->quote_number }})"
                     style="margin-bottom:8px;">
            </div>

            <textarea class="textarea" name="body" rows="5" required
                      :placeholder="type === 'email' ? 'Write your response to the customer…' : 'Internal note (not visible to customer)…'"
                      style="margin-bottom:10px;"></textarea>

            {{-- File attachment (email only) --}}
            <div x-show="type === 'email'" style="margin-bottom:12px;">
              <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;">
                Attach file <span style="font-weight:400;text-transform:none;">(PDF, Word, Excel, image — max 10 MB)</span>
              </label>
              <label style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;padding:8px 14px;border:1.5px dashed var(--border);border-radius:8px;font-size:13px;color:var(--text);transition:border-color .15s;"
                     x-bind:style="fileName ? 'border-color:var(--navy);background:var(--bg2);' : ''">
                <svg viewBox="0 0 24 24" style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/>
                </svg>
                <span x-text="fileName || 'Choose file to attach…'"></span>
                <input type="file" name="attachment" style="display:none;"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.png,.jpg,.jpeg,.zip"
                       @change="fileName = $event.target.files[0]?.name || ''">
              </label>
              <template x-if="fileName">
                <button type="button" @click="fileName=''; $el.closest('div').querySelector('input[type=file]').value=''"
                        style="margin-left:8px;font-size:12px;color:var(--muted);background:none;border:none;cursor:pointer;text-decoration:underline;">
                  Remove
                </button>
              </template>
            </div>

            <button type="submit" class="btn btn-primary" x-text="type === 'email' ? '📧 Send email to customer' : '📝 Add internal note'">
              Send email to customer
            </button>
          </form>
        </div>

      </div>
    </div>

  </div>

  {{-- RIGHT: Status + Assign + Internal notes --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    {{-- Status --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Status</div></div>
      <div class="card-body">
        <form action="{{ route('adminhu.quotes.status', $quote) }}" method="POST">
          @csrf @method('PATCH')
          <select class="select" name="status" onchange="this.form.submit()" style="margin-bottom:0;">
            @foreach(\App\Models\Quote::statuses() as $s)
              <option value="{{ $s }}" {{ $quote->status === $s ? 'selected' : '' }}>
                {{ \App\Models\Quote::statusLabel($s) }}
              </option>
            @endforeach
          </select>
        </form>
        <div style="margin-top:10px;font-size:12px;color:var(--muted);">
          Created: {{ $quote->created_at->format('d M Y, g:ia') }}<br>
          @if($quote->last_replied_at)Last reply: {{ $quote->last_replied_at->diffForHumans() }}@endif
        </div>
      </div>
    </div>

    {{-- Assign --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Assigned to</div></div>
      <div class="card-body">
        <form action="{{ route('adminhu.quotes.assign', $quote) }}" method="POST">
          @csrf
          <select class="select" name="assigned_to" onchange="this.form.submit()">
            <option value="">— Unassigned —</option>
            @foreach($users as $u)
              <option value="{{ $u->id }}" {{ $quote->assigned_to == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
          </select>
        </form>
      </div>
    </div>

    {{-- Quick actions --}}
    <div class="card">
      <div class="card-header"><div class="card-title">Quick actions</div></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:8px;">
        <a href="{{ route('adminhu.quotes.pdf', $quote) }}" target="_blank" class="btn btn-ghost" style="justify-content:flex-start;">
          <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/></svg>
          Download PDF quote
        </a>
        <a href="{{ $quote->whatsapp_url }}" target="_blank" class="btn btn-ghost" style="justify-content:flex-start;color:#25d366;border-color:#25d366;">
          <svg viewBox="0 0 24 24" style="fill:currentColor;stroke:none;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.533 5.855L0 24l6.335-1.652C8.044 23.419 9.974 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.846 0-3.574-.49-5.072-1.348l-.361-.214-3.764.982.998-3.662-.234-.376A9.93 9.93 0 012 12c0-5.514 4.486-10 10-10s10 4.486 10 10-4.486 10-10 10z"/></svg>
          WhatsApp customer
        </a>
        <a href="mailto:{{ $quote->customer_email }}?subject=Re: Quote {{ $quote->quote_number }}" class="btn btn-ghost" style="justify-content:flex-start;">
          <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          Email directly
        </a>
        <form action="{{ route('adminhu.quotes.destroy', $quote) }}" method="POST" onsubmit="return confirm('Delete this quote permanently?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger" style="width:100%;justify-content:flex-start;">
            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
            Delete quote
          </button>
        </form>
      </div>
    </div>

  </div>

</div>
</div>
@endsection
