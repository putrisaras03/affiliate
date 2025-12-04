<?php

namespace App\Http\Controllers;

use App\Models\LiveAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $liveAccounts = LiveAccount::with('products')->get();

        $accountsData = $liveAccounts->map(function ($acc) {
            $products = $acc->products;
            return [
                'id' => $acc->id,
                'name' => $acc->nama,
                'totalProducts' => $products->count(),
                'avgCommission' => round($products->avg('seller_commission') ?? 0, 2),
                'maxCommission' => $products->max('commission_value') ?? 0,
                'avgRating' => round($products->avg('shop_rating') ?? 0, 2),
            ];
        });

        $user = Auth::user();

        return view('dashboard', compact('accountsData', 'user'));
    }
}
