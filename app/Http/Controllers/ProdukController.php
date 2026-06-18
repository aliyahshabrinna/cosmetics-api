<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori; 
use Illuminate\Support\Facades\DB; 

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->limit ?? 10;

        $produk = Produk::with([
            'kategori',
            'merk',
            'varian'
        ])->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $produk
        ]);
    }

    public function show($id)
    {
        $produk = Produk::with(['kategori', 'merk', 'varian'])->find($id);
        if (!$produk) return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        return response()->json(['success' => true, 'data' => $produk]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'id_merk'     => 'required|exists:merk,id_merk',
            'nama_produk' => 'required|string|max:150',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
        ]);
        $produk = Produk::create($request->only('id_kategori','id_merk','nama_produk','harga','stok','deskripsi','gambar'));
        return response()->json(['success' => true, 'message' => 'Produk berhasil ditambahkan.', 'data' => $produk], 201);
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        if (!$produk) return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        $request->validate([
            'id_kategori' => 'sometimes|exists:kategori,id_kategori',
            'id_merk'     => 'sometimes|exists:merk,id_merk',
            'nama_produk' => 'sometimes|string|max:150',
            'harga'       => 'sometimes|numeric|min:0',
            'stok'        => 'sometimes|integer|min:0',
        ]);
        $produk->update($request->only('id_kategori','id_merk','nama_produk','harga','stok','deskripsi','gambar'));
        return response()->json(['success' => true, 'message' => 'Produk berhasil diperbarui.', 'data' => $produk]);
    }

    public function destroy($id)
    {
        $produk = Produk::find($id);
        if (!$produk) return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        $produk->delete();
        return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus.']);
    }

    /* =========================================================================
       LOGIKA BARU: KHUSUS HALAMAN UTAMA / HOME (SINKRON DENGAN REAT FILTER)
       ========================================================================= */
    public function getHomeData()
    {
        try {
            // 1. Ambil Kategori Utama untuk slider/menu atas
            $kategori = Kategori::all();

            // 2. Ambil 4 Produk Terlaris + Join Kategori & Merk agar selevel dengan frontend
            $terlaris = Produk::leftJoin('kategori', 'produk.id_kategori', '=', 'kategori.id_kategori')
                ->leftJoin('merk', 'produk.id_merk', '=', 'merk.id_merk')
                ->join('detail_transaksi', 'produk.id_produk', '=', 'detail_transaksi.id_produk')
                ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id_transaksi')
                ->select(
                    'produk.id_produk',
                    'produk.id_kategori',
                    'produk.id_merk',
                    'produk.nama_produk',
                    'produk.harga',
                    'produk.stok',
                    'produk.deskripsi',
                    'produk.gambar',
                    'produk.created_at',
                    'produk.updated_at',
                    'kategori.nama_kategori',
                    'merk.nama_merk',
                    DB::raw('SUM(detail_transaksi.jumlah) as total_terjual') 
                )
                ->where('transaksi.status', '=', 'Selesai') 
                ->groupBy(
                    'produk.id_produk', 
                    'produk.id_kategori',
                    'produk.id_merk',
                    'produk.nama_produk',
                    'produk.harga',
                    'produk.stok',
                    'produk.deskripsi',
                    'produk.gambar',
                    'produk.created_at',
                    'produk.updated_at',
                    'kategori.nama_kategori', 
                    'merk.nama_merk'
                )
                ->orderByDesc('total_terjual')
                ->take(4)
                ->get();

            // 3. Ambil 8 Produk Terbaru + Join Kategori & Merk untuk grid katalog bawah
            $produkTerbaru = Produk::leftJoin('kategori', 'produk.id_kategori', '=', 'kategori.id_kategori')
                ->leftJoin('merk', 'produk.id_merk', '=', 'merk.id_merk')
                ->select('produk.*', 'kategori.nama_kategori', 'merk.nama_merk')
                ->latest('produk.created_at')
                ->take(8)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'kategori' => $kategori,
                    'produk_terlaris' => $terlaris,
                    'produk_terbaru' => $produkTerbaru
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data home: ' . $e->getMessage()
            ], 500);
        }
    }
}

