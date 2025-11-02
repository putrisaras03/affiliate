<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\LiveAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Tampilkan semua produk
     */
    public function index(Request $request)
    {
        // Ambil parameter sort dan search
        $sort = $request->get('sort');
        $search = $request->get('search');

        // Query dasar produk dengan relasi
        $query = Product::with(['category', 'models']);

        // 🔍 Filter pencarian (cocok sebagian, case-insensitive)
        if (!empty($search)) {
            $query->whereRaw('LOWER(title) LIKE LOWER(?)', ['%' . trim($search) . '%']);
        }

        // 🧩 Terapkan urutan sesuai pilihan user
        switch ($sort) {
            case 'komisi_tertinggi':
                $query->orderBy('commission', 'desc');
                break;

            case 'rating_tertinggi':
                $query->orderBy('rating_star', 'desc');
                break;

            case 'terlaris':
                $query->orderBy('historical_sold', 'desc');
                break;

            case 'terbaru':
                $query->orderBy('ctime', 'desc');
                break;

            default:
                $query->latest(); // fallback: urut berdasarkan created_at terbaru
                break;
        }

        // 🔢 Pagination
        $products = $query->paginate(40);

        // 👤 Ambil user login
        $user = Auth::user();
        return view('produk', compact('products', 'user', 'sort'));
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'itemid' => 'required|numeric|unique:products,itemid',
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
            'product_link' => 'nullable|string',
            'seller_commission' => 'nullable|numeric',
            'historical_sold' => 'nullable|integer',
            'price_min' => 'nullable|numeric',
            'price_max' => 'nullable|numeric',
            'rating_star' => 'nullable|numeric',
            'shop_rating' => 'nullable|numeric',
        ]);

        $product = Product::create($validated);
        return response()->json(['message' => 'Produk berhasil disimpan', 'data' => $product]);
    }

    /**
     * Tampilkan detail produk.
     */
    public function show($itemid)
    {
        $product = Product::findOrFail($itemid);
        return response()->json($product);
    }

    /**
     * Perbarui data produk.
     */
    public function update(Request $request, $itemid)
    {
        $product = Product::findOrFail($itemid);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'image' => 'sometimes|string',
            'product_link' => 'sometimes|string',
            'seller_commission' => 'sometimes|numeric',
            'historical_sold' => 'sometimes|integer',
            'price_min' => 'sometimes|numeric',
            'price_max' => 'sometimes|numeric',
            'rating_star' => 'sometimes|numeric',
            'shop_rating' => 'sometimes|numeric',
        ]);

        $product->update($validated);
        return response()->json(['message' => 'Produk berhasil diperbarui', 'data' => $product]);
    }

    /**
     * Hapus produk.
     */
    public function destroy($itemid)
    {
        $product = Product::findOrFail($itemid);
        $product->delete();

        return response()->json(['message' => 'Produk berhasil dihapus']);
    }

    public function fetchProductsFromShopee($accountId)
    {
        $account = LiveAccount::findOrFail($accountId);

        // Kirim request ke Shopee Affiliate API
        $response = Http::withHeaders([
            'Cookie' => $account->cookies,
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'Accept' => 'application/json, text/plain, */*',
            'Referer' => 'https://affiliate.shopee.co.id/',
        ])->get('https://affiliate.shopee.co.id/api/v4/pas/product/search_items?limit=100&offset=0');

        if (!$response->successful()) {
            return back()->with('error', 'Gagal mengambil data produk dari Shopee.');
        }

        $data = $response->json();
        if (empty($data['data']['items'])) {
            return back()->with('error', 'Tidak ada produk ditemukan atau cookie kedaluwarsa.');
        }

        $items = $data['data']['items'];

        foreach ($items as $item) {
            try {
                Product::updateOrCreate(
                    [
                        'itemid' => $item['itemid'],
                        'live_account_id' => $account->user_id,
                    ],
                    [
                        'name' => $item['name'] ?? '',
                        'image' => $item['image'] ?? '',
                        'product_link' => $item['product_link'] ?? '',
                        'seller_commission' => $item['seller_commission'] ?? 0,
                        'historical_sold' => $item['historical_sold'] ?? 0,
                        'price_min' => $item['price_min'] ?? 0,
                        'price_max' => $item['price_max'] ?? 0,
                        'rating_star' => $item['rating_star'] ?? 0,
                        'shop_rating' => $item['shop_rating'] ?? 0,
                    ]
                );
            } catch (\Exception $e) {
                Log::error('Gagal menyimpan produk: ' . $e->getMessage());
            }
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui dari Shopee!');
    }
}
