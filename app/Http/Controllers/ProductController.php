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
    public function index($id, Request $request)
    {
        $sort = $request->get('sort');
        $search = $request->get('search');

        // Query produk khusus untuk user_id tertentu
        $query = Product::where('live_account_id', $id);

        // Filter pencarian
        if (!empty($search)) {
            $query->whereRaw('LOWER(title) LIKE LOWER(?)', ['%' . trim($search) . '%']);
        }

        // Sorting
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
                $query->latest();
                break;
        }

        $products = $query->paginate(20);
        $user = Auth::user();

        return view('produk', compact('products', 'user', 'sort', 'id'));
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
     * Hapus produk.
     */
    public function destroy($itemid)
    {
        $product = Product::findOrFail($itemid);
        $product->delete();

        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}
