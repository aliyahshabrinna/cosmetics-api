<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->limit ?? 10;

        $data = Pelanggan::paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $pelanggan = Pelanggan::find($id);
        if (!$pelanggan) return response()->json(['success' => false, 'message' => 'Pelanggan tidak ditemukan.'], 404);
        return response()->json(['success' => true, 'data' => $pelanggan]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:150',
            'email'          => 'required|email|unique:pelanggan,email',
            'telepon'        => 'nullable|string|max:20',
        ]);
        $pelanggan = Pelanggan::create($request->only('nama_pelanggan','email','telepon'));
        return response()->json(['success' => true, 'message' => 'Pelanggan berhasil ditambahkan.', 'data' => $pelanggan], 201);
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::find($id);
        if (!$pelanggan) return response()->json(['success' => false, 'message' => 'Pelanggan tidak ditemukan.'], 404);
        $request->validate([
            'nama_pelanggan' => 'sometimes|string|max:150',
            'email'          => 'sometimes|email|unique:pelanggan,email,'.$id.',id_pelanggan',
            'telepon'        => 'nullable|string|max:20',
        ]);
        $pelanggan->update($request->only('nama_pelanggan','email','telepon'));
        return response()->json(['success' => true, 'message' => 'Pelanggan berhasil diperbarui.', 'data' => $pelanggan]);
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::find($id);
        if (!$pelanggan) return response()->json(['success' => false, 'message' => 'Pelanggan tidak ditemukan.'], 404);
        // delete related transaksi (and their details via Transaksi model boot)
        $pelanggan->transaksi()->each(function ($trx) {
            $trx->delete();
        });
        $pelanggan->delete();
        return response()->json(['success' => true, 'message' => 'Pelanggan berhasil dihapus.']);
    }
}