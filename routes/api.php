<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\MerkController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\VarianController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| ROUTE PUBLIK (Bisa Diakses Tanpa Login)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); 

Route::get('/produk', [ProdukController::class, 'index']);
Route::get('/produk/{id}', [ProdukController::class, 'show']);
Route::get('/home-data', [ProdukController::class, 'getHomeData']);

Route::get('/pindah-database-aliyah', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "Database berhasil diperbarui: " . \Illuminate\Support\Facades\Artisan::output();
    } catch (\Exception $e) {
        return "Gagal migrasi: " . $e->getMessage();
    }
});
// Rute pembantu jika ada request GET ke /api/login lewat browser biasa
Route::get('/login', function () {
    return response()->json([
        'success' => false,
        'message' => 'Sesi Anda telah habis atau Token tidak valid. Silakan lakukan POST login kembali.'
    ], 401);
})->name('login');


/*
|--------------------------------------------------------------------------
| ROUTE PROTECTED (Harus Login / Pakai Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | DATA MASTER (Untuk Admin)
    |--------------------------------------------------------------------------
    |*/
    Route::apiResource('kategori', KategoriController::class);
    Route::apiResource('merk', MerkController::class);
    
    Route::post('/produk', [ProdukController::class, 'store']);
    $id = '{id}';
    Route::put("/produk/$id", [ProdukController::class, 'update']);
    Route::delete("/produk/$id", [ProdukController::class, 'destroy']);
    
    Route::apiResource('varian', VarianController::class);
    Route::apiResource('pelanggan', PelangganController::class);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::apiResource('cart', CartController::class)->except(['show']);
    Route::apiResource('wishlist', WishlistController::class)->except(['show', 'update']);

    Route::apiResource('alamat', AlamatController::class);

    /*
    |--------------------------------------------------------------------------
    | DATA TRANSAKSIONAL
    |--------------------------------------------------------------------------
    |*/
    Route::get('/orders', [TransaksiController::class, 'index']); 
    Route::get('/my-orders', [TransaksiController::class, 'index']);
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    
    Route::post('/transaksi', [TransaksiController::class, 'store']);
    Route::post('/orders', [TransaksiController::class, 'store']); 
    
    Route::get("/transaksi/$id", [TransaksiController::class, 'show']);
    Route::delete("/transaksi/$id", [TransaksiController::class, 'destroy']);

    Route::post("/transaksi/$id/detail", [TransaksiController::class, 'storeDetail']);
    $idDetail = '{idDetail}';
    Route::delete("/transaksi/$id/detail/$idDetail", [TransaksiController::class, 'destroyDetail']);

    /*
    |--------------------------------------------------------------------------
    | NOTIFIKASI / RIWAYAT TRANSAKSI
    |--------------------------------------------------------------------------
    |*/
    Route::get('/notifications', [TransaksiController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | STATISTIK
    |--------------------------------------------------------------------------
    |*/
    Route::prefix('statistik')->group(function () {
        Route::get('/harian', [StatistikController::class, 'harian']);
        Route::get('/bulanan', [StatistikController::class, 'bulanan']);
        Route::get('/tahunan', [StatistikController::class, 'tahunan']);
        Route::get('/produk-terlaris', [StatistikController::class, 'produkTerlaris']);
        Route::get('/ringkasan', [StatistikController::class, 'ringkasan']);
    });
    // Tempelkan ini di baris paling akhir file routes/api.php kamu

});