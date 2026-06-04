<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class FetchProductImages extends Command
{
    protected $signature   = 'products:fetch-images {--force : Re-download even if image already exists}';
    protected $description = 'Download official product images from Dell CDN and attach via Spatie Media Library';

    // Base URL for Dell's Image Service
    private const CDN = 'https://i.dell.com/is/image/DellContent/';
    private const PARAMS = '?fmt=jpg&pscan=auto&scl=1&hei=600&wid=800&qlt=100,1&resMode=sharp2&size=800,600&chrss=full';

    /**
     * Confirmed image paths extracted from Dell product pages.
     * Key = substring match against product name (lowercase).
     * Value = array of CDN path candidates (tried in order).
     */
    private function getCandidates(): array
    {
        return [
            // ── Desktops / Workstations ──────────────────────────────────────
            'precision 3460' => [
                'content/dam/ss2/product-images/dell-client-products/workstations/fixed-workstations/precision/3460/media-gallery/desktop-precision-3460-gallery-3.psd',
                'content/dam/ss2/product-images/dell-client-products/workstations/fixed-workstations/precision/3460/media-gallery/desktop-precision-3460-gallery-1.psd',
            ],
            'precision 3680' => [
                'content/dam/ss2/product-images/dell-client-products/workstations/fixed-workstations/precision/3680/media-gallery/desktop-precision-3680-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/workstations/fixed-workstations/precision/3680/media-gallery/workstation-precision-3680-tower-gallery-3.psd',
            ],
            'precision 7875' => [
                'content/dam/ss2/product-images/dell-client-products/workstations/fixed-workstations/precision/7875/media-gallery/workstation-precision-7875-tower-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/workstations/fixed-workstations/precision/7875/media-gallery/desktop-precision-7875-gallery-1.psd',
            ],
            'optiplex 7010' => [
                'content/dam/ss2/product-images/dell-client-products/desktops/optiplex-desktops/optiplex-7010/media-gallery/desktop-optiplex-7010-mff-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/desktops/optiplex-desktops/optiplex-7010/media-gallery/desktop-optiplex-7010-gallery-1.psd',
            ],
            'optiplex 7020' => [
                'content/dam/ss2/product-images/dell-client-products/desktops/optiplex-desktops/optiplex-7020/media-gallery/desktop-optiplex-7020-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/desktops/optiplex-desktops/optiplex-7020/media-gallery/desktop-optiplex-7020-sff-gallery-1.psd',
            ],
            'vostro 3020' => [
                'content/dam/ss2/product-images/dell-client-products/desktops/vostro-desktops/vostro-3020/media-gallery/desktop-vostro-3020-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/desktops/vostro-desktops/3020/media-gallery/desktop-vostro-3020-sff-gallery-1.psd',
            ],
            'vostro 3030' => [
                'content/dam/ss2/product-images/dell-client-products/desktops/vostro-desktops/3030t/media-gallery/desktop-vostro-3030-bl-noodd-mcr-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/desktops/vostro-desktops/3030t/media-gallery/desktop-vostro-3030-gallery-1.psd',
            ],
            'vostro 3910' => [
                'content/dam/ss2/product-images/dell-client-products/desktops/vostro-desktops/3910t/media-gallery/desktop-vostro-3910-mt-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/desktops/vostro-desktops/3910/media-gallery/desktop-vostro-3910-gallery-1.psd',
            ],
            'inspiron 3030' => [
                'content/dam/ss2/product-images/dell-client-products/desktops/inspiron-desktops/3030/media-gallery/desktop-inspiron-3030-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/desktops/inspiron-desktops/inspiron-3030/media-gallery/desktop-inspiron-3030-sff-gallery-1.psd',
            ],
            'ecs1250' => [
                'content/dam/ss2/product-images/dell-client-products/desktops/dell-pro-desktops/dell-slim-ecs1250/media-gallery/desktop-dell-slim-ecs1250-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/desktops/dell-pro-desktops/ecs1250/media-gallery/desktop-slim-ecs1250-gallery-1.psd',
            ],
            'qct1250' => [
                'content/dam/ss2/product-images/dell-client-products/desktops/dell-pro-desktops/pro-tower-qct1250/media-gallery/desktop-pro-tower-qct1250-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/desktops/dell-pro-desktops/qct1250/media-gallery/desktop-dell-pro-tower-qct1250-gallery-1.psd',
            ],
            'qct1255' => [
                'content/dam/ss2/product-images/dell-client-products/desktops/dell-pro-desktops/pro-tower-qct1255/media-gallery/desktop-pro-tower-qct1255-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/desktops/dell-pro-desktops/qct1255/media-gallery/desktop-dell-pro-tower-qct1255-gallery-1.psd',
            ],
            'qvt1260' => [
                'content/dam/ss2/product-images/dell-client-products/desktops/dell-pro-desktops/pro-tower-qvt1260/media-gallery/desktop-pro-tower-qvt1260-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/desktops/dell-pro-desktops/qvt1260/media-gallery/desktop-dell-pro-tower-qvt1260-gallery-1.psd',
            ],
            'qcm1250' => [
                'content/dam/ss2/product-images/dell-client-products/desktops/dell-pro-desktops/pro-micro-qcm1250/media-gallery/desktop-pro-micro-qcm1250-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/desktops/dell-pro-desktops/qcm1250/media-gallery/desktop-dell-pro-micro-qcm1250-gallery-1.psd',
            ],
            // ── Laptops / Notebooks ─────────────────────────────────────────
            'latitude 3420' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/latitude-notebooks/latitude-14-3420-laptop/media-gallery/notebook-latitude-14-3420-nt-gray-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/notebooks/latitude-notebooks/14-3420/media-gallery/notebook-latitude-14-3420-gallery-1.psd',
            ],
            'latitude 3440' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/latitude-notebooks/latitude-14-3440-laptop/media-gallery/notebook-latitude-14-3440-nt-uma-gray-gallery-2.psd',
                'content/dam/ss2/product-images/dell-client-products/notebooks/latitude-notebooks/latitude-14-3440-laptop/media-gallery/notebook-latitude-14-3440-nt-uma-gray-gallery-1.psd',
            ],
            'latitude 3450' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/latitude-notebooks/latitude-14-3450-laptop/media-gallery/notebook-latitude-14-3450-nt-gray-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/notebooks/latitude-notebooks/14-3450/media-gallery/notebook-latitude-14-3450-gallery-1.psd',
            ],
            'latitude 3520' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/latitude-notebooks/15-3520/media-gallery/peripherals_latop_latitude_3520_gallery_3.psd',
                'content/dam/ss2/product-images/dell-client-products/notebooks/latitude-notebooks/latitude-15-3520-laptop/media-gallery/notebook-latitude-15-3520-nt-gray-gallery-1.psd',
            ],
            'latitude 3540' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/latitude-notebooks/latitude-14-3540-laptop/media-gallery/notebook-latitude-14-3540-nt-gray-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/notebooks/latitude-notebooks/latitude-15-3540-laptop/media-gallery/notebook-latitude-15-3540-nt-gray-gallery-1.psd',
            ],
            // Dell 3450 laptop (not latitude)
            'dell 3450' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/latitude-notebooks/latitude-14-3450-laptop/media-gallery/notebook-latitude-14-3450-nt-gray-gallery-1.psd',
            ],
            'dell  5450' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/latitude-notebooks/latitude-14-5450-laptop/media-gallery/notebook-latitude-14-5450-nt-gray-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pro-14-5450/media-gallery/notebook-pro-14-5450-gallery-1.psd',
            ],
            'pv14250' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pro-14-essential-pv14250/media-gallery/notebook-pro-14-pv14250-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pv14250/media-gallery/notebook-dell-pro-14-essential-pv14250-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pro-14-pv14250/media-gallery/notebook-pro-14-pv14250-gallery-1.psd',
            ],
            'pb14250' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pro-14-plus-pb14250/media-gallery/notebook-pro-14-pb14250-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pb14250/media-gallery/notebook-dell-pro-14-plus-pb14250-gallery-1.psd',
            ],
            'pb14255' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pro-14-plus-pb14255/media-gallery/notebook-pro-14-pb14255-gallery-1.psd',
            ],
            'pc14250' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pro-pc14250/media-gallery/notebook-pro-pc14250-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pc14250/media-gallery/notebook-dell-pro-pc14250-gallery-1.psd',
            ],
            'pc16250' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pro-pc16250/media-gallery/notebook-pro-pc16250-gallery-1.psd',
            ],
            'pv15250' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pro-15-essential-pv15250/media-gallery/notebook-pro-15-pv15250-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pv15250/media-gallery/notebook-dell-pro-15-essential-pv15250-gallery-1.psd',
            ],
            'pv15255' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pro-15-essential-pv15255/media-gallery/notebook-pro-15-pv15255-gallery-1.psd',
            ],
            'mc 16250' => [
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/pro-max-16-mc16250/media-gallery/notebook-pro-max-16-mc16250-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/notebooks/dell-pro-notebooks/mc16250/media-gallery/notebook-dell-pro-max-16-mc16250-gallery-1.psd',
            ],
            // ── Mobile Workstations ─────────────────────────────────────────
            'precision 3590' => [
                'content/dam/ss2/product-images/dell-client-products/workstations/mobile-workstations/precision/15-3590/media-gallery/workstation-precision-3590-nt-silver-gallery-7.psd',
                'content/dam/ss2/product-images/dell-client-products/workstations/mobile-workstations/precision/15-3590/media-gallery/workstation-precision-3590-nt-silver-gallery-1.psd',
            ],
            'precision 3591' => [
                'content/dam/ss2/product-images/dell-client-products/workstations/mobile-workstations/precision/15-3591/media-gallery/workstation-precision-3591-nt-silver-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/workstations/mobile-workstations/precision/15-3591/media-gallery/workstation-precision-15-3591-gallery-1.psd',
            ],
            'precision ws 5570' => [
                'content/dam/ss2/product-images/dell-client-products/workstations/mobile-workstations/precision/15-5570/media-gallery/workstation-precision-5570-nt-gray-gallery-1.psd',
                'content/dam/ss2/product-images/dell-client-products/workstations/mobile-workstations/precision/15-5570/media-gallery/workstation-precision-15-5570-gallery-1.psd',
            ],
            'precision 7780' => [
                'content/dam/ss2/product-images/dell-client-products/workstations/mobile-workstations/precision/17-7780/media-gallery/workstation-precision-17-7780-gray-gallery-3.psd',
                'content/dam/ss2/product-images/dell-client-products/workstations/mobile-workstations/precision/17-7780/media-gallery/workstation-precision-17-7780-gray-gallery-1.psd',
            ],
        ];
    }

    public function handle(): int
    {
        $force     = $this->option('force');
        $products  = Product::when(!$force, fn($q) => $q->doesntHave('media'))->get();
        $candidates = $this->getCandidates();

        $this->info("Found {$products->count()} products to process.");
        $ok = $skip = $fail = 0;

        foreach ($products as $product) {
            $name  = strtolower($product->name);
            $paths = null;

            // Match by keyword
            foreach ($candidates as $keyword => $urls) {
                if (str_contains($name, strtolower($keyword))) {
                    $paths = $urls;
                    break;
                }
            }

            if (!$paths) {
                $this->line("  <fg=yellow>SKIP</> [{$product->id}] {$product->name} — no URL pattern");
                $skip++;
                continue;
            }

            $downloaded = false;
            foreach ($paths as $path) {
                $url = self::CDN . ltrim($path, '/') . self::PARAMS;
                $this->line("  Trying [{$product->id}] {$product->name}...");

                $tmpPath = $this->downloadImage($url);
                if ($tmpPath) {
                    try {
                        if ($force) $product->clearMediaCollection('images');
                        $product->addMedia($tmpPath)
                            ->usingFileName(str_replace(' ', '-', strtolower($product->name)) . '.jpg')
                            ->usingName($product->name)
                            ->toMediaCollection('images');
                        $this->line("  <fg=green>✓ OK</> [{$product->id}] {$product->name}");
                        $ok++;
                        $downloaded = true;
                        break;
                    } catch (\Exception $e) {
                        @unlink($tmpPath);
                        $this->error("  Failed to attach: " . $e->getMessage());
                    }
                }
            }

            if (!$downloaded) {
                $this->line("  <fg=red>✗ FAIL</> [{$product->id}] {$product->name}");
                $fail++;
            }
        }

        $this->info("\nDone — ✓ {$ok} saved, ⊘ {$skip} skipped, ✗ {$fail} failed.");
        return 0;
    }

    private function downloadImage(string $url): ?string
    {
        $ctx = stream_context_create(['http' => [
            'timeout'       => 20,
            'ignore_errors' => true,
            'header'        => implode("\r\n", [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124.0 Safari/537.36',
                'Accept: image/webp,image/apng,image/*,*/*;q=0.8',
                'Referer: https://www.dell.com/',
            ]),
        ]]);

        $data = @file_get_contents($url, false, $ctx);

        if ($data === false || strlen($data) < 5000) return null;

        // Confirm it's an image
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->buffer($data);
        if (!str_starts_with($mime, 'image/')) return null;

        $tmp = tempnam(sys_get_temp_dir(), 'dell_img_') . '.jpg';
        file_put_contents($tmp, $data);
        return $tmp;
    }
}
