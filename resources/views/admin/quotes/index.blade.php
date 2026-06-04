@extends('admin.layouts.app')
@section('title', 'Quotes')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Quotes</div>
    <div class="page-sub">
      {{ $counts['all'] }} total &nbsp;·&nbsp;
      <span style="color:var(--red);">{{ $counts['new'] }} new</span>
    </div>
  </div>
  <div class="page-actions">
    <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-ghost">
      <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
      Export CSV
    </a>
  </div>
</div>

<div class="page-body">

  {{-- Status tabs --}}
  <div class="tabs" style="margin-bottom:14px;">
    @foreach(['all' => 'All', 'new' => 'New', 'reviewing' => 'Reviewing', 'quoted' => 'Quoted', 'won' => 'Won', 'lost' => 'Lost'] as $s => $label)
    <a href="{{ request()->fullUrlWithQuery(['status' => $s, 'page' => null]) }}"
       class="tab {{ $status === $s ? 'active' : '' }}" style="text-decoration:none;">
      {{ $label }}
      <span style="margin-left:5px;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:0 6px;font-size:10.5px;font-weight:700;color:var(--muted);">
        {{ $counts[$s] }}
      </span>
    </a>
    @endforeach
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('adminhu.quotes.index') }}" style="margin-bottom:12px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <input type="hidden" name="status" value="{{ $status }}">
    <input class="input" type="text" name="q" value="{{ request('q') }}"
           placeholder="Search name, email, company, quote #…" style="width:260px;">
    <input class="input" type="date" name="from" value="{{ request('from') }}" style="width:140px;">
    <input class="input" type="date" name="to" value="{{ request('to') }}" style="width:140px;">
    <select class="select" name="assigned_to" style="width:auto;">
      <option value="">All agents</option>
      @foreach($users as $u)
        <option value="{{ $u->id }}" {{ request('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-ghost">Filter</button>
    @if(request()->anyFilled(['q','from','to','assigned_to']))
      <a href="{{ route('adminhu.quotes.index', ['status' => $status]) }}" class="btn btn-ghost" style="color:var(--red);">Clear</a>
    @endif
  </form>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Quote #</th>
            <th>Customer</th>
            <th>Company</th>
            <th>Items</th>
            <th>Status</th>
            <th>Assigned</th>
            <th>Date</th>
            <th style="width:60px;"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($quotes as $quote)
          <tr style="cursor:pointer;" onclick="location.href='{{ route('adminhu.quotes.show', $quote) }}'">
            <td>
              <span style="font-family:'DM Mono',monospace;font-size:12px;font-weight:700;color:var(--navy);">
                {{ $quote->quote_number }}
              </span>
            </td>
            <td>
              <div style="font-weight:600;">{{ $quote->customer_name }}</div>
              <div style="font-size:11.5px;color:var(--muted);">{{ $quote->customer_email }}</div>
            </td>
            <td style="color:var(--muted);">{{ $quote->company_name ?: '—' }}</td>
            <td>
              <span class="badge badge-gray">{{ $quote->items_count }} item{{ $quote->items_count != 1 ? 's' : '' }}</span>
            </td>
            <td>
              @php
                $sc = match($quote->status) {
                  'new'       => 'status-new',
                  'reviewing' => 'status-reviewing',
                  'quoted'    => 'status-quoted',
                  'won'       => 'status-won',
                  'lost'      => 'status-lost',
                  default     => 'badge-gray',
                };
              @endphp
              <span class="badge {{ $sc }}">{{ \App\Models\Quote::statusLabel($quote->status) }}</span>
            </td>
            <td style="font-size:12px;color:var(--muted);">
              {{ $quote->assignedTo?->name ?? '—' }}
            </td>
            <td style="font-size:11.5px;color:var(--muted);">
              {{ $quote->created_at->format('d M Y') }}<br>
              {{ $quote->created_at->format('g:ia') }}
            </td>
            <td onclick="event.stopPropagation()">
              <a href="{{ route('adminhu.quotes.show', $quote) }}" class="btn btn-ghost btn-icon btn-icon sm" title="View">
                <svg viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
              </a>
            </td>
          </tr>
          @empty
          <tr><td colspan="8">
            <div class="empty-state" style="padding:40px;">
              <svg viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6a1 1 0 01.7.3l5.4 5.4a1 1 0 01.3.7V19a2 2 0 01-2 2z"/></svg>
              <div class="t">No quotes {{ $status !== 'all' ? "with status \"$status\"" : '' }}</div>
              <div class="s">Quotes submitted from the website will appear here.</div>
            </div>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($quotes->hasPages())
    <div style="padding:10px 16px;border-top:1px solid var(--border);">
      {{ $quotes->appends(request()->query())->links() }}
    </div>
    @endif
  </div>

</div>
@endsection

@php use App\Models\Quote; @endphp
