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
| 1. ROUTE PUBLIK (Bebas Diakses Siapa Saja, Bebas dari Sanctum)
|--------------------------------------------------------------------------
*/

Route::get('/pindah-database-aliyah', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return response()->json([
            'success' => true,
            'message' => 'Database berhasil diperbarui!',
            'output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal migrasi: ' . $e->getMessage()
        ], 500);
    }
});

// Pastikan dua rute ini berada di luar dan di atas middleware auth:sanctum!
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); 

Route::get('/produk', [ProdukController::class, 'index']);
Route::get('/produk/{id}', [ProdukController::class, 'show']);
Route::get('/home-data', [ProdukController::class, 'getHomeData']);


/*
|--------------------------------------------------------------------------
| 2. ROUTE PROTECTED (Hanya Bisa Diakses Jika Sudah Login / Punya Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);

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

    Route::get('/notifications', [TransaksiController::class, 'index']);

    Route::prefix('statistik')->group(function () {
        Route::get('/harian', [StatistikController::class, 'harian']);
        Route::get('/bulanan', [StatistikController::class, 'bulanan']);
        Route::get('/tahunan', [StatistikController::class, 'tahunan']);
        Route::get('/produk-terlaris', [StatistikController::class, 'produkTerlaris']);
        Route::get('/ringkasan', [StatistikController::class, 'ringkasan']);
    });
    
});