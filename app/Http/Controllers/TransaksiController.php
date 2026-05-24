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
        return response()->json(['success' => true, 'data' => Transaksi::with(['pelanggan', 'details.product', 'details.user'])->get()]);
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'details.product', 'details.user'])->find($id);
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
                DetailTransaksi::create(array_merge($d, ['id_transaksi' => $transaksi->id_transaksi, 'id_user' => auth()->user()->id ?? null]));
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dibuat.', 'data' => $transaksi->load(['pelanggan', 'details.product', 'details.user'])], 201);

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
        $transaksi = Transaksi::find($id);
        if (!$transaksi) return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);

        $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'jumlah'    => 'required|integer|min:1',
        ]);

        $produk = Produk::find($request->id_produk);
        
        // Check jika detail sudah ada
        $detail = DetailTransaksi::where('id_transaksi', $id)
            ->where('id_produk', $request->id_produk)
            ->first();

        if ($detail) {
            // Jika sudah ada, update qty dan subtotal
            if ($produk->stok < $request->jumlah) {
                return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi.'], 422);
            }
            $oldSubtotal = $detail->subtotal;
            $newQty = $detail->jumlah + $request->jumlah;
            $newSubtotal = $produk->harga * $newQty;
            $addedSubtotal = $newSubtotal - $oldSubtotal;
            
            $detail->jumlah = $newQty;
            $detail->subtotal = $newSubtotal;
            $detail->save();
            
            $transaksi->increment('total_bayar', $addedSubtotal);
            $produk->decrement('stok', $request->jumlah);
            
            return response()->json(['success' => true, 'message' => 'Detail berhasil diperbarui.', 'data' => $detail->load('product','user')], 200);
        } else {
            // Insert baru jika belum ada
            if ($produk->stok < $request->jumlah) {
                return response()->json(['success' => false, 'message' => 'Stok tidak mencukupi.'], 422);
            }

            $subtotal = $produk->harga * $request->jumlah;
            $detail = DetailTransaksi::create([
                'id_transaksi' => $id,
                'id_produk'    => $request->id_produk,
                'id_user'      => auth()->user()->id ?? null,
                'jumlah'       => $request->jumlah,
                'subtotal'     => $subtotal,
            ]);

            $transaksi->increment('total_bayar', $subtotal);
            $produk->decrement('stok', $request->jumlah);

            return response()->json(['success' => true, 'message' => 'Detail berhasil ditambahkan.', 'data' => $detail->load('product','user')], 201);
        }
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