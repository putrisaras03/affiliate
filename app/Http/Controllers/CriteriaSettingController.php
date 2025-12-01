<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\CriteriaSetting;

class CriteriaSettingController extends Controller
{
    public function index()
    {
        // 🟦 Daftar kolom yang diizinkan jadi kriteria MOORA
        $allowedColumns = [
            'commission_value', // 🟪 Kriteria virtual
            'historical_sold',
            'rating_star',
            'shop_rating',
        ];

        // Ambil setting yang sudah ada
        $settings = CriteriaSetting::all()->keyBy('column_name');

        // Kirim hanya kolom yang diperbolehkan
        return view('criteria', [
            'columns' => $allowedColumns,
            'settings' => $settings
        ]);
    }

    public function store(Request $request)
    {
        foreach ($request->criteria as $column => $data) {
            CriteriaSetting::updateOrCreate(
                ['column_name' => $column],
                [
                    'weight' => $data['weight'],
                    'type'   => $data['type'],
                ]
            );
        }

        return back()->with('success', 'Pengaturan kriteria berhasil disimpan!');
    }
}
