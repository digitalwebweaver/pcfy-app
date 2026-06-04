@extends('admin.layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
.dash-grid   { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.dash-wide   { grid-column:1/-1; }
.funnel-row  { display:flex; gap:8px; align-items:stretch; flex-wrap:wrap; margin-bottom:0; }
.funnel-card { flex:1; min-width:100px; background:var(--bg); border:1.5px solid var(--border); border-radius:8px; padding:12px 14px; text-align:center; }
.funnel-val  { font-size:26px; font-weight:800; color:var(--navy); }
.funnel-lbl  { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); margin-top:2px; }
.funnel-arrow{ display:flex; align-items:center; color:var(--border); font-size:20px; padding-top:8px; }
.top-bar     { display:flex; align-items:center; gap:8px; margin-bottom:6px; }
.top-bar-fill{ height:8px; border-radius:4px; background:var(--navy); transition:width .4s; }
.top-prod-name{ font-size:12.5px; font-weight:600; flex:1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.top-prod-qty { font-size:11px; color:var(--muted); min-width:40px; text-align:right; }
.pipeline-val { font-size:28px; font-weight:800; color:var(--navy); }
.contact-row  { display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid var(--border); }
.contact-row:last-child { border-bottom:none; }
.contact-init { width:32px; height:32px; border-radius:50%; background:var(--navy); color:#fff; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0; }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Dashboard</div>
    <div class="page-sub">{{ now()->format('l, d M Y') }}</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.quotes.index') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6a1 1 0 01.7.3l5.4 5.4a1 1 0 01.3.7V19a2 2 0 01-2 2z"/></svg>
      All quotes
    </a>
  </div>
</div>

<div class="page-body">

  {{-- Stats --}}
  <div class="stats-grid" style="margin-bottom:14px;">
    <a href="{{ route('adminhu.quotes.index', ['status'=>'new']) }}" class="stat-card" style="text-decoration:none;">
      <div class="stat-icon red"><svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6a1 1 0 01.7.3l5.4 5.4a1 1 0 01.3.7V19a2 2 0 01-2 2z"/></svg></div>
      <div>
        <div class="stat-val">{{ $stats['quotes_new'] }}</div>
        <div class="stat-lbl">New quotes</div>
        <div class="stat-delta {{ $stats['quotes_new'] > 0 ? 'up' : '' }}" style="{{ $stats['quotes_new'] == 0 ? 'color:var(--muted)' : '' }}">
          {{ $stats['quotes_new'] > 0 ? 'Needs attention' : 'All reviewed' }}
        </div>
      </div>
    </a>
    <div class="stat-card">
      <div class="stat-icon green"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
      <div>
        <div class="stat-val">{{ $stats['quotes_won'] }}</div>
        <div class="stat-lbl">Quotes won</div>
        <div class="stat-delta" style="color:var(--muted);">of {{ $stats['quotes_total'] }} total</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon navy"><svg viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg></div>
      <div>
        <div class="stat-val">{{ $stats['products_active'] }}</div>
        <div class="stat-lbl">Active products</div>
        <div class="stat-delta" style="color:var(--muted);">{{ $stats['products_total'] }} total</div>
      </div>
    </div>
    <a href="{{ route('adminhu.contacts.index') }}" class="stat-card" style="text-decoration:none;">
      <div class="stat-icon amber"><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg></div>
      <div>
        <div class="stat-val">{{ $stats['contacts_unread'] }}</div>
        <div class="stat-lbl">Unread contacts</div>
        <div class="stat-delta {{ $stats['contacts_unread'] > 0 ? 'up' : '' }}" style="{{ $stats['contacts_unread'] == 0 ? 'color:var(--muted)' : '' }}">
          {{ $stats['contacts_unread'] > 0 ? 'Needs response' : 'All read' }}
        </div>
      </div>
    </a>
  </div>

  {{-- Pipeline + Funnel row --}}
  <div style="display:grid;grid-template-columns:200px 1fr;gap:14px;margin-bottom:14px;">

    <div class="card">
      <div class="card-header"><div class="card-title">Revenue pipeline</div></div>
      <div class="card-body" style="text-align:center;padding:20px 16px;">
        <div class="pipeline-val">₹{{ number_format($pipeline / 100, 0) }}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:4px;">Reviewing + Quoted</div>
        <div style="margin-top:12px;font-size:11.5px;color:var(--muted);">
          <div>{{ $funnel['reviewing'] ?? 0 }} reviewing</div>
          <div>{{ $funnel['quoted'] ?? 0 }} quoted</div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><div class="card-title">Quote funnel</div></div>
      <div class="card-body">
        <div class="funnel-row">
          @php
            $funnelSteps = ['new'=>'New','reviewing'=>'Reviewing','quoted'=>'Quoted','won'=>'Won','lost'=>'Lost'];
            $funnelColors = ['new'=>'var(--amber)','reviewing'=>'#3b82f6','quoted'=>'var(--navy)','won'=>'#22c55e','lost'=>'#ef4444'];
          @endphp
          @foreach($funnelSteps as $status => $label)
            @if(!$loop->first)<div class="funnel-arrow">›</div>@endif
            <div class="funnel-card">
              <div class="funnel-val" style="color:{{ $funnelColors[$status] }}">{{ $funnel[$status] ?? 0 }}</div>
              <div class="funnel-lbl">{{ $label }}</div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- Chart + Top products --}}
  <div style="display:grid;grid-template-columns:1fr 300px;gap:14px;margin-bottom:14px;">

    <div class="card">
      <div class="card-header"><div class="card-title">Quotes — last 12 months</div></div>
      <div class="card-body" style="padding:16px;">
        <canvas id="quotesChart" height="90"></canvas>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><div class="card-title">Top quoted products</div></div>
      <div class="card-body">
        @if($topProducts->isEmpty())
          <div style="color:var(--muted);font-size:13px;text-align:center;padding:20px 0;">No quote items yet</div>
        @else
          @php $maxQty = $topProducts->max('total_qty') ?: 1; @endphp
          @foreach($topProducts as $tp)
          <div style="margin-bottom:8px;">
            <div style="display:flex;align-items:center;gap:6px;">
              <div class="top-prod-name">{{ $tp->product_name }}</div>
              <div class="top-prod-qty">×{{ $tp->total_qty }}</div>
            </div>
            <div class="top-bar">
              <div class="top-bar-fill" style="width:{{ round(($tp->total_qty / $maxQty) * 100) }}%;"></div>
            </div>
          </div>
          @endforeach
        @endif
      </div>
    </div>
  </div>

  {{-- Recent quotes + Contacts --}}
  <div style="display:grid;grid-template-columns:1fr 320px;gap:14px;">

    <div class="card">
      <div class="card-header">
        <div class="card-title">Recent quotes</div>
        <a href="{{ route('adminhu.quotes.index') }}" class="btn btn-ghost btn-sm">See all</a>
      </div>
      @if($recentQuotes->isEmpty())
        <div class="empty-state" style="padding:30px;"><div class="t">No quotes yet</div></div>
      @else
        <div class="table-wrap">
          <table>
            <thead><tr><th>Quote #</th><th>Customer</th><th>Status</th><th>Items</th><th>Date</th><th></th></tr></thead>
            <tbody>
              @foreach($recentQuotes as $q)
              <tr>
                <td><span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--navy);font-weight:600;">{{ $q->quote_number }}</span></td>
                <td style="font-weight:500;font-size:12.5px;">{{ $q->customer_name }}<br><span style="color:var(--muted);font-size:11px;">{{ $q->company_name }}</span></td>
                <td><span class="badge status-{{ $q->status }}">{{ ucfirst($q->status) }}</span></td>
                <td style="color:var(--muted);font-size:12px;">{{ $q->items->count() }}</td>
                <td style="color:var(--muted);font-size:11px;">{{ $q->created_at->format('d M') }}</td>
                <td><a href="{{ route('adminhu.quotes.show', $q) }}" class="btn btn-ghost btn-icon btn-icon sm"><svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

    <div style="display:flex;flex-direction:column;gap:14px;">

      {{-- Recent contacts --}}
      <div class="card">
        <div class="card-header">
          <div class="card-title">Recent contacts</div>
          <a href="{{ route('adminhu.contacts.index') }}" class="btn btn-ghost btn-sm">See all</a>
        </div>
        <div class="card-body">
          @if($recentContacts->isEmpty())
            <div style="color:var(--muted);font-size:13px;text-align:center;padding:16px 0;">No submissions yet</div>
          @else
            @foreach($recentContacts as $c)
            <div class="contact-row">
              <div class="contact-init">{{ strtoupper(substr($c->name, 0, 2)) }}</div>
              <div style="flex:1;min-width:0;">
                <div style="font-weight:600;font-size:12.5px;display:flex;align-items:center;gap:6px;">
                  {{ $c->name }}
                  @if($c->status === 'unread')<span style="width:7px;height:7px;border-radius:50%;background:var(--amber);display:inline-block;"></span>@endif
                </div>
                <div style="font-size:11px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Str::limit($c->message, 40) }}</div>
              </div>
              <a href="{{ route('adminhu.contacts.show', $c) }}" style="color:var(--muted);">
                <svg viewBox="0 0 24 24" width="14" height="14"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
              </a>
            </div>
            @endforeach
          @endif
        </div>
      </div>

      {{-- Quick actions --}}
      <div class="card">
        <div class="card-header"><div class="card-title">Quick actions</div></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:6px;">
          <a href="{{ route('adminhu.products.create') }}" class="btn btn-ghost" style="justify-content:flex-start;font-size:12.5px;">
            <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Add product
          </a>
          <a href="{{ route('adminhu.blog.create') }}" class="btn btn-ghost" style="justify-content:flex-start;font-size:12.5px;">
            <svg viewBox="0 0 24 24"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> Write blog post
          </a>
          <a href="{{ route('adminhu.quotes.index', ['export'=>'csv']) }}" class="btn btn-ghost" style="justify-content:flex-start;font-size:12.5px;">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg> Export quotes CSV
          </a>
          <a href="{{ route('adminhu.settings.index') }}" class="btn btn-ghost" style="justify-content:flex-start;font-size:12.5px;">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg> Settings
          </a>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('quotesChart'), {
  type: 'line',
  data: {
    labels: {!! json_encode($chartLabels) !!},
    datasets: [{
      label: 'Quotes',
      data: {!! json_encode($chartData) !!},
      borderColor: '#1e3a5f',
      backgroundColor: 'rgba(30,58,95,.08)',
      borderWidth: 2,
      pointBackgroundColor: '#1e3a5f',
      pointRadius: 3,
      fill: true,
      tension: 0.3
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: 'rgba(0,0,0,.05)' } },
      x: { ticks: { font: { size: 10 } }, grid: { display: false } }
    }
  }
});
</script>
@endpush
