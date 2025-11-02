<?php

namespace App\Http\Controllers;

use App\Models\LiveAccount;
use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LiveAccountController extends Controller
{
    /**
     * Tampilkan daftar akun live.
     */
    public function index()
    {
        $liveAccounts = LiveAccount::with('studio')->get();
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
     * Simpan akun baru + ambil data dari Shopee API.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'cookie' => 'required|string',
            'studio_id' => 'required|exists:studios,id',
        ]);

        try {
            // Kirim request ke Shopee Affiliate API
            $response = Http::withHeaders([
                'Cookie' => $request->cookie,
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'Accept' => 'application/json, text/plain, */*',
                'Referer' => 'https://affiliate.shopee.co.id/',
                ])->get('https://affiliate.shopee.co.id/api/v3/user/profile');
                // Cek apakah response sukses
                if (!$response->successful()) {
                    return back()->with('error', 'Gagal mengambil data dari Shopee. Pastikan cookie valid.');
                }
                
                $data = $response->json();
                
            // Pastikan struktur data sesuai
            if (!isset($data['data']['user_id'])) {
                return back()->with('error', 'Cookie tidak valid atau sesi login telah kedaluwarsa.');
            }

            // Ambil data penting
            $userId = $data['data']['user_id'];
            $username = $data['data']['shopee_user_name'] ?? '-';
            $affiliateId = $data['data']['affiliate_id'] ?? '-';

            // Simpan ke database
            LiveAccount::insert(
                [
                    'user_id' => $userId,
                    'shopee_user_name' => $username,
                    'affiliate_id' => $affiliateId,
                    'studio_id' => $request->studio_id,
                    'cookies' => $request->cookie,
                    // 'status' => 'aktif'
                ]
            );

            return redirect()->route('live-accounts.index')
                ->with('success', "Akun ". $username. "berhasil ditambahkan!");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Edit akun live.
     */
    public function edit($user_id)
    {
        $liveAccount = LiveAccount::findOrFail($user_id);
        $studios = Studio::all();
        return view('live_accounts.edit', compact('liveAccount', 'studios'));
    }

    /**
     * Update data akun.
     */
    public function update(Request $request, $user_id)
    {
        $request->validate([
            'cookie' => 'required|string',
            'studio_id' => 'required|exists:studios,id',
        ]);

        $liveAccount = LiveAccount::where('user_id', $user_id)->firstOrFail();

        try {
            // 🔹 Ambil data baru dari Shopee API pakai cookie terbaru
            $response = Http::withHeaders([
                'Cookie' => $request->cookie,
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'Accept' => 'application/json, text/plain, */*',
                'Referer' => 'https://affiliate.shopee.co.id/',
            ])->get('https://affiliate.shopee.co.id/api/v3/user/profile');

            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengambil data dari Shopee. Pastikan cookie valid.');
            }

            $data = $response->json();

            if (!isset($data['data']['user_id'])) {
                return back()->with('error', 'Cookie tidak valid atau sesi login telah kedaluwarsa.');
            }

            // 🔹 Ambil data dari response
            $newUserId = $data['data']['user_id'];
            $username = $data['data']['shopee_user_name'] ?? '-';
            $affiliateId = $data['data']['affiliate_id'] ?? '-';

            // 🔹 Update data di database
            $liveAccount->update([
                'user_id' => $newUserId,
                'shopee_user_name' => $username,
                'affiliate_id' => $affiliateId,
                'cookies' => $request->cookie,
                'studio_id' => $request->studio_id,
            ]);

            return redirect()->back()->with('success', "Akun $username berhasil diperbarui!");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus akun.
     */
    public function destroy($user_id)
    {
        $liveAccount = LiveAccount::findOrFail($user_id);
        $liveAccount->delete();

        return redirect()->route('live-accounts.index')
            ->with('success', 'Akun live berhasil dihapus!');
    }
}
