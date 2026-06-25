\use Illuminate\Http\Request;
use App\Models\User; // atau App\Models\Pelanggan tergantung strukturmu
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

public function register(Request $request)
{
    // 1. Validasi Input secara Ketat
    $validator = Validator::make($request->all(), [
        'username' => 'required|string|unique:users,username',
        'password' => 'required|string|min:6',
        'nama' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'hp' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // 2. Gunakan Try-Catch untuk Menangkap Masalah Database / Enskripsi
    try {
        $user = User::create([
            'username' => $request->username,
            'password' => bcrypt($request->password), // atau Hash::make($request->password)
            'nama' => $request->nama,
            'email' => $request->email,
            'hp' => $request->hp,
            'role' => $request->role ?? 'pelanggan',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil',
            'data' => $user
        ], 201);

    } catch (\Exception $e) {
        // Jika database crash, kirim pesan error aslinya ke Frontend!
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan ke database: ' . $e->getMessage()
        ], 500);
    }
}