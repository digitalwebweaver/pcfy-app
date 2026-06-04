<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DellSystemsSeeder extends Seeder
{
    public function run(): void
    {
        $file = base_path('Dell Systems.xlsx');
        if (!file_exists($file)) {
            $this->command->error('Dell Systems.xlsx not found in project root.');
            return;
        }

        // ── Brand ────────────────────────────────────────────
        $brand = Brand::firstOrCreate(['name' => 'Dell'], [
            'slug'      => 'dell',
            'is_active' => true,
        ]);

        // ── Parent categories ─────────────────────────────────
        $desktopParent = Category::firstOrCreate(['name' => 'Desktops'], [
            'slug'       => 'desktops',
            'is_visible' => true,
            'sort_order' => 1,
        ]);
        $laptopParent = Category::firstOrCreate(['name' => 'Laptops'], [
            'slug'       => 'laptops',
            'is_visible' => true,
            'sort_order' => 2,
        ]);

        // ── Sub-categories ────────────────────────────────────
        $subCats = [
            'Desktop Core i3' => ['parent_id' => $desktopParent->id, 'sort_order' => 1],
            'Desktop Core i5' => ['parent_id' => $desktopParent->id, 'sort_order' => 2],
            'Desktop Core i7' => ['parent_id' => $desktopParent->id, 'sort_order' => 3],
            'Desktop Core i9' => ['parent_id' => $desktopParent->id, 'sort_order' => 4],
            'Desktop AMD'     => ['parent_id' => $desktopParent->id, 'sort_order' => 5],
            'Laptop Core 3'   => ['parent_id' => $laptopParent->id,  'sort_order' => 1],
            'Laptop Core 5'   => ['parent_id' => $laptopParent->id,  'sort_order' => 2],
            'Laptop Core 7'   => ['parent_id' => $laptopParent->id,  'sort_order' => 3],
            'Laptop AMD'      => ['parent_id' => $laptopParent->id,  'sort_order' => 4],
        ];

        $categories = [];
        foreach ($subCats as $name => $attrs) {
            $categories[$name] = Category::firstOrCreate(['name' => $name], array_merge($attrs, [
                'slug'       => Str::slug($name),
                'is_visible' => true,
            ]));
        }

        // ── Read Excel ────────────────────────────────────────
        $spreadsheet   = IOFactory::load($file);
        $sheet         = $spreadsheet->getActiveSheet();
        $highestRow    = $sheet->getHighestRow();

        $currentSection = null;
        $imported       = 0;
        $skipped        = 0;

        for ($row = 1; $row <= $highestRow; $row++) {
            $c = trim((string)$sheet->getCell('C' . $row)->getFormattedValue());
            $d = trim((string)$sheet->getCell('D' . $row)->getFormattedValue());
            $e = trim((string)$sheet->getCell('E' . $row)->getFormattedValue());

            // Detect section header
            if (preg_match('/Desktop[s]?\s+Core\s+i3/i', "$c $d $e"))  { $currentSection = 'Desktop Core i3'; continue; }
            if (preg_match('/Desktop[s]?\s+Core\s+i5/i', "$c $d $e"))  { $currentSection = 'Desktop Core i5'; continue; }
            if (preg_match('/Desktop[s]?\s+Core\s+i7/i', "$c $d $e"))  { $currentSection = 'Desktop Core i7'; continue; }
            if (preg_match('/Desktop[s]?\s+Core\s+i9/i', "$c $d $e"))  { $currentSection = 'Desktop Core i9'; continue; }
            if (preg_match('/Desktop\s+AMD/i', "$c $d $e"))            { $currentSection = 'Desktop AMD';     continue; }
            if (preg_match('/Laptop\s+Core\s+3/i', "$c $d $e"))        { $currentSection = 'Laptop Core 3';  continue; }
            if (preg_match('/Laptop\s+Core\s+5/i', "$c $d $e"))        { $currentSection = 'Laptop Core 5';  continue; }
            if (preg_match('/Laptop\s+Core\s+7/i', "$c $d $e"))        { $currentSection = 'Laptop Core 7';  continue; }
            if (preg_match('/Laptop\s+AMD/i', "$c $d $e"))             { $currentSection = 'Laptop AMD';     continue; }

            // Skip column headers and empty rows
            if (empty($d) || strtolower($d) === 'model' || empty($e)) continue;

            // Skip if no active section
            if (!$currentSection || !isset($categories[$currentSection])) { $skipped++; continue; }

            $category = $categories[$currentSection];
            $product  = $this->buildProduct($d, $e, $c, $currentSection, $brand->id, $category->id);

            // Avoid duplicating exact same model+config
            $exists = Product::where('name', $product['name'])->exists();
            if ($exists) { $skipped++; continue; }

            Product::create($product);
            $imported++;
        }

        $this->command->info("✓ Dell Systems imported: {$imported} products ({$skipped} skipped).");
    }

    // ── Build product array ───────────────────────────────────
    private function buildProduct(string $model, string $config, string $formFactor, string $section, int $brandId, int $categoryId): array
    {
        $parts    = array_map('trim', explode('|', $config));
        $cpu      = $parts[0] ?? '';
        $ram      = $parts[1] ?? '';
        $storage  = $parts[2] ?? '';
        $rest     = array_slice($parts, 3);

        // Detect display size from form factor or rest
        $display  = $this->detectDisplay($formFactor, $rest, $section);
        $os       = $this->detectOs($rest);
        $warranty = $this->detectWarranty($rest);
        $gpu      = $this->detectGpu($rest);
        $connectivity = 'Wi-Fi + Bluetooth';

        // Clean up model name
        $modelClean = $this->titleCase($model);
        $cpuClean   = strtoupper($cpu);
        $name       = "{$modelClean} ({$cpuClean} | {$ram} | {$storage})";
        if (mb_strlen($name) > 250) $name = mb_substr($name, 0, 247).'...';

        $isDesktop = str_starts_with($section, 'Desktop');
        $type      = $isDesktop ? 'Desktop' : 'Laptop';
        $ff        = $formFactor ?: ($isDesktop ? 'Tower' : ($display ? $display.' Screen' : ''));

        // Quick specs chips
        $quickSpecs = array_filter([
            $cpuClean,
            $ram ? 'RAM: '.$ram : '',
            $storage ? 'Storage: '.$storage : '',
            $display ? 'Display: '.$display : '',
            $os ?: '',
            $gpu ?: '',
            $warranty ?: '',
        ]);

        // Short description
        $shortDesc = "{$type} | {$cpuClean} | {$ram} RAM | {$storage} Storage"
            . ($display ? " | {$display}" : '')
            . ($os ? " | {$os}" : '')
            . ($warranty ? " | {$warranty}" : '');

        // Full description
        $description = $this->buildDescription($modelClean, $cpuClean, $ram, $storage, $display, $os, $warranty, $gpu, $ff, $type, $connectivity);

        // Price
        $price = $this->estimatePrice($cpu, $ram, $storage, $os, $warranty, $gpu, $section);
        $mrp   = (int)round($price * 1.22); // ~22% above selling price (realistic markup)

        // SKU
        $skuBase = strtoupper(Str::slug(preg_replace('/[^a-zA-Z0-9\s]/', '', $model)));
        $suffix  = strtoupper(substr(md5($config), 0, 4));
        $sku     = "DELL-{$skuBase}-{$suffix}";
        if (mb_strlen($sku) > 60) $sku = mb_substr($sku, 0, 60);

        return [
            'brand_id'          => $brandId,
            'category_id'       => $categoryId,
            'name'              => $name,
            'slug'              => Str::slug($name).'-'.strtolower($suffix),
            'sku'               => $sku,
            'short_description' => $shortDesc,
            'description'       => $description,
            'price'             => $price,
            'mrp'               => $mrp,
            'grade'             => 'A',
            'stock_status'      => 'in_stock',
            'warranty_months'   => $this->warrantyMonths($warranty),
            'quick_specs'       => json_encode(array_values($quickSpecs)),
            'is_active'         => true,
            'is_featured'       => false,
            'sort_order'        => 0,
        ];
    }

    // ── Helpers ───────────────────────────────────────────────
    private function detectDisplay(string $ff, array $rest, string $section): string
    {
        // From form factor column
        if (preg_match('/(\d+)\"/i', $ff, $m)) return $m[1].'"';

        // From section name
        if (preg_match('/Laptop/i', $section)) {
            foreach ($rest as $p) {
                if (preg_match('/FHD|HD|FPR|TOUCH/i', $p)) {
                    $res = '';
                    if (stripos($p, 'FHD') !== false || stripos($p, 'FPR') !== false) $res = 'FHD';
                    elseif (stripos($p, 'HD') !== false) $res = 'HD';
                    if (stripos($p, 'TOUCH') !== false) $res .= ' Touch';
                    return trim($res);
                }
            }
        }

        return '';
    }

    private function detectOs(array $rest): string
    {
        foreach ($rest as $p) {
            $p = trim($p);
            if (preg_match('/WIN\s*11\s*PRO/i',  $p)) return 'Windows 11 Pro';
            if (preg_match('/WIN\s*11\s*HOME/i', $p)) return 'Windows 11 Home';
            if (preg_match('/WIN\s*11/i',        $p)) return 'Windows 11';
            if (preg_match('/WIN\s*10/i',        $p)) return 'Windows 10';
            if (preg_match('/\bDOS\b/i',         $p)) return 'DOS (No OS)';
        }
        return '';
    }

    private function detectWarranty(array $rest): string
    {
        foreach ($rest as $p) {
            $p = trim($p);
            if (preg_match('/(\d)\s*YR\s*PRO\s*SUPPORT/i',   $p, $m)) return $m[1].' Year Pro Support';
            if (preg_match('/(\d)\s*YR\s*NBD/i',             $p, $m)) return $m[1].' Year NBD';
            if (preg_match('/(\d)\s*YR\s*ONSITE/i',          $p, $m)) return $m[1].' Year Onsite';
            if (preg_match('/(\d)\s*YR\s*ADP/i',             $p, $m)) return $m[1].' Year ADP';
            if (preg_match('/(\d)\s*YRS?/i',                 $p, $m)) return $m[1].' Year';
            if (preg_match('/(\d)\s*YEAR/i',                 $p, $m)) return $m[1].' Year';
        }
        return '1 Year';
    }

    private function detectGpu(array $rest): string
    {
        foreach ($rest as $p) {
            if (preg_match('/(RTX|GTX|T\d{3}|T\d{4}|A\d{3}|A\d{4}|Quadro)/i', $p)) {
                return trim($p);
            }
        }
        return '';
    }

    private function warrantyMonths(string $warranty): int
    {
        if (preg_match('/(\d+)\s*year/i', $warranty, $m)) return (int)$m[1] * 12;
        return 12;
    }

    private function titleCase(string $str): string
    {
        return ucwords(strtolower(trim(preg_replace('/\s+/', ' ', $str))));
    }

    private function buildDescription(string $model, string $cpu, string $ram, string $storage, string $display, string $os, string $warranty, string $gpu, string $ff, string $type, string $connectivity): string
    {
        $gpuLine = $gpu ? "<li><strong>Graphics:</strong> {$gpu}</li>" : '';
        $osLine  = $os ? "<li><strong>Operating System:</strong> {$os}</li>" : '';
        $ffLine  = $ff ? "<li><strong>Form Factor:</strong> {$ff}</li>" : '';
        $dispLine = ($display && $type === 'Laptop') ? "<li><strong>Display:</strong> {$display}</li>" : '';

        return <<<HTML
<p>The <strong>{$model}</strong> is a brand-new Dell {$type} powered by the <strong>{$cpu}</strong> processor, built for professionals and businesses seeking reliable performance at a competitive price.</p>

<h3>Key Specifications</h3>
<ul>
  <li><strong>Processor:</strong> {$cpu}</li>
  <li><strong>Memory:</strong> {$ram} RAM</li>
  <li><strong>Storage:</strong> {$storage}</li>
  {$dispLine}
  {$osLine}
  {$gpuLine}
  {$ffLine}
  <li><strong>Connectivity:</strong> {$connectivity}</li>
  <li><strong>Warranty:</strong> {$warranty}</li>
</ul>

<h3>Why Choose This {$type}?</h3>
<ul>
  <li>Genuine Dell hardware with official warranty support</li>
  <li>Ideal for business, office productivity and multitasking</li>
  <li>GST invoice included on all purchases</li>
  <li>Available with bulk/B2B discounts — contact us for pricing</li>
</ul>
HTML;
    }

    // ── Price estimation (all values in paise = INR × 100) ───
    private function estimatePrice(string $cpu, string $ram, string $storage, string $os, string $warranty, string $gpu, string $section): int
    {
        $cpu = strtoupper(trim($cpu));

        // Base prices in paise (INR × 100)
        // e.g. 3_800_000 paise = ₹38,000
        $base = match(true) {
            // ── Desktops ──────────────────────────────────────
            str_starts_with($section, 'Desktop') && preg_match('/I3|CORE\s*3/i', $cpu)  =>  3_800_000,  // ₹38,000
            str_starts_with($section, 'Desktop') && preg_match('/I5|CORE\s*5/i', $cpu)  =>  5_500_000,  // ₹55,000
            str_starts_with($section, 'Desktop') && preg_match('/I7|CORE\s*7/i', $cpu)  =>  8_500_000,  // ₹85,000
            str_starts_with($section, 'Desktop') && preg_match('/I9/i', $cpu)           => 15_500_000,  // ₹1,55,000
            str_starts_with($section, 'Desktop') && preg_match('/THREADRIPPER/i', $cpu) => 28_000_000,  // ₹2,80,000
            str_starts_with($section, 'Desktop') && preg_match('/ULTRA\s*7/i', $cpu)    => 12_000_000,  // ₹1,20,000
            str_starts_with($section, 'Desktop') && preg_match('/RYZEN/i', $cpu)        =>  8_800_000,  // ₹88,000
            // ── Laptops ───────────────────────────────────────
            str_starts_with($section, 'Laptop') && preg_match('/CORE\s*3/i', $cpu)      =>  4_800_000,  // ₹48,000
            str_starts_with($section, 'Laptop') && preg_match('/I3-/i', $cpu)           =>  4_500_000,  // ₹45,000
            str_starts_with($section, 'Laptop') && preg_match('/ULTRA\s*5/i', $cpu)     =>  8_800_000,  // ₹88,000
            str_starts_with($section, 'Laptop') && preg_match('/CORE\s*5/i', $cpu)      =>  7_200_000,  // ₹72,000
            str_starts_with($section, 'Laptop') && preg_match('/I5-/i', $cpu)           =>  7_000_000,  // ₹70,000
            str_starts_with($section, 'Laptop') && preg_match('/ULTRA\s*7/i', $cpu)     => 13_000_000,  // ₹1,30,000
            str_starts_with($section, 'Laptop') && preg_match('/CORE\s*7/i', $cpu)      => 10_200_000,  // ₹1,02,000
            str_starts_with($section, 'Laptop') && preg_match('/I7-/i', $cpu)           => 10_000_000,  // ₹1,00,000
            str_starts_with($section, 'Laptop') && preg_match('/RYZEN\s*5/i', $cpu)     =>  6_500_000,  // ₹65,000
            str_starts_with($section, 'Laptop') && preg_match('/RYZEN\s*7/i', $cpu)     => 11_000_000,  // ₹1,10,000
            default                                                                      =>  5_000_000,  // ₹50,000 fallback
        };

        // RAM premium
        $ramGb = (int)preg_replace('/[^0-9]/', '', $ram);
        if ($ramGb >= 64)     $base = (int)($base * 1.40);
        elseif ($ramGb >= 32) $base = (int)($base * 1.20);
        elseif ($ramGb >= 16) $base = (int)($base * 1.10);

        // Storage premium
        if (preg_match('/2\s*TB/i', $storage))     $base = (int)($base * 1.12);
        elseif (preg_match('/1\s*TB/i', $storage)) $base = (int)($base * 1.06);

        // Windows premium: ₹8,000 = 800_000 paise
        if (preg_match('/WIN/i', $os)) $base += 800_000;

        // GPU premium (in paise)
        if (!empty($gpu)) {
            if (preg_match('/RTX.*8GB|RTX\s*A\d{4}|RTX.*PRO.*8/i', $gpu)) $base += 3_500_000; // ₹35,000
            elseif (preg_match('/RTX.*6GB|RTX.*4GB|RTX.*PRO/i', $gpu))    $base += 2_500_000; // ₹25,000
            elseif (preg_match('/T\d{3,4}|A\d{3,4}/i', $gpu))             $base += 1_500_000; // ₹15,000
        }

        // 5-year warranty premium (+8%)
        if (preg_match('/5\s*YR/i', $warranty)) $base = (int)($base * 1.08);

        return max($base, 300_000); // minimum ₹3,000 sanity floor
    }
}
