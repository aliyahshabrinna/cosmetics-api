<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

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
}