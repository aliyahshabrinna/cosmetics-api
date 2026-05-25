<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        return response()->json(['success' => true, 'data' => Transaksi::with(['pelanggan', 'details.produk'])->get()]);
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'details.produk'])->find($id);
        if (!$transaksi) return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
        return response()->json(['success' => true, 'data' => $transaksi]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan'      => 'required|exists:pelanggan,id_pelanggan',
            'items'             => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produk,id_produk',
            'items.*.jumlah'    => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $totalBayar = 0;
            $details = [];

            foreach ($request->items as $item) {
                $produk = Produk::find($item['id_produk']);
                if ($produk->stok < $item['jumlah']) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => "Stok produk '{$produk->nama_produk}' tidak mencukupi."], 422);
                }
                $subtotal = $produk->harga * $item['jumlah'];
                $totalBayar += $subtotal;
                $details[] = ['id_produk' => $item['id_produk'], 'jumlah' => $item['jumlah'], 'subtotal' => $subtotal];
                $produk->decrement('stok', $item['jumlah']);
            }

            $transaksi = Transaksi::create([
                'id_pelanggan'      => $request->id_pelanggan,
                'tanggal_transaksi' => now(),
                'total_bayar'       => $totalBayar,
            ]);

            foreach ($details as $d) {
    DetailTransaksi::create(array_merge($d, [
        'id_transaksi' => $transaksi->id_transaksi
    ]));
}

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dibuat.', 'data' => $transaksi->load(['pelanggan', 'details.produk'])], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);
        if (!$transaksi) return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
        $transaksi->delete();
        return response()->json(['success' => true, 'message' => 'Transaksi berhasil dihapus.']);
    }

   public function storeDetail(Request $request, $id)
{
    $request->validate([
        'id_produk' => 'required|exists:produk,id_produk',
        'jumlah' => 'required|integer|min:1'
    ]);

    $transaksi = Transaksi::find($id);

    if (!$transaksi) {
        return response()->json([
            'success' => false,
            'message' => 'Transaksi tidak ditemukan'
        ], 404);
    }

    $produk = Produk::find($request->id_produk);

    if (!$produk) {
        return response()->json([
            'success' => false,
            'message' => 'Produk tidak ditemukan'
        ], 404);
    }

    if ($produk->stok < $request->jumlah) {
        return response()->json([
            'success' => false,
            'message' => 'Stok tidak cukup'
        ], 422);
    }

    $subtotal = $produk->harga * $request->jumlah;

    $detail = DetailTransaksi::create([
        'id_transaksi' => $id,
        'id_produk' => $request->id_produk,
        'jumlah' => $request->jumlah,
        'subtotal' => $subtotal
    ]);

    $produk->stok -= $request->jumlah;
    $produk->save();

    $transaksi->total_bayar =
        DetailTransaksi::where('id_transaksi', $id)
        ->sum('subtotal');

    $transaksi->save();

    return response()->json([
        'success' => true,
        'message' => 'Detail transaksi berhasil ditambahkan',
        'data' => $detail
    ]);
}

    public function destroyDetail($id, $idDetail)
    {
        $detail = DetailTransaksi::where('id_transaksi', $id)->where('id_detail', $idDetail)->first();
        if (!$detail) return response()->json(['success' => false, 'message' => 'Detail tidak ditemukan.'], 404);

        $transaksi = Transaksi::find($id);
        $transaksi->decrement('total_bayar', $detail->subtotal);
        Produk::find($detail->id_produk)->increment('stok', $detail->jumlah);
        $detail->delete();

        return response()->json(['success' => true, 'message' => 'Detail berhasil dihapus.']);
    }
}