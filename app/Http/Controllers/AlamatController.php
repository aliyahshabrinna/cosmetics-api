<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alamat;

class AlamatController extends Controller
{
    public function index(Request $request)
    {
        $alamat = Alamat::where('user_id', $request->user()->id)->get();

        return response()->json(['success' => true, 'data' => $alamat]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_penerima' => 'required|string|max:150',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'kota' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kode_pos' => 'required|string|max:20',
        ]);

        $alamat = Alamat::create(array_merge($request->only([
            'nama_penerima',
            'no_hp',
            'alamat',
            'kota',
            'provinsi',
            'kode_pos'
        ]), ['user_id' => $request->user()->id]));

        return response()->json(['success' => true, 'message' => 'Alamat berhasil ditambahkan.', 'data' => $alamat], 201);
    }

    public function update(Request $request, $id)
    {
        $alamat = Alamat::where('id_alamat', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$alamat) {
            return response()->json(['success' => false, 'message' => 'Alamat tidak ditemukan.'], 404);
        }

        $request->validate([
            'nama_penerima' => 'sometimes|string|max:150',
            'no_hp' => 'sometimes|string|max:20',
            'alamat' => 'sometimes|string|max:255',
            'kota' => 'sometimes|string|max:100',
            'provinsi' => 'sometimes|string|max:100',
            'kode_pos' => 'sometimes|string|max:20',
        ]);

        $alamat->update($request->only([
            'nama_penerima',
            'no_hp',
            'alamat',
            'kota',
            'provinsi',
            'kode_pos'
        ]));

        return response()->json(['success' => true, 'message' => 'Alamat berhasil diperbarui.', 'data' => $alamat]);
    }

    public function destroy(Request $request, $id)
    {
        $alamat = Alamat::where('id_alamat', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$alamat) {
            return response()->json(['success' => false, 'message' => 'Alamat tidak ditemukan.'], 404);
        }

        $alamat->delete();

        return response()->json(['success' => true, 'message' => 'Alamat berhasil dihapus.']);
    }
}
