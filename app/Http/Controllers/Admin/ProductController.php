<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSpec;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Bulk actions
        if ($request->isMethod('POST') && $request->filled('_bulk_action')) {
            $ids    = $request->input('bulk_ids', []);
            $action = $request->input('_bulk_action');
            if ($ids && in_array($action, ['activate', 'deactivate', 'delete'])) {
                $q = Product::whereIn('id', $ids);
                match ($action) {
                    'activate'   => $q->update(['is_active' => true]),
                    'deactivate' => $q->update(['is_active' => false]),
                    'delete'     => $q->delete(),
                };
                return back()->with('success', ucfirst($action) . 'd ' . count($ids) . ' product(s).');
            }
        }

        $query = Product::with(['brand', 'category']);

        if ($request->filled('q')) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$request->q}%")
                                      ->orWhere('sku', 'like', "%{$request->q}%"));
        }
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('brand_id'))    $query->where('brand_id', $request->brand_id);
        if ($request->filled('grade'))       $query->where('grade', $request->grade);
        if ($request->filled('stock'))       $query->where('stock_status', $request->stock);
        if ($request->filled('active'))      $query->where('is_active', $request->active);

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'name'       => $query->orderBy('name'),
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'stock'      => $query->orderBy('stock_qty'),
            default      => $query->latest(),
        };

        $perPage    = in_array((int)$request->get('per_page'), [10, 25, 50, 100]) ? (int)$request->get('per_page') : 25;
        $products   = $query->paginate($perPage)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::orderBy('name')->get();
        $counts = [
            'total'    => Product::count(),
            'active'   => Product::where('is_active', true)->count(),
            'out'      => Product::where('stock_status', 'out_of_stock')->count(),
            'featured' => Product::where('is_featured', true)->count(),
        ];

        return view('admin.products.index', compact('products', 'categories', 'brands', 'counts'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::active()->orderBy('name')->get();
        return view('admin.products.form', ['product' => new Product, 'categories' => $categories, 'brands' => $brands]);
    }

    public function store(Request $request)
    {
        $data    = $this->validated($request);
        $product = Product::create($data);
        $this->syncSpecs($product, $request);
        $this->handleImages($product, $request);
        return redirect()->route('adminhu.products.edit', $product)->with('success', 'Product created.');
    }

    public function show(Product $product) { return redirect()->route('adminhu.products.edit', $product); }

    public function edit(Product $product)
    {
        $product->load(['brand', 'category', 'specs_rows', 'media']);
        $categories = Category::orderBy('name')->get();
        $brands     = Brand::active()->orderBy('name')->get();
        return view('admin.products.form', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product);
        $product->update($data);
        $this->syncSpecs($product, $request);
        $this->handleImages($product, $request);
        return redirect()->route('adminhu.products.edit', $product)->with('success', 'Product saved.');
    }

    public function destroy(Product $product)
    {
        $product->clearMediaCollection('images');
        $product->delete();
        return redirect()->route('adminhu.products.index')->with('success', 'Product deleted.');
    }

    public function deleteImage(Request $request, Product $product)
    {
        $media = $product->media()->findOrFail($request->media_id);
        $media->delete();
        return response()->json(['ok' => true]);
    }

    public function reorderImages(Request $request, Product $product)
    {
        foreach ($request->order as $index => $mediaId) {
            $product->media()->where('id', $mediaId)->update(['order_column' => $index + 1]);
        }
        return response()->json(['ok' => true]);
    }

    public function importForm()
    {
        return view('admin.products.import');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240']]);
        try {
            $import = new ProductsImport;
            Excel::import($import, $request->file('file'));
            $msg = "Import done - {$import->imported} imported, {$import->skipped} skipped.";
            if ($import->errors->isNotEmpty()) {
                $msg .= ' Some rows had errors.';
                return redirect()->route('adminhu.products.import.form')
                    ->with('import_result', ['message' => $msg, 'errors' => $import->errors->toArray(), 'type' => 'partial']);
            }
            return redirect()->route('adminhu.products.index')->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function downloadSample()
    {
        $headers = ['name','sku','brand','category','grade','price','mrp','stock_status','stock_qty',
                    'warranty_months','short_description','quick_specs','is_active','is_featured','image_url',
                    'meta_title','meta_description'];
        $sample  = ['Dell Latitude 5490','LAT-5490-A','Dell','Laptops','A','14999','45000',
                    'in_stock','10','6','Certified refurbished business laptop',
                    'i5 8th Gen|8GB|256GB SSD|14" FHD','1','0','https://example.com/img.jpg','',''];
        $callback = function () use ($headers, $sample) {
            $f = fopen('php://output', 'w');
            fputcsv($f, $headers);
            fputcsv($f, $sample);
            fclose($f);
        };
        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pcfy-products-import-sample.csv"',
        ]);
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $request->merge([
            'price' => (int) round(floatval($request->input('price_inr', 0)) * 100),
            'mrp'   => $request->filled('mrp_inr') ? (int) round(floatval($request->input('mrp_inr')) * 100) : null,
        ]);

        $data = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'sku'               => ['nullable', 'string', 'max:80', 'unique:products,sku' . ($product ? ",{$product->id}" : '')],
            'brand_id'          => ['nullable', 'exists:brands,id'],
            'category_id'       => ['nullable', 'exists:categories,id'],
            'grade'             => ['required', 'in:A,B,C'],
            'price'             => ['required', 'integer', 'min:0'],
            'mrp'               => ['nullable', 'integer', 'min:0'],
            'stock_status'      => ['required', 'in:in_stock,low_stock,out_of_stock'],
            'stock_qty'         => ['nullable', 'integer', 'min:0'],
            'warranty_months'   => ['nullable', 'integer', 'min:0'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'quick_specs'       => ['nullable', 'string'],
            'sort_order'        => ['nullable', 'integer'],
            'meta_title'        => ['nullable', 'string', 'max:160'],
            'meta_description'  => ['nullable', 'string', 'max:320'],
        ]);

        $data['is_active']   = $request->boolean('is_active', true);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['sort_order']  = $data['sort_order'] ?? 0;
        $data['quick_specs'] = $request->filled('quick_specs')
            ? array_values(array_filter(array_map('trim', explode('|', $request->quick_specs))))
            : [];

        return $data;
    }

    private function syncSpecs(Product $product, Request $request): void
    {
        $product->specs_rows()->delete();
        $groups = $request->input('spec_group', []);
        $labels = $request->input('spec_label', []);
        $values = $request->input('spec_value', []);
        foreach ($labels as $i => $label) {
            if (!trim($label) || !trim($values[$i] ?? '')) continue;
            ProductSpec::create([
                'product_id' => $product->id,
                'group'      => trim($groups[$i] ?? ''),
                'label'      => trim($label),
                'value'      => trim($values[$i]),
                'sort_order' => $i,
            ]);
        }
    }

    private function handleImages(Product $product, Request $request): void
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $product->addMedia($file)->toMediaCollection('images');
            }
        }
    }
}