<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    // ===============================
    // STATISTIK HARIAN
    // ===============================
    public function harian(Request $request)
    {
        $data = Transaksi::selectRaw('
                DATE(tanggal_transaksi) as tanggal,
                COUNT(*) as jumlah_transaksi,
                SUM(total_bayar) as total_pendapatan
            ')
            ->groupByRaw('DATE(tanggal_transaksi)')
            ->orderBy('tanggal', 'DESC')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // ===============================
    // STATISTIK BULANAN
    // ===============================
    public function bulanan(Request $request)
{
    $data = Transaksi::selectRaw('
            YEAR(tanggal_transaksi) as tahun,
            MONTH(tanggal_transaksi) as bulan,
            MONTHNAME(tanggal_transaksi) as nama_bulan,
            COUNT(*) as jumlah_transaksi,
            SUM(total_bayar) as total_pendapatan
        ')
        ->groupByRaw('
            YEAR(tanggal_transaksi),
            MONTH(tanggal_transaksi),
            MONTHNAME(tanggal_transaksi)
        ')
        ->orderByRaw('tahun DESC, bulan DESC')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}
    // ===============================
    // STATISTIK TAHUNAN
    // ===============================
    public function tahunan(Request $request)
    {
        $data = Transaksi::selectRaw('
                YEAR(tanggal_transaksi) as tahun,
                COUNT(*) as jumlah_transaksi,
                SUM(total_bayar) as total_pendapatan
            ')
            ->groupByRaw('YEAR(tanggal_transaksi)')
            ->orderByRaw('tahun DESC')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // ===============================
    // PRODUK TERLARIS
    // ===============================
    public function produkTerlaris()
{
    $data = DetailTransaksi::select(
            'id_produk',
            DB::raw('SUM(jumlah) as total_terjual'),
            DB::raw('SUM(subtotal) as total_pendapatan')
        )
        ->with('produk:id_produk,nama_produk,harga')
        ->groupBy('id_produk')
        ->orderByDesc('total_terjual')
        ->limit(10)
        ->get();

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}
    // ===============================
    // RINGKASAN
    // ===============================
    public function ringkasan()
    {
        $data = [
            'total_transaksi' => Transaksi::count(),

            'total_pendapatan' => Transaksi::sum('total_bayar'),

            'total_produk' => Produk::count(),

            'stok_habis' => Produk::where('stok', 0)->count(),

            'transaksi_hari_ini' => Transaksi::whereDate(
                'tanggal_transaksi',
                today()
            )->count(),

            'pendapatan_bulan_ini' => Transaksi::whereMonth(
                    'tanggal_transaksi',
                    now()->month
                )
                ->whereYear(
                    'tanggal_transaksi',
                    now()->year
                )
                ->sum('total_bayar'),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}