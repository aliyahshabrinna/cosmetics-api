<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // 👈 Menggunakan model User langsung

class PelangganController extends Controller
{
    // Mengambil semua user dengan role pelanggan
    public function index(Request $request)
    {
        $limit = $request->limit ?? 10;

        // Ambil data dari tabel users yang rolenya adalah pelanggan
        $data = User::where('role', 'pelanggan')->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    // Mengambil detail profil pelanggan berdasarkan ID
    public function show($id)
    {
        $pelanggan = User::where('role', 'pelanggan')->find($id);
        
        if (!$pelanggan) {
            return response()->json(['success' => false, 'message' => 'Pelanggan tidak ditemukan.'], 404);
        }
        
        return response()->json(['success' => true, 'data' => $pelanggan], 200);
    }

    // Menambah pelanggan baru langsung ke tabel users
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username|max:100',
            'password' => 'required|string|min:5',
            'nama'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email',
            'hp'       => 'nullable|string|max:20',
        ]);

        $pelanggan = User::create([
            'username' => $request->username,
            'password' => bcrypt($request->password), // Enkripsi password
            'nama'     => $request->nama,
            'email'    => $request->email,
            'hp'       => $request->hp,
            'role'     => 'pelanggan',
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Pelanggan berhasil ditambahkan.', 
            'data' => $pelanggan
        ], 201);
    }

    // Memperbarui data profil pelanggan di tabel users
    public function update(Request $request, $id)
    {
        $pelanggan = User::where('role', 'pelanggan')->find($id);
        if (!$pelanggan) {
            return response()->json(['success' => false, 'message' => 'Pelanggan tidak ditemukan.'], 404);
        }

        $request->validate([
            'username' => 'sometimes|string|unique:users,username,'.$id,
            'nama'     => 'sometimes|string|max:150',
            'email'    => 'sometimes|email|unique:users,email,'.$id,
            'hp'       => 'nullable|string|max:20',
        ]);

        $pelanggan->update($request->only('username', 'nama', 'email', 'hp'));

        return response()->json([
            'success' => true, 
            'message' => 'Pelanggan berhasil diperbarui.', 
            'data' => $pelanggan
        ], 200);
    }

    // Menghapus akun pelanggan dari database
    public function destroy($id)
    {
        $pelanggan = User::where('role', 'pelanggan')->find($id);
        if (!$pelanggan) {
            return response()->json(['success' => false, 'message' => 'Pelanggan tidak ditemukan.'], 404);
        }

        // Jika ada relasi transaksi ke user ini, hapus dulu transaksinya (opsional)
        if (method_exists($pelanggan, 'transaksi')) {
            $pelanggan->transaksi()->delete();
        }

        $pelanggan->delete();
        return response()->json(['success' => true, 'message' => 'Pelanggan berhasil dihapus.'], 200);
    }
}