<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CriteriaSetting;
use App\Services\MooraService;

class MooraController extends Controller
{
    protected $moora;

    public function __construct(MooraService $moora)
    {
        $this->moora = $moora;
    }

    public function index($accountId)
    {
        return view('moora.index', compact('accountId'));
    }

    public function run($accountId)
    {
        // Ambil produk berdasarkan akun affiliate (live account)
        $products = Product::where('live_account_id', $accountId)
            ->get();

        if ($products->count() === 0) {
            return back()->with('error', 'Tidak ada produk untuk dihitung.');
        }

        // Ambil bobot kriteria user
        $criteria = CriteriaSetting::all();

        // Jalankan perhitungan MOORA
        $result = $this->moora->run($products, $criteria);

        return view('moora.result', compact('result'));
    }
}
