<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public int        $imported = 0;
    public int        $skipped  = 0;
    public Collection $errors;

    // Cache brand/category lookups across rows
    private array $brandCache    = [];
    private array $categoryCache = [];

    public function __construct()
    {
        $this->errors = collect();
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // 1-indexed, +1 for header
            $row    = $row->toArray();

            $validator = Validator::make($row, [
                'name'  => ['required', 'string'],
                'grade' => ['required', 'in:A,B,C'],
                'price' => ['required', 'numeric', 'min:0'],
            ]);

            if ($validator->fails()) {
                $this->errors->push("Row {$rowNum}: " . implode(' | ', $validator->errors()->all()));
                $this->skipped++;
                continue;
            }

            try {
                // Resolve brand
                $brandId = null;
                if (!empty($row['brand'])) {
                    $key = strtolower(trim($row['brand']));
                    if (!isset($this->brandCache[$key])) {
                        $this->brandCache[$key] = Brand::firstOrCreate(['name' => trim($row['brand'])])->id;
                    }
                    $brandId = $this->brandCache[$key];
                }

                // Resolve category
                $categoryId = null;
                if (!empty($row['category'])) {
                    $key = strtolower(trim($row['category']));
                    if (!isset($this->categoryCache[$key])) {
                        $this->categoryCache[$key] = Category::firstOrCreate(['name' => trim($row['category'])])->id;
                    }
                    $categoryId = $this->categoryCache[$key];
                }

                // Quick specs: pipe-separated
                $quickSpecs = [];
                if (!empty($row['quick_specs'])) {
                    $quickSpecs = array_filter(array_map('trim', explode('|', $row['quick_specs'])));
                }

                // Price in ₹ → paise
                $price = (int) round(floatval($row['price']) * 100);
                $mrp   = !empty($row['mrp']) ? (int) round(floatval($row['mrp']) * 100) : null;

                $sku = !empty($row['sku']) ? trim($row['sku']) : null;

                // Skip or update duplicate SKU
                $existing = $sku ? Product::where('sku', $sku)->first() : null;

                $productData = [
                    'brand_id'          => $brandId,
                    'category_id'       => $categoryId,
                    'sku'               => $sku,
                    'grade'             => strtoupper(trim($row['grade'])),
                    'price'             => $price,
                    'mrp'               => $mrp,
                    'stock_status'      => in_array($row['stock_status'] ?? '', ['in_stock','low_stock','out_of_stock'])
                                            ? $row['stock_status'] : 'in_stock',
                    'stock_qty'         => (int)($row['stock_qty'] ?? 0),
                    'warranty_months'   => (int)($row['warranty_months'] ?? 6),
                    'short_description' => $row['short_description'] ?? null,
                    'quick_specs'       => $quickSpecs ?: null,
                    'is_active'         => isset($row['is_active']) ? (bool)(int)$row['is_active'] : true,
                    'is_featured'       => isset($row['is_featured']) ? (bool)(int)$row['is_featured'] : false,
                    'meta_title'        => $row['meta_title'] ?? null,
                    'meta_description'  => $row['meta_description'] ?? null,
                ];

                if ($existing) {
                    $existing->update($productData);
                    $product = $existing;
                } else {
                    $productData['name'] = trim($row['name']);
                    $product = Product::create($productData);
                }

                // Download image from URL
                if (!empty($row['image_url']) && filter_var($row['image_url'], FILTER_VALIDATE_URL)) {
                    try {
                        $response = Http::timeout(10)->get($row['image_url']);
                        if ($response->successful()) {
                            $ext      = pathinfo(parse_url($row['image_url'], PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                            $tmpPath  = tempnam(sys_get_temp_dir(), 'pcfy_img_') . '.' . $ext;
                            file_put_contents($tmpPath, $response->body());
                            $product->addMedia($tmpPath)->toMediaCollection('images');
                        }
                    } catch (\Exception $e) {
                        // Non-fatal — just skip image
                    }
                }

                $this->imported++;
            } catch (\Exception $e) {
                $this->errors->push("Row {$rowNum}: " . $e->getMessage());
                $this->skipped++;
            }
        }
    }
}
