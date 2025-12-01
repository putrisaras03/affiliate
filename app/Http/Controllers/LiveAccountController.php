<?php

namespace App\Http\Controllers;

use App\Models\LiveAccount;
use App\Models\Studio;
use Illuminate\Http\Request;

class LiveAccountController extends Controller
{
    /**
     * Tampilkan daftar akun live.
     */
    public function index()
    {
        // Ambil semua akun live beserta studio dan jumlah produk
        $liveAccounts = LiveAccount::with('studio')
            ->withCount('products') // otomatis menambahkan $akun->products_count
            ->get();

        $studios = Studio::all();

        return view('etalase', compact('liveAccounts', 'studios'));
    }

    /**
     * Form tambah akun live.
     */
    public function create()
    {
        $studios = Studio::all();
        return view('live_accounts.create', compact('studios'));
    }

    /**
     * Simpan akun baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'studio_id' => 'required|exists:studios,id',
        ]);

        LiveAccount::create([
            'nama' => $request->nama,
            'studio_id' => $request->studio_id,
        ]);

        return redirect()->route('live-accounts.index')
            ->with('success', "Akun $request->nama berhasil ditambahkan!");
    }

    /**
     * Form edit akun live.
     */
    public function edit($id)
    {
        $liveAccount = LiveAccount::findOrFail($id);
        $studios = Studio::all();

        return view('live_accounts.edit', compact('liveAccount', 'studios'));
    }

    /**
     * Update data akun.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'studio_id' => 'required|exists:studios,id',
        ]);

        $liveAccount = LiveAccount::findOrFail($id);
        $liveAccount->update([
            'nama' => $request->nama,
            'studio_id' => $request->studio_id,
        ]);

        return redirect()->route('live-accounts.index')
            ->with('success', "Akun $request->nama berhasil diperbarui!");
    }

    /**
     * Hapus akun.
     */
    public function destroy($id)
    {
        $liveAccount = LiveAccount::findOrFail($id);
        $liveAccount->delete();

        return redirect()->route('live-accounts.index')
            ->with('success', 'Akun live berhasil dihapus!');
    }
}
