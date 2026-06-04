@extends('admin.layouts.app')
@section('title', $product->exists ? 'Edit Product' : 'Add Product')

@push('styles')
<style>
.img-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:10px;margin-top:10px;}
.img-item{position:relative;border-radius:6px;overflow:hidden;border:1.5px solid var(--border);background:var(--bg);aspect-ratio:4/3;cursor:grab;}
.img-item:first-child{border-color:var(--navy);}
.img-item img{width:100%;height:100%;object-fit:cover;}
.img-del{position:absolute;top:4px;right:4px;width:22px;height:22px;background:rgba(0,0,0,.6);border:none;border-radius:50%;color:#fff;font-size:14px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;}
.img-primary{position:absolute;bottom:4px;left:4px;font-size:9px;font-weight:700;background:var(--navy);color:#fff;padding:1px 5px;border-radius:3px;letter-spacing:.04em;}
.spec-row{display:grid;grid-template-columns:140px 160px 1fr 28px;gap:6px;margin-bottom:6px;align-items:center;}
.spec-row input{height:30px;}
.spec-add{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:var(--navy);cursor:pointer;background:none;border:none;margin-top:6px;}
.tabs{margin-bottom:0;}
.tab-content{display:none;}
.tab-content.active{display:block;}
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">{{ $product->exists ? 'Edit: '.$product->name : 'Add Product' }}</div>
    @if($product->exists)
      <div class="page-sub">SKU: {{ $product->sku ?? 'Not set' }} &nbsp;·&nbsp; Created {{ $product->created_at->diffForHumans() }}</div>
    @endif
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.products.index') }}" class="btn btn-ghost">← Back</a>
    @if($product->exists)
    <form method="POST" action="{{ route('adminhu.products.destroy', $product) }}" onsubmit="return confirm('Delete this product permanently?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger">Delete</button>
    </form>
    @endif
  </div>
</div>

<div class="page-body">
<form method="POST"
      action="{{ $product->exists ? route('adminhu.products.update', $product) : route('adminhu.products.store') }}"
      enctype="multipart/form-data"
      x-data="productForm()">
  @csrf
  @if($product->exists) @method('PUT') @endif

  <div style="display:grid;grid-template-columns:1fr 300px;gap:14px;align-items:start;">

    {{-- LEFT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

      {{-- Basic info --}}
      <div class="card">
        <div class="card-header"><div class="card-title">Basic information</div></div>
        <div class="card-body">
          <div class="form-grid" style="gap:12px;">

            <div class="field span-2">
              <label>Product name <span class="req">*</span></label>
              <input class="input {{ $errors->has('name') ? 'error' : '' }}"
                     type="text" name="name" value="{{ old('name', $product->name) }}"
                     required placeholder="e.g. Dell Latitude 5490 Core i5 8th Gen">
              @error('name')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
              <label>SKU</label>
              <input class="input" type="text" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="e.g. LAT-5490-A">
              @error('sku')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
              <label>Grade <span class="req">*</span></label>
              <select class="select" name="grade" required>
                @foreach(['A'=>'A — Like new','B'=>'B — Good condition','C'=>'C — Functional'] as $val=>$lbl)
                  <option value="{{ $val }}" {{ old('grade', $product->grade ?? 'A') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
              </select>
            </div>

            <div class="field">
              <label>Brand</label>
              <select class="select" name="brand_id">
                <option value="">— None —</option>
                @foreach($brands as $brand)
                  <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="field">
              <label>Category</label>
              <select class="select" name="category_id">
                <option value="">— None —</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                    {{ $cat->parent ? $cat->parent->name.' › ' : '' }}{{ $cat->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="field">
              <label>Warranty (months)</label>
              <input class="input" type="number" name="warranty_months" min="0"
                     value="{{ old('warranty_months', $product->warranty_months ?? 6) }}" style="width:120px;">
            </div>

          </div>
        </div>
      </div>

      {{-- Pricing --}}
      <div class="card">
        <div class="card-header"><div class="card-title">Pricing & stock</div></div>
        <div class="card-body">
          <div class="form-grid cols-3" style="gap:12px;">

            <div class="field">
              <label>Selling price (₹) <span class="req">*</span></label>
              <div class="input-group">
                <span style="height:34px;padding:0 10px;background:var(--bg);border:1px solid var(--border-dark);border-right:none;border-radius:4px 0 0 4px;display:flex;align-items:center;font-size:13px;color:var(--muted);">₹</span>
                <input class="input" type="number" name="price_inr" min="0" step="1" required
                       style="border-radius:0 4px 4px 0;"
                       value="{{ old('price_inr', $product->exists ? $product->price/100 : '') }}"
                       placeholder="14999">
              </div>
              @error('price')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div class="field">
              <label>MRP / Original price (₹)</label>
              <div class="input-group">
                <span style="height:34px;padding:0 10px;background:var(--bg);border:1px solid var(--border-dark);border-right:none;border-radius:4px 0 0 4px;display:flex;align-items:center;font-size:13px;color:var(--muted);">₹</span>
                <input class="input" type="number" name="mrp_inr" min="0" step="1"
                       style="border-radius:0 4px 4px 0;"
                       value="{{ old('mrp_inr', $product->exists && $product->mrp ? $product->mrp/100 : '') }}"
                       placeholder="45000">
              </div>
            </div>

            <div class="field">
              <label>Discount</label>
              <div id="discountPreview" style="height:34px;display:flex;align-items:center;font-size:18px;font-weight:700;color:var(--green);">
                @if($product->exists && $product->discount_percent > 0)
                  {{ $product->discount_percent }}% off
                @else
                  —
                @endif
              </div>
            </div>

            <div class="field">
              <label>Stock status <span class="req">*</span></label>
              <select class="select" name="stock_status" required>
                <option value="in_stock"     {{ old('stock_status', $product->stock_status ?? 'in_stock') === 'in_stock'     ? 'selected' : '' }}>In stock</option>
                <option value="low_stock"    {{ old('stock_status', $product->stock_status) === 'low_stock'    ? 'selected' : '' }}>Low stock</option>
                <option value="out_of_stock" {{ old('stock_status', $product->stock_status) === 'out_of_stock' ? 'selected' : '' }}>Out of stock</option>
              </select>
            </div>

            <div class="field">
              <label>Stock quantity</label>
              <input class="input" type="number" name="stock_qty" min="0"
                     value="{{ old('stock_qty', $product->stock_qty ?? 0) }}" style="width:120px;">
            </div>

          </div>
        </div>
      </div>

      {{-- Tabs: Description / Specs / SEO --}}
      <div class="card">
        <div class="tabs" id="formTabs">
          <div class="tab active" onclick="switchTab('desc')">Description</div>
          <div class="tab" onclick="switchTab('specs')">Specs</div>
          <div class="tab" onclick="switchTab('seo')">SEO</div>
        </div>

        <div class="card-body">

          {{-- Description tab --}}
          <div class="tab-content active" id="tab-desc">
            <div class="field" style="margin-bottom:12px;">
              <label>Short description</label>
              <textarea class="textarea" name="short_description" rows="2"
                        placeholder="1–2 lines shown on product cards">{{ old('short_description', $product->short_description) }}</textarea>
              <div class="field-hint">Max 500 characters. Shown on listing cards.</div>
            </div>
            <div class="field">
              <label>Full description</label>
              <textarea class="textarea" id="descEditor" name="description" rows="8"
                        placeholder="Detailed product description (HTML supported)">{{ old('description', $product->description) }}</textarea>
            </div>
          </div>

          {{-- Specs tab --}}
          <div class="tab-content" id="tab-specs">
            <div class="field" style="margin-bottom:16px;">
              <label>Quick specs chips</label>
              <input class="input" type="text" name="quick_specs"
                     value="{{ old('quick_specs', is_array($product->quick_specs) ? implode('|', $product->quick_specs) : '') }}"
                     placeholder="i5 8th Gen|8GB RAM|256GB SSD|14&quot; FHD">
              <div class="field-hint">Pipe-separated values. Shown as chips on product cards (e.g. <strong>i5 8th Gen | 8GB | 256GB SSD</strong>).</div>
            </div>

            <div class="form-section-title">Detailed spec table</div>
            <div style="font-size:11.5px;color:var(--muted);margin-bottom:10px;">
              Group → Label → Value. Group is optional (e.g. "Processor", "Display", "Storage").
            </div>
            <div id="specRows">
              @php $specs = $product->specs_rows ?? collect(); @endphp
              @foreach($specs as $i => $spec)
              <div class="spec-row">
                <input class="input" type="text" name="spec_group[]" value="{{ $spec->group }}" placeholder="Group">
                <input class="input" type="text" name="spec_label[]" value="{{ $spec->label }}" placeholder="Label *">
                <input class="input" type="text" name="spec_value[]" value="{{ $spec->value }}" placeholder="Value *">
                <button type="button" onclick="this.closest('.spec-row').remove()" style="width:26px;height:26px;border-radius:50%;border:none;background:var(--bg);color:var(--muted);cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;">×</button>
              </div>
              @endforeach
            </div>
            <button type="button" class="spec-add" onclick="addSpecRow()">
              <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M12 5v14M5 12h14"/></svg>
              Add spec row
            </button>
          </div>

          {{-- SEO tab --}}
          <div class="tab-content" id="tab-seo">
            <div class="form-grid cols-1" style="gap:12px;">
              <div class="field">
                <label>Meta title</label>
                <input class="input" type="text" name="meta_title"
                       value="{{ old('meta_title', $product->meta_title) }}"
                       placeholder="Defaults to product name">
                <div class="field-hint">Max 60 characters recommended.</div>
              </div>
              <div class="field">
                <label>Meta description</label>
                <textarea class="textarea" name="meta_description" rows="2"
                          placeholder="Max 160 characters">{{ old('meta_description', $product->meta_description) }}</textarea>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>

    {{-- RIGHT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

      {{-- Publish --}}
      <div class="card">
        <div class="card-header"><div class="card-title">Publish</div></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
          <label class="toggle">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
            <div class="toggle-track"></div><div class="toggle-thumb"></div>
            <span class="toggle-label">Active (visible on site)</span>
          </label>
          <label class="toggle">
            <input type="hidden" name="is_featured" value="0">
            <input type="checkbox" name="is_featured" value="1"
                   {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
            <div class="toggle-track"></div><div class="toggle-thumb"></div>
            <span class="toggle-label">Featured product</span>
          </label>
          <div class="field" style="margin-top:4px;">
            <label>Sort order</label>
            <input class="input" type="number" name="sort_order"
                   value="{{ old('sort_order', $product->sort_order ?? 0) }}" min="0" style="width:100px;">
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:4px;">
            {{ $product->exists ? 'Save changes' : 'Create product' }}
          </button>
        </div>
      </div>

      {{-- Images --}}
      <div class="card">
        <div class="card-header">
          <div class="card-title">Images</div>
          <span style="font-size:11px;color:var(--muted);">Drag to reorder · First = primary</span>
        </div>
        <div class="card-body">
          @if($product->exists)
            <div class="img-grid" id="imgGrid">
              @foreach($product->getMedia('images') as $i => $media)
              <div class="img-item" data-id="{{ $media->id }}">
                <img src="{{ $media->getUrl('thumb') }}" alt="Image {{ $i+1 }}">
                <button type="button" class="img-del"
                        onclick="deleteImage({{ $media->id }}, this.closest('.img-item'))">×</button>
                @if($i === 0)<div class="img-primary">PRIMARY</div>@endif
              </div>
              @endforeach
            </div>
            <div style="margin-top:10px;">
              <label style="display:block;margin-bottom:4px;font-size:11.5px;font-weight:600;color:var(--text);">Add more images</label>
              <input type="file" name="images[]" multiple accept="image/*"
                     class="input" style="height:auto;padding:6px 10px;">
              <div class="field-hint">JPG, PNG, WebP · Max 4MB each · First uploaded = primary</div>
            </div>
          @else
            <input type="file" name="images[]" multiple accept="image/*"
                   class="input" style="height:auto;padding:6px 10px;">
            <div class="field-hint">Add images after saving the product for drag-to-reorder.</div>
          @endif
        </div>
      </div>

    </div>

  </div>
</form>
</div>
@endsection

@push('scripts')
<script>
// Tab switcher
function switchTab(id) {
  document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('#formTabs .tab').forEach(t => t.classList.remove('active'));
  document.getElementById('tab-' + id).classList.add('active');
  event.target.classList.add('active');
}

// Spec rows
function addSpecRow() {
  const tpl = `<div class="spec-row">
    <input class="input" type="text" name="spec_group[]" placeholder="Group">
    <input class="input" type="text" name="spec_label[]" placeholder="Label *">
    <input class="input" type="text" name="spec_value[]" placeholder="Value *">
    <button type="button" onclick="this.closest('.spec-row').remove()" style="width:26px;height:26px;border-radius:50%;border:none;background:var(--bg);color:var(--muted);cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;">×</button>
  </div>`;
  document.getElementById('specRows').insertAdjacentHTML('beforeend', tpl);
}

// Discount preview
(function() {
  const p = document.querySelector('[name="price_inr"]');
  const m = document.querySelector('[name="mrp_inr"]');
  const el = document.getElementById('discountPreview');
  if (!p || !m || !el) return;
  function update() {
    const pr = parseFloat(p.value), mr = parseFloat(m.value);
    if (pr > 0 && mr > pr) {
      el.textContent = Math.round((mr - pr) / mr * 100) + '% off';
      el.style.color = 'var(--green)';
    } else {
      el.textContent = '—';
      el.style.color = 'var(--muted)';
    }
  }
  p.addEventListener('input', update);
  m.addEventListener('input', update);
})();

// Image delete
function deleteImage(mediaId, el) {
  if (!confirm('Delete this image?')) return;
  fetch('{{ $product->exists ? route("adminhu.products.image.delete", $product) : "#" }}', {
    method: 'DELETE',
    headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json'},
    body: JSON.stringify({media_id: mediaId})
  }).then(r => r.json()).then(d => { if (d.ok) el.remove(); });
}

// Image drag-to-reorder (SortableJS)
const grid = document.getElementById('imgGrid');
if (grid && typeof Sortable !== 'undefined') {
  Sortable.create(grid, {
    animation: 150,
    onEnd: function() {
      const order = [...grid.querySelectorAll('.img-item')].map(el => el.dataset.id);
      // Update PRIMARY badge
      grid.querySelectorAll('.img-primary').forEach(b => b.remove());
      const first = grid.querySelector('.img-item');
      if (first) first.insertAdjacentHTML('beforeend', '<div class="img-primary">PRIMARY</div>');
      // Persist order
      fetch('{{ $product->exists ? route("adminhu.products.image.reorder", $product) : "#" }}', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Content-Type': 'application/json'},
        body: JSON.stringify({order})
      });
    }
  });
}

function productForm() { return {}; }
</script>
@endpush
