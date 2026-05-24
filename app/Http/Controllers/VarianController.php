<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Varian;

class VarianController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->limit ?? 10;

        $data = Varian::with('produk')->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $varian = Varian::with('produk')->find($id);
        if (!$varian) return response()->json(['success' => false, 'message' => 'Varian tidak ditemukan.'], 404);
        return response()->json(['success' => true, 'data' => $varian]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk'      => 'required|exists:produk,id_produk',
            'nama_varian'    => 'required|string|max:100',
            'kode_warna_hex' => 'nullable|string|max:10',
            'stok_varian'    => 'required|integer|min:0',
        ]);
        $varian = Varian::create($request->only('id_produk','nama_varian','kode_warna_hex','stok_varian'));
        return response()->json(['success' => true, 'message' => 'Varian berhasil ditambahkan.', 'data' => $varian], 201);
    }

    public function update(Request $request, $id)
    {
        $varian = Varian::find($id);
        if (!$varian) return response()->json(['success' => false, 'message' => 'Varian tidak ditemukan.'], 404);
        $request->validate([
            'nama_varian'    => 'sometimes|string|max:100',
            'kode_warna_hex' => 'nullable|string|max:10',
            'stok_varian'    => 'sometimes|integer|min:0',
        ]);
        $varian->update($request->only('nama_varian','kode_warna_hex','stok_varian'));
        return response()->json(['success' => true, 'message' => 'Varian berhasil diperbarui.', 'data' => $varian]);
    }

    public function destroy($id)
    {
        $varian = Varian::find($id);
        if (!$varian) return response()->json(['success' => false, 'message' => 'Varian tidak ditemukan.'], 404);
        $varian->delete();
        return response()->json(['success' => true, 'message' => 'Varian berhasil dihapus.']);
    }
}