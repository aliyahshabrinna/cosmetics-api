<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validasi semua data yang dikirim dari React
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:5', // Kita turunkan ke min:5 agar pas dengan inputanmu
            'nama'     => 'required|string',
            'email'    => 'required|string|email|unique:users,email',
            'hp'       => 'required|string',
            'role'     => 'string',
        ]);

        // 2. Simpan semua datanya ke table users
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama'     => $request->nama,
            'email'    => $request->email,
            'hp'       => $request->hp,
            'role'     => $request->role ?? 'pelanggan',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil.',
            'user' => $user,
        ], 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}