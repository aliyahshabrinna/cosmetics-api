<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    // DITAMBAHKAN: Mengambil semua daftar transaksi milik user untuk halaman Notifikasi
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi.'
            ], 401);
        }

        // Ambil transaksi milik pelanggan saat ini, urutkan dari yang paling baru
        $transaksi = Transaksi::where('id_pelanggan', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transaksi
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_alamat' => 'required',
            'items' => 'required|array',
            'items.*.id_produk' => 'required',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi.'
            ], 401);
        }

        DB::beginTransaction();

        try {
            $transaksi = Transaksi::create([
                'id_pelanggan' => $user->id, 
                'id_alamat' => $request->id_alamat,
                'status' => 'pending', 
                'tanggal_transaksi' => now(),
            ]);

            foreach ($request->items as $item) {
                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi ?? $transaksi->id,
                    'id_produk'    => $item['id_produk'], 
                    'jumlah'       => $item['jumlah'],    
                ]);
            }

            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat!',
                'data' => $transaksi
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat transaksi: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses transaksi di server: ' . $e->getMessage()
            ], 500);
        }
    }
}