<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\LiveAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Tampilkan semua produk untuk akun live tertentu
     */
    public function index($liveAccountId, Request $request)
    {
        $sort = $request->get('sort');
        $search = $request->get('search');

        $query = Product::where('live_account_id', $liveAccountId);

        // Pencarian berdasarkan nama
        if (!empty($search)) {
            $query->whereRaw('LOWER(name) LIKE LOWER(?)', ['%' . trim($search) . '%']);
        }

        // Sorting
        switch ($sort) {
            case 'komisi_tertinggi':
                $query->orderByRaw('(COALESCE(price_min,0) * (COALESCE(seller_commission,0) / 100)) DESC');
                break;

            case 'rating_tertinggi':
                $query->orderBy('rating_star', 'desc');
                break;

            case 'terlaris':
                $query->orderBy('historical_sold', 'desc');
                break;

            case 'terbaru':
                $query->orderBy('created_at', 'desc');
                break;

            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(20)->appends($request->query());
        $user = Auth::user();

        return view('produk', compact('products', 'user', 'sort', 'liveAccountId'));
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
            'product_link' => 'nullable|string',
            'seller_commission' => 'nullable|numeric',
            'historical_sold' => 'nullable|integer',
            'price_min' => 'nullable|numeric',
            'price_max' => 'nullable|numeric',
            'rating_star' => 'nullable|numeric',
            'shop_rating' => 'nullable|numeric',
            'live_account_id' => 'required|exists:live_accounts,id',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Produk berhasil disimpan',
            'data' => $product,
        ]);
    }

    /**
     * Tampilkan detail satu produk
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    /**
     * Hapus produk.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus'
        ]);
    }
}
