<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\Pelanggan;

class StatistikController extends Controller
{
    public function ringkasan()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_produk' => Produk::count(),
                'total_transaksi' => Transaksi::count(),
                'total_pelanggan' => Pelanggan::count(),
            ]
        ]);
    }

    public function transaksiPerBulan()
    {
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    public function produkTerlaris()
    {
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }
}