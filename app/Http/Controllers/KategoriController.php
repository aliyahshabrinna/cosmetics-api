<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->limit ?? 10;

        $data = Kategori::paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $kategori = Kategori::find($id);
        if (!$kategori) return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan.'], 404);
        return response()->json(['success' => true, 'data' => $kategori]);
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kategori' => 'required|string|max:100']);
        $kategori = Kategori::create($request->only('nama_kategori'));
        return response()->json(['success' => true, 'message' => 'Kategori berhasil ditambahkan.', 'data' => $kategori], 201);
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::find($id);
        if (!$kategori) return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan.'], 404);
        $request->validate(['nama_kategori' => 'sometimes|string|max:100']);
        $kategori->update($request->only('nama_kategori'));
        return response()->json(['success' => true, 'message' => 'Kategori berhasil diperbarui.', 'data' => $kategori]);
    }

    public function destroy($id)
    {
        $kategori = Kategori::find($id);
        if (!$kategori) return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan.'], 404);
        // delete related products (produk) which will cascade to varian and detail via model boot
        $kategori->produk()->each(function ($produk) {
            $produk->delete();
        });
        $kategori->delete();
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
    }
}