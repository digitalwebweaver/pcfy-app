@extends('admin.layouts.app')
@section('title', 'Import Products')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Import Products</div>
    <div class="page-sub">Bulk upload via CSV or Excel file</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.products.sample') }}" class="btn btn-ghost">
      <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
      Download sample CSV
    </a>
    <a href="{{ route('adminhu.products.index') }}" class="btn btn-ghost">← Back to products</a>
  </div>
</div>

<div class="page-body">

  @if(session('import_result'))
    @php $res = session('import_result'); @endphp
    <div class="alert {{ $res['type'] === 'partial' ? 'alert-warning' : 'alert-success' }}" style="margin-bottom:14px;">
      <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
      {{ $res['message'] }}
    </div>
    @if(!empty($res['errors']))
      <div class="card" style="margin-bottom:14px;">
        <div class="card-header"><div class="card-title" style="color:var(--red);">Row errors</div></div>
        <div class="card-body">
          @foreach($res['errors'] as $err)
            <div style="font-size:12px;color:var(--red);padding:3px 0;border-bottom:1px solid var(--border);">{{ $err }}</div>
          @endforeach
        </div>
      </div>
    @endif
  @endif

  <div style="display:grid;grid-template-columns:1fr 340px;gap:14px;align-items:start;">

    <div class="card">
      <div class="card-header"><div class="card-title">Upload file</div></div>
      <div class="card-body">
        <form method="POST" action="{{ route('adminhu.products.import') }}" enctype="multipart/form-data">
          @csrf
          <div class="field" style="margin-bottom:16px;">
            <label>CSV / Excel file <span class="req">*</span></label>
            <input class="input" type="file" name="file" accept=".csv,.xlsx,.xls,.txt"
                   style="height:auto;padding:8px 10px;" required>
            <div class="field-hint">Supported: .csv, .xlsx, .xls · Max 10MB</div>
            @error('file')<div class="field-error">{{ $message }}</div>@enderror
          </div>
          <button type="submit" class="btn btn-primary">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
            Start import
          </button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><div class="card-title">CSV column guide</div></div>
      <div class="card-body" style="font-size:12px;">
        <table style="width:100%;">
          <thead><tr><th style="text-align:left;padding:4px 0;border-bottom:1px solid var(--border);">Column</th><th style="text-align:left;padding:4px 0;border-bottom:1px solid var(--border);">Required</th></tr></thead>
          <tbody>
            @foreach([
              ['name',              'Yes', 'Product name'],
              ['sku',               'No',  'Unique SKU — duplicates update existing'],
              ['brand',             'No',  'Brand name (auto-created if new)'],
              ['category',          'No',  'Category name (auto-created if new)'],
              ['grade',             'Yes', 'A, B, or C'],
              ['price',             'Yes', 'Selling price in ₹ (e.g. 14999)'],
              ['mrp',               'No',  'Original MRP in ₹'],
              ['stock_status',      'No',  'in_stock / low_stock / out_of_stock'],
              ['stock_qty',         'No',  'Integer quantity'],
              ['warranty_months',   'No',  'Integer months (default 6)'],
              ['short_description', 'No',  'Max 500 chars'],
              ['quick_specs',       'No',  'Pipe-separated: i5 8th Gen|8GB|256GB'],
              ['is_active',         'No',  '1 = active, 0 = draft'],
              ['is_featured',       'No',  '1 = featured'],
              ['image_url',         'No',  'Public image URL (auto-downloaded)'],
              ['meta_title',        'No',  'SEO title'],
              ['meta_description',  'No',  'SEO description'],
            ] as [$col, $req, $desc])
            <tr>
              <td style="padding:4px 0;border-bottom:1px solid var(--border);font-family:'DM Mono',monospace;font-size:11px;color:var(--navy);">{{ $col }}</td>
              <td style="padding:4px 0;border-bottom:1px solid var(--border);">
                <span class="badge {{ $req === 'Yes' ? 'badge-red' : 'badge-gray' }}" style="font-size:9px;">{{ $req }}</span>
                <span style="color:var(--muted);font-size:11px;margin-left:4px;">{{ $desc }}</span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>
@endsection