// =========================================================================
// BAGIAN BAWAH TETAP DIPERTAHANKAN (DIUBAH NAMA CLASS AGAR TIDAK DUPLIKAT ERROR 500)
// =========================================================================
namespace App\Http\Controllers;

class ProdukControllerBackup extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->limit ?? 10;

        $produk = Produk::with([
            'kategori',
            'merk',
            'varian'
        ])->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $produk
        ]);
    }

    public function show($id)
    {
        $produk = Produk::with(['kategori', 'merk', 'varian'])->find($id);
        if (!$produk) return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        return response()->json(['success' => true, 'data' => $produk]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'id_merk'     => 'required|exists:merk,id_merk',
            'nama_produk' => 'required|string|max:150',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
        ]);
        $produk = Produk::create($request->only('id_kategori','id_merk','nama_produk','harga','stok','deskripsi','gambar'));
        return response()->json(['success' => true, 'message' => 'Produk berhasil ditambahkan.', 'data' => $produk], 201);
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        if (!$produk) return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        $request->validate([
            'id_kategori' => 'sometimes|exists:kategori,id_kategori',
            'id_merk'     => 'sometimes|exists:merk,id_merk',
            'nama_produk' => 'sometimes|string|max:150',
            'harga'       => 'sometimes|numeric|min:0',
            'stok'        => 'sometimes|integer|min:0',
        ]);
        $produk->update($request->only('id_kategori','id_merk','nama_produk','harga','stok','deskripsi','gambar'));
        return response()->json(['success' => true, 'message' => 'Produk berhasil diperbarui.', 'data' => $produk]);
    }

    public function destroy($id)
    {
        $produk = Produk::find($id);
        if (!$produk) return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        $produk->delete();
        return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus.']);
    }

    public function getHomeData()
    {
        try {
            $kategori = Kategori::all();

            $terlaris = Produk::leftJoin('kategori', 'produk.id_kategori', '=', 'kategori.id_kategori')
                ->leftJoin('merk', 'produk.id_merk', '=', 'merk.id_merk')
                ->join('detail_transaksi', 'produk.id_produk', '=', 'detail_transaksi.id_produk')
                ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id_transaksi')
                ->select(
                    'produk.id_produk',
                    'produk.id_kategori',
                    'produk.id_merk',
                    'produk.nama_produk',
                    'produk.harga',
                    'produk.stok',
                    'produk.deskripsi',
                    'produk.gambar',
                    'produk.created_at',
                    'produk.updated_at',
                    'kategori.nama_kategori',
                    'merk.nama_merk',
                    DB::raw('SUM(detail_transaksi.jumlah) as total_terjual')
                )
                ->where('transaksi.status', '=', 'Selesai') 
                ->groupBy(
                    'produk.id_produk', 
                    'produk.id_kategori',
                    'produk.id_merk',
                    'produk.nama_produk',
                    'produk.harga',
                    'produk.stok',
                    'produk.deskripsi',
                    'produk.gambar',
                    'produk.created_at',
                    'produk.updated_at',
                    'kategori.nama_kategori', 
                    'merk.nama_merk'
                )
                ->orderByDesc('total_terjual')
                ->take(4)
                ->get();

            $produkTerbaru = Produk::leftJoin('kategori', 'produk.id_kategori', '=', 'kategori.id_kategori')
                ->leftJoin('merk', 'produk.id_merk', '=', 'merk.id_merk')
                ->select('produk.*', 'kategori.nama_kategori', 'merk.nama_merk')
                ->latest('produk.created_at')
                ->take(8)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'kategori' => $kategori,
                    'produk_terlaris' => $produkTerlaris,
                    'produk_terbaru' => $produkTerbaru
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data home: ' . $e->getMessage()
            ], 500);
        }
    }
}