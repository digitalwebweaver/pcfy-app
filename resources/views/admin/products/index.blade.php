@extends('admin.layouts.app')
@section('title', 'Products')

@push('styles')
<style>
/* ── Datatable toolbar ───────────────────────── */
.dt-toolbar{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:10px;}
.dt-toolbar-left{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.dt-toolbar-right{display:flex;align-items:center;gap:8px;}
.dt-search-wrap{position:relative;display:flex;align-items:center;}
.dt-search-wrap svg{position:absolute;left:10px;width:15px;height:15px;stroke:var(--muted);fill:none;stroke-width:2;pointer-events:none;}
.dt-search-wrap input{padding-left:32px;width:260px;}
.dt-search-wrap .clear-search{position:absolute;right:8px;cursor:pointer;color:var(--muted);background:none;border:none;font-size:16px;line-height:1;padding:0;display:none;}
.dt-search-wrap input:not(:placeholder-shown) ~ .clear-search{display:block;}

/* ── Filter chips ────────────────────────────── */
.filter-chips{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px;}
.chip{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;background:var(--navy);color:#fff;border-radius:20px;font-size:11.5px;font-weight:600;text-decoration:none;}
.chip svg{width:11px;height:11px;stroke:#fff;fill:none;stroke-width:2.5;cursor:pointer;}

/* ── Table enhancements ──────────────────────── */
.dt-info{font-size:12.5px;color:var(--muted);}
.dt-footer{display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px;}

/* Sortable headers */
th.sortable{cursor:pointer;user-select:none;white-space:nowrap;}
th.sortable:hover{background:var(--bg2);}
th.sortable .sort-ico{display:inline-flex;flex-direction:column;gap:1px;margin-left:5px;vertical-align:middle;opacity:.35;}
th.sortable.asc .sort-ico,th.sortable.desc .sort-ico{opacity:1;}
th.sortable .sort-ico span{width:0;height:0;border-left:4px solid transparent;border-right:4px solid transparent;}
th.sortable .sort-ico .up{border-bottom:5px solid var(--text);}
th.sortable .sort-ico .dn{border-top:5px solid var(--text);}
th.sortable.asc .sort-ico .dn{opacity:.2;}
th.sortable.desc .sort-ico .up{opacity:.2;}

/* Row hover + checkbox */
tbody tr:hover{background:var(--bg2);}
.cb-col{width:36px;}
input[type=checkbox]{width:15px;height:15px;cursor:pointer;accent-color:var(--navy);}

/* Bulk bar */
.bulk-bar{display:none;align-items:center;gap:10px;padding:8px 14px;background:var(--navy);color:#fff;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:10px;}
.bulk-bar.show{display:flex;}
.bulk-bar button{background:rgba(255,255,255,.15);border:none;color:#fff;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;}
.bulk-bar button:hover{background:rgba(255,255,255,.25);}
.bulk-bar .bulk-close{margin-left:auto;background:none;opacity:.6;font-size:18px;line-height:1;}
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Products</div>
    <div class="page-sub" id="dtSub">
      {{ $counts['total'] }} total &nbsp;·&nbsp;
      {{ $counts['active'] }} active &nbsp;·&nbsp;
      {{ $counts['featured'] }} featured &nbsp;·&nbsp;
      <span style="color:var(--red);">{{ $counts['out'] }} out of stock</span>
    </div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.products.import.form') }}" class="btn btn-ghost">
      <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
      Import CSV
    </a>
    <a href="{{ route('adminhu.products.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
      Add product
    </a>
  </div>
</div>

<div class="page-body">

{{-- Bulk action bar --}}
<div class="bulk-bar" id="bulkBar">
  <span id="bulkCount">0 selected</span>
  <form method="POST" action="{{ route('adminhu.products.index') }}" id="bulkForm">
    @csrf
    <input type="hidden" name="_bulk_action" id="bulkAction">
    <div id="bulkIds"></div>
    <button type="button" onclick="doBulk('activate')">✓ Activate</button>
    <button type="button" onclick="doBulk('deactivate')">○ Deactivate</button>
    <button type="button" onclick="doBulk('delete')" style="background:rgba(239,68,68,.3);">🗑 Delete</button>
  </form>
  <button class="bulk-close" onclick="clearSelection()">✕</button>
</div>

{{-- Main filter form --}}
<form method="GET" action="{{ route('adminhu.products.index') }}" id="filterForm">

  {{-- Toolbar row --}}
  <div class="dt-toolbar">
    <div class="dt-toolbar-left">
      {{-- Live search --}}
      <div class="dt-search-wrap">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4-4"/></svg>
        <input class="input" type="text" name="q" id="dtSearch"
               value="{{ request('q') }}"
               placeholder="Search name or SKU…"
               autocomplete="off">
        <button type="button" class="clear-search" onclick="clearSearch()" title="Clear search">×</button>
      </div>

      {{-- Filters --}}
      <select class="select" name="category_id" style="width:auto;" onchange="this.form.submit()">
        <option value="">All categories</option>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
      </select>
      <select class="select" name="brand_id" style="width:auto;" onchange="this.form.submit()">
        <option value="">All brands</option>
        @foreach($brands as $b)
          <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
        @endforeach
      </select>
      <select class="select" name="grade" style="width:auto;" onchange="this.form.submit()">
        <option value="">All grades</option>
        <option value="A" {{ request('grade')=='A'?'selected':'' }}>Grade A</option>
        <option value="B" {{ request('grade')=='B'?'selected':'' }}>Grade B</option>
        <option value="C" {{ request('grade')=='C'?'selected':'' }}>Grade C</option>
      </select>
      <select class="select" name="stock" style="width:auto;" onchange="this.form.submit()">
        <option value="">All stock</option>
        <option value="in_stock"     {{ request('stock')=='in_stock'?'selected':'' }}>In stock</option>
        <option value="low_stock"    {{ request('stock')=='low_stock'?'selected':'' }}>Low stock</option>
        <option value="out_of_stock" {{ request('stock')=='out_of_stock'?'selected':'' }}>Out of stock</option>
      </select>
      <select class="select" name="active" style="width:auto;" onchange="this.form.submit()">
        <option value="">All status</option>
        <option value="1" {{ request('active')==='1'?'selected':'' }}>Active</option>
        <option value="0" {{ request('active')==='0'?'selected':'' }}>Draft</option>
      </select>

      {{-- Sort --}}
      <input type="hidden" name="sort" id="sortInput" value="{{ request('sort','newest') }}">
      <input type="hidden" name="per_page" id="perPageInput" value="{{ request('per_page', 25) }}">

      @if(request()->anyFilled(['q','category_id','brand_id','grade','stock','active']))
        <a href="{{ route('adminhu.products.index') }}" class="btn btn-ghost" style="color:var(--red);white-space:nowrap;">✕ Clear all</a>
      @endif
    </div>

    <div class="dt-toolbar-right">
      {{-- Sort dropdown --}}
      <select class="select" style="width:auto;" onchange="document.getElementById('sortInput').value=this.value;document.getElementById('filterForm').submit();">
        <option value="newest"     {{ request('sort','newest')=='newest'?'selected':'' }}>Newest first</option>
        <option value="name"       {{ request('sort')=='name'?'selected':'' }}>Name A–Z</option>
        <option value="price_asc"  {{ request('sort')=='price_asc'?'selected':'' }}>Price ↑</option>
        <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>Price ↓</option>
      </select>
      {{-- Per-page --}}
      <div style="display:flex;align-items:center;gap:6px;white-space:nowrap;">
        <span style="font-size:12.5px;color:var(--muted);">Show</span>
        <select class="select" style="width:auto;" onchange="document.getElementById('perPageInput').value=this.value;document.getElementById('filterForm').submit();">
          @foreach([10,25,50,100] as $pp)
            <option value="{{ $pp }}" {{ request('per_page',25)==$pp?'selected':'' }}>{{ $pp }}</option>
          @endforeach
        </select>
        <span style="font-size:12.5px;color:var(--muted);">entries</span>
      </div>
    </div>
  </div>

  {{-- Active filter chips --}}
  @php
    $activeFilters = [];
    if(request('q'))           $activeFilters[] = ['label'=>'Search: "'.request('q').'"',  'remove'=>request()->except('q','page')];
    if(request('category_id')) $activeFilters[] = ['label'=>'Category: '.$categories->find(request('category_id'))?->name, 'remove'=>request()->except('category_id','page')];
    if(request('brand_id'))    $activeFilters[] = ['label'=>'Brand: '.$brands->find(request('brand_id'))?->name, 'remove'=>request()->except('brand_id','page')];
    if(request('grade'))       $activeFilters[] = ['label'=>'Grade: '.request('grade'), 'remove'=>request()->except('grade','page')];
    if(request('stock'))       $activeFilters[] = ['label'=>str_replace('_',' ',ucfirst(request('stock'))), 'remove'=>request()->except('stock','page')];
    if(request('active')!==null && request('active')!=='') $activeFilters[] = ['label'=>request('active')==='1'?'Active only':'Draft only', 'remove'=>request()->except('active','page')];
  @endphp
  @if(count($activeFilters))
  <div class="filter-chips">
    @foreach($activeFilters as $f)
      <a href="{{ route('adminhu.products.index', $f['remove']) }}" class="chip">
        {{ $f['label'] }}
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </a>
    @endforeach
  </div>
  @endif

</form>

{{-- Table card --}}
<div class="card">
  <div class="table-wrap">
    <table id="productsTable">
      <thead>
        <tr>
          <th class="cb-col"><input type="checkbox" id="selectAll" title="Select all"></th>
          <th style="width:52px;">Image</th>
          <th class="sortable {{ request('sort')=='name'?'asc':'' }}" data-sort="name">
            Product
            <span class="sort-ico"><span class="up"></span><span class="dn"></span></span>
          </th>
          <th>SKU</th>
          <th>Grade</th>
          <th class="sortable {{ request('sort')=='price_asc'?'asc':(request('sort')=='price_desc'?'desc':'') }}" data-sort-asc="price_asc" data-sort-desc="price_desc">
            Price
            <span class="sort-ico"><span class="up"></span><span class="dn"></span></span>
          </th>
          <th>Stock</th>
          <th>Status</th>
          <th style="width:72px;"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $product)
        <tr data-id="{{ $product->id }}">
          <td class="cb-col"><input type="checkbox" class="row-cb" value="{{ $product->id }}"></td>
          <td>
            @if($product->thumb)
              <img src="{{ $product->thumb }}" alt="{{ $product->name }}"
                   style="width:44px;height:36px;object-fit:cover;border-radius:5px;border:1px solid var(--border);">
            @else
              <div style="width:44px;height:36px;background:var(--bg);border-radius:5px;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;">
                <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:var(--border-dark,#ccc);fill:none;stroke-width:1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
              </div>
            @endif
          </td>
          <td style="max-width:260px;">
            <a href="{{ route('adminhu.products.edit', $product) }}"
               style="font-weight:600;color:var(--navy);text-decoration:none;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
               title="{{ $product->name }}">{{ $product->name }}</a>
            <div style="font-size:11px;color:var(--muted);">{{ $product->brand?->name }} · {{ $product->category?->name }}</div>
          </td>
          <td style="font-family:'DM Mono',monospace;font-size:11.5px;color:var(--muted);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $product->sku ?? '—' }}</td>
          <td>
            @php $gc = ['A'=>'badge-green','B'=>'badge-amber','C'=>'badge-gray'][$product->grade] ?? 'badge-gray'; @endphp
            <span class="badge {{ $gc }}">{{ $product->grade }}</span>
          </td>
          <td>
            <div style="font-weight:700;font-size:13px;">{{ $product->price_inr }}</div>
            @if($product->mrp)<div style="font-size:11px;color:var(--muted);text-decoration:line-through;">{{ $product->mrp_inr }}</div>@endif
          </td>
          <td>
            @php
              $sc = match($product->stock_status) { 'in_stock'=>'badge-green','low_stock'=>'badge-amber','out_of_stock'=>'badge-red',default=>'badge-gray' };
              $sl = match($product->stock_status) { 'in_stock'=>'In stock','low_stock'=>'Low stock','out_of_stock'=>'Out',default=>$product->stock_status };
            @endphp
            <span class="badge {{ $sc }}">{{ $sl }}</span>
          </td>
          <td>
            <span class="badge {{ $product->is_active ? 'badge-green' : 'badge-gray' }}">
              {{ $product->is_active ? 'Active' : 'Draft' }}
            </span>
          </td>
          <td>
            <div style="display:flex;gap:4px;">
              <a href="{{ route('adminhu.products.edit', $product) }}" class="btn btn-ghost btn-icon btn-icon sm" title="Edit">
                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <form method="POST" action="{{ route('adminhu.products.destroy', $product) }}" onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-icon btn-icon sm" title="Delete">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="9">
          <div class="empty-state" style="padding:50px 20px;">
            <svg viewBox="0 0 24 24"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
            <div class="t">No products found</div>
            <div class="s">Try adjusting your filters or <a href="{{ route('adminhu.products.create') }}" style="color:var(--navy);">add a product</a>.</div>
          </div>
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Footer: info + pagination --}}
  <div class="dt-footer">
    <div class="dt-info">
      @if($products->total() > 0)
        Showing
        <strong>{{ $products->firstItem() }}</strong>–<strong>{{ $products->lastItem() }}</strong>
        of <strong>{{ $products->total() }}</strong> products
        @if(request()->anyFilled(['q','category_id','brand_id','grade','stock','active']))
          (filtered)
        @endif
      @else
        No products found
      @endif
    </div>
    @if($products->hasPages())
      {{ $products->appends(request()->query())->links('pagination.default') }}
    @endif
  </div>
</div>

</div>

@push('scripts')
<script>
// ── Live search with 400ms debounce ─────────────────
(function(){
  var inp = document.getElementById('dtSearch');
  var form = document.getElementById('filterForm');
  var timer;
  if (!inp) return;
  inp.addEventListener('input', function(){
    clearTimeout(timer);
    timer = setTimeout(function(){ form.submit(); }, 400);
  });
})();

function clearSearch(){
  document.getElementById('dtSearch').value='';
  document.getElementById('filterForm').submit();
}

// ── Sortable column headers ──────────────────────────
document.querySelectorAll('th.sortable').forEach(function(th){
  th.addEventListener('click', function(){
    var inp = document.getElementById('sortInput');
    var sortAsc  = th.dataset.sort || th.dataset.sortAsc;
    var sortDesc = th.dataset.sortDesc;
    var cur = inp.value;
    if (sortDesc) {
      inp.value = cur === sortAsc ? sortDesc : sortAsc;
    } else {
      inp.value = sortAsc;
    }
    document.getElementById('filterForm').submit();
  });
});

// ── Checkbox selection + bulk bar ───────────────────
var selectAll = document.getElementById('selectAll');
var bulkBar   = document.getElementById('bulkBar');
var bulkCount = document.getElementById('bulkCount');
var bulkIds   = document.getElementById('bulkIds');

function getSelected(){
  return Array.from(document.querySelectorAll('.row-cb:checked')).map(function(cb){ return cb.value; });
}

function updateBulkBar(){
  var sel = getSelected();
  if(sel.length > 0){
    bulkBar.classList.add('show');
    bulkCount.textContent = sel.length + ' selected';
  } else {
    bulkBar.classList.remove('show');
  }
}

selectAll.addEventListener('change', function(){
  document.querySelectorAll('.row-cb').forEach(function(cb){ cb.checked = selectAll.checked; });
  updateBulkBar();
});

document.querySelectorAll('.row-cb').forEach(function(cb){
  cb.addEventListener('change', function(){
    selectAll.checked = document.querySelectorAll('.row-cb').length === getSelected().length;
    updateBulkBar();
  });
});

function clearSelection(){
  document.querySelectorAll('.row-cb').forEach(function(cb){ cb.checked=false; });
  selectAll.checked = false;
  bulkBar.classList.remove('show');
}

function doBulk(action){
  var sel = getSelected();
  if(!sel.length) return;
  var msg = action === 'delete'
    ? 'Delete ' + sel.length + ' product(s)? This cannot be undone.'
    : (action === 'activate' ? 'Activate ' : 'Deactivate ') + sel.length + ' product(s)?';
  if(!confirm(msg)) return;
  document.getElementById('bulkAction').value = action;
  bulkIds.innerHTML = sel.map(function(id){ return '<input type="hidden" name="bulk_ids[]" value="'+id+'">'; }).join('');
  document.getElementById('bulkForm').submit();
}
</script>
@endpush
@endsection
