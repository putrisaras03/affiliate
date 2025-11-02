<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

class ShopeeFetchProducts extends Command
{
    /**
     * Command signature:
     * php artisan shopee:fetch-products {catid} {--limit=60} {--offset=0}
     */
    protected $signature = 'shopee:fetch-products {catid : ID kategori Shopee} {--limit=60} {--offset=0}';

    protected $description = 'Ambil itemid & shopid dari kategori Shopee (API recommend_v2)';

    public function handle()
    {
        $catid = $this->argument('catid');
        $limit = $this->option('limit');
        $offset = $this->option('offset');

        $this->info("🛍️  Mengambil itemid & shopid dari kategori {$catid}...");

        // Endpoint Shopee
        $url = "https://shopee.co.id/api/v4/recommend/recommend_v2";

        // Parameter
        $params = [
            'bundle' => 'category_landing_page',
            'limit' => $limit,
            'offset' => $offset,
            'category_id' => $catid,
        ];

        // Header lengkap agar tidak 403
        $headers = [
            'accept' => 'application/json',
            'accept-language' => 'id-ID,id;q=0.9,en;q=0.8',
            'referer' => "https://shopee.co.id/{$catid}",
            'origin' => 'https://shopee.co.id',
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
            'x-requested-with' => 'XMLHttpRequest',
            // Cookie opsional tapi sangat membantu untuk mencegah 403
            'cookie' => '_gcl_au=1.1.123456789.1700000000; csrftoken=abc123; SPC_F=abcxyz; SPC_SI=abcdefghi;',
        ];

        try {
            $response = Http::withHeaders($headers)->get($url, $params);

            if ($response->failed()) {
                $this->error("❌ Gagal mengambil data: " . $response->status());
                $this->line($response->body());
                return;
            }

            $data = $response->json();

            if (!isset($data['data']['sections'])) {
                $this->error("⚠️ Tidak ada data sections dalam respons Shopee.");
                return;
            }

            $count = 0;

            foreach ($data['data']['sections'] as $section) {
                if (!isset($section['data']['item'])) continue;

                foreach ($section['data']['item'] as $item) {
                    $itemid = $item['itemid'] ?? null;
                    $shopid = $item['shopid'] ?? null;

                    if ($itemid && $shopid) {
                        Product::updateOrCreate(
                            ['item_id' => $itemid],
                            ['shopid' => $shopid]
                        );
                        $count++;
                    }
                }
            }

            $this->info("✅ Berhasil menyimpan {$count} produk dari kategori {$catid}.");

        } catch (\Exception $e) {
            $this->error("⚠️ Terjadi kesalahan: " . $e->getMessage());
        }
    }
}
