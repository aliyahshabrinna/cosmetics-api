<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merk;

class MerkController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->limit ?? 10;

        $data = Merk::paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $merk = Merk::find($id);
        if (!$merk) return response()->json(['success' => false, 'message' => 'Merk tidak ditemukan.'], 404);
        return response()->json(['success' => true, 'data' => $merk]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_merk'   => 'required|string|max:100',
            'asal_negara' => 'required|string|max:100',
        ]);
        $merk = Merk::create($request->only('nama_merk', 'asal_negara'));
        return response()->json(['success' => true, 'message' => 'Merk berhasil ditambahkan.', 'data' => $merk], 201);
    }

    public function update(Request $request, $id)
    {
        $merk = Merk::find($id);
        if (!$merk) return response()->json(['success' => false, 'message' => 'Merk tidak ditemukan.'], 404);
        $request->validate([
            'nama_merk'   => 'sometimes|string|max:100',
            'asal_negara' => 'sometimes|string|max:100',
        ]);
        $merk->update($request->only('nama_merk', 'asal_negara'));
        return response()->json(['success' => true, 'message' => 'Merk berhasil diperbarui.', 'data' => $merk]);
    }

    public function destroy($id)
    {
        $merk = Merk::find($id);
        if (!$merk) return response()->json(['success' => false, 'message' => 'Merk tidak ditemukan.'], 404);
        // delete related products (produk) which will cascade to varian and detail via model boot
        $merk->produk()->each(function ($produk) {
            $produk->delete();
        });
        $merk->delete();
        return response()->json(['success' => true, 'message' => 'Merk berhasil dihapus.']);
    }
}