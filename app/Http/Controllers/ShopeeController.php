<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\LiveAccount;
use App\Models\Product;

class ShopeeController extends Controller
{
    public function fetchProductsFromShopee(Request $request, $accountId)
    {
        $account = LiveAccount::findOrFail($accountId);

        $rawJson = $request->input('json_data');
        if (empty($rawJson)) {
            return back()->with('error', 'Data JSON tidak boleh kosong.');
        }

        // Parse JSON
        $data = json_decode($rawJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->with('error', 'Format JSON tidak valid.');
        }

        // Ambil bagian penting dari JSON
        $url = $data['url'] ?? null;
        $headers = $data['headers'] ?? [];
        $cookies = $data['cookies'] ?? [];
        $queries = $data['queries'] ?? [];

        if (empty($url)) {
            return back()->with('error', 'URL tidak ditemukan di data JSON.');
        }

        // Gabungkan cookie array jadi string
        if (!empty($cookies)) {
            $cookieString = collect($cookies)
                ->map(fn($v, $k) => "$k=$v")
                ->implode('; ');
            $headers['cookie'] = $cookieString;
        }

        // Kirim request ke Shopee
        try {
            $response = Http::withHeaders($headers)->get($url, $queries);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghubungi API Shopee: ' . $e->getMessage());
        }

        if (!$response->successful()) {
            Log::error('Gagal ambil data Shopee', [
                'status' => $response->status(),
                'body'    => $response->body(),
            ]);
            return back()->with('error', 'Gagal mengambil data dari Shopee Affiliate.');
        }

        $jsonResponse = $response->json();
        $items = $jsonResponse['data']['list'] ?? [];

        if (empty($items)) {
            return back()->with('error', 'Tidak ada produk ditemukan atau cookie sudah kadaluwarsa.');
        }

        // Simpan produk ke database
        foreach ($items as $item) {
            try {
                $productData = $item['batch_item_for_item_card_full'] ?? [];
                $ratingData  = $productData['item_rating'] ?? [];

                // Ambil ctime (UNIX timestamp)
                $ctimeUnix = $productData['ctime'] ?? null;

                Product::updateOrCreate(
                    ['itemid' => $item['item_id']],
                    [
                        'live_account_id' => $account->id,

                        'name' => $productData['name'] ?? '',

                        'image' => isset($productData['image'])
                            ? 'https://down-id.img.susercontent.com/file/' . $productData['image']
                            : '',

                        'product_link' => $item['product_link'] ?? $item['long_link'] ?? '',

                        'seller_commission' => isset($item['seller_commission_rate'])
                            ? floatval(str_replace('%', '', $item['seller_commission_rate']))
                            : (isset($item['default_commission_rate'])
                                ? floatval(str_replace('%', '', $item['default_commission_rate']))
                                : 0),

                        'historical_sold' => $productData['historical_sold'] ?? 0,

                        'price_min' => isset($productData['price_min'])
                            ? $productData['price_min'] / 100000
                            : 0,

                        'price_max' => isset($productData['price_max'])
                            ? $productData['price_max'] / 100000
                            : 0,

                        'rating_star' => isset($ratingData['rating_star'])
                            ? floatval($ratingData['rating_star'])
                            : 0,

                        'shop_rating' => $productData['shop_rating']
                            ?? ($ratingData['rating_star'] ?? 0),

                        'ctime' => $ctimeUnix ? intval($ctimeUnix) : null,
                    ]
                );

            } catch (\Exception $e) {
                Log::error('Gagal simpan produk Shopee: ' . $e->getMessage(), [
                    'item_id' => $item['item_id'] ?? null,
                ]);
            }
        }

        return redirect()->route('produk.index', ['id' => $accountId])
            ->with('success', 'Produk berhasil diperbarui dari Shopee!');
    }
}
