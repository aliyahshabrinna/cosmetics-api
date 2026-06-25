<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:5',
            'nama'     => 'nullable|string',
            'email'    => 'nullable|string',
            'hp'       => 'nullable|string',
            'role'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Buat token string acak manual
        $manualToken = bin2hex(random_bytes(40));

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama'     => $request->nama ?? $request->username,
            'email'    => $request->email,
            'hp'       => $request->hp,
            'role'     => $request->role ?? 'pelanggan',
            'token'    => $manualToken,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil.',
            'token'   => $manualToken,
            'user'    => $user,
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

        // Buat token string acak manual & simpan ke database
        $manualToken = bin2hex(random_bytes(40));
        $user->update(['token' => $manualToken]);

        return response()->json([
            'success' => true,
            'token'   => $manualToken,
            'user'    => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}