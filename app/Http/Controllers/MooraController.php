<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CriteriaSetting;
use App\Services\MooraService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function run($accountId, Request $request)
    {
        // Ambil produk berdasarkan akun affiliate (live account)
        $products = Product::where('live_account_id', $accountId)->get();

        if ($products->count() === 0) {
            return back()->with('error', 'Tidak ada produk untuk dihitung.');
        }

        // Ambil bobot kriteria user
        $criteria = CriteriaSetting::all();

        // Jalankan perhitungan MOORA → hasil berupa array
        $results = $this->moora->run($products, $criteria);

        // Set pagination
        $perPage = 20;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        // Slice data sesuai halaman
        $paginatedResults = new LengthAwarePaginator(
            array_slice($results, $offset, $perPage),
            count($results),
            $perPage,
            $page,
            ['path' => url()->current()]
        );

        return view('moora.result', [
            'results' => $paginatedResults
        ]);
    }
}
