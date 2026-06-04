@extends('admin.layouts.app')
@section('title', $menu->name.' — Items')

@push('styles')
<style>
.menu-item-row { display:grid; grid-template-columns:24px 1fr auto auto auto; gap:8px; align-items:center;
  padding:8px 12px; border-bottom:1px solid var(--border); }
.menu-item-row:last-child { border-bottom:none; }
.drag-handle { cursor:grab; color:var(--muted); display:flex; align-items:center; }
.drag-handle:active { cursor:grabbing; }
.item-type-badge { font-size:10px; font-weight:700; padding:1px 6px; border-radius:3px; background:var(--bg); border:1px solid var(--border); color:var(--muted); }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">{{ $menu->name }}</div>
    <div class="page-sub">{{ $items->count() }} items &nbsp;·&nbsp; Location: <strong>{{ $menu->location }}</strong></div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.menus.index') }}" class="btn btn-ghost">← Menus</a>
  </div>
</div>

<div class="page-body">
  <div style="display:grid;grid-template-columns:1fr 320px;gap:14px;align-items:start;">

    {{-- Items list --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title">Menu items</div>
        <div style="font-size:12px;color:var(--muted);">Drag to reorder</div>
      </div>

      @if($items->isEmpty())
        <div class="empty-state" style="padding:40px;"><div class="t">No items yet</div><div class="s">Add items using the form →</div></div>
      @else
        <div id="sortable-list">
          @foreach($items as $item)
          <div class="menu-item-row" data-id="{{ $item->id }}">
            <div class="drag-handle">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="5" r="1" fill="currentColor"/><circle cx="9" cy="12" r="1" fill="currentColor"/><circle cx="9" cy="19" r="1" fill="currentColor"/><circle cx="15" cy="5" r="1" fill="currentColor"/><circle cx="15" cy="12" r="1" fill="currentColor"/><circle cx="15" cy="19" r="1" fill="currentColor"/></svg>
            </div>
            <div>
              <div style="font-weight:600;">{{ $item->label }}</div>
              <div style="font-size:11px;color:var(--muted);">
                @if($item->type === 'page') /{{ $item->page?->slug ?? '—' }}
                @elseif($item->type === 'category') shop/{{ $item->category?->slug ?? '—' }}
                @else {{ $item->url ?: '—' }}
                @endif
              </div>
            </div>
            <span class="item-type-badge">{{ $item->type }}</span>
            @if($item->open_new_tab)<span style="font-size:10px;color:var(--muted);" title="Opens in new tab">↗</span>@endif
            <form method="POST" action="{{ route('adminhu.menus.items.destroy', [$menu, $item]) }}">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-icon btn-icon sm"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
            </form>
          </div>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Add item form --}}
    <div class="card" x-data="{ type: 'url' }">
      <div class="card-header"><div class="card-title">Add item</div></div>
      <div class="card-body">
        <form method="POST" action="{{ route('adminhu.menus.items.store', $menu) }}" style="display:flex;flex-direction:column;gap:12px;">
          @csrf
          <div class="field">
            <label>Label <span class="req">*</span></label>
            <input class="input" type="text" name="label" value="{{ old('label') }}" required placeholder="e.g. About Us">
          </div>

          <div class="field">
            <label>Type</label>
            <select class="select" name="type" x-model="type" style="width:100%;">
              <option value="url">Custom URL</option>
              <option value="page">Static page</option>
              <option value="category">Product category</option>
            </select>
          </div>

          <div class="field" x-show="type === 'url'">
            <label>URL</label>
            <input class="input" type="text" name="url" value="{{ old('url') }}" placeholder="/about">
          </div>

          <div class="field" x-show="type === 'page'">
            <label>Page</label>
            <select class="select" name="page_id" style="width:100%;">
              <option value="">— Select page —</option>
              @foreach($pages as $pg)
                <option value="{{ $pg->id }}" {{ old('page_id') == $pg->id ? 'selected' : '' }}>{{ $pg->title }}</option>
              @endforeach
            </select>
          </div>

          <div class="field" x-show="type === 'category'">
            <label>Category</label>
            <select class="select" name="category_id" style="width:100%;">
              <option value="">— Select category —</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>

          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;">
            <input type="checkbox" name="open_new_tab" value="1" style="width:15px;height:15px;">
            Open in new tab
          </label>

          <button type="submit" class="btn btn-primary">Add item</button>
        </form>
      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
const list = document.getElementById('sortable-list');
if (list) {
  Sortable.create(list, {
    handle: '.drag-handle',
    animation: 150,
    onEnd() {
      const order = [...list.querySelectorAll('[data-id]')].map(el => el.dataset.id);
      fetch('{{ route('adminhu.menus.items.reorder', $menu) }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ order })
      });
    }
  });
}
</script>
@endpush
