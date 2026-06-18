<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json(['success' => true, 'data' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        // Ditambahkan validasi email agar aman dan unik di database users
        $request->validate([
            'nama' => 'sometimes|string|max:150',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'sometimes|string|max:20',
            'alamat' => 'sometimes|string|max:255',
        ]);

        // Izinkan pembaruan kolom email bersama data profile lainnya
        $user->update($request->only(['nama', 'email', 'no_hp', 'alamat']));

        return response()->json([
            'success' => true, 
            'message' => 'Profile berhasil diperbarui.', 
            'data' => $user
        ]);
    }
}