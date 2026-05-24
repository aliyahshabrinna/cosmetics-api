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

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| DATA MASTER
|--------------------------------------------------------------------------
*/

Route::apiResource('kategori', KategoriController::class);
Route::apiResource('merk', MerkController::class);
Route::apiResource('produk', ProdukController::class);
Route::apiResource('varian', VarianController::class);
Route::apiResource('pelanggan', PelangganController::class);

/*
|--------------------------------------------------------------------------
| DATA TRANSAKSIONAL
|--------------------------------------------------------------------------
*/

Route::get('/transaksi', [TransaksiController::class, 'index']);
Route::post('/transaksi', [TransaksiController::class, 'store']);
Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);
Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy']);

Route::post('/transaksi/{id}/detail', [TransaksiController::class, 'storeDetail']);
Route::delete('/transaksi/{id}/detail/{idDetail}', [TransaksiController::class, 'destroyDetail']);

/*
|--------------------------------------------------------------------------
| STATISTIK
|--------------------------------------------------------------------------
*/

Route::get('/statistik/ringkasan', [StatistikController::class, 'ringkasan']);
Route::get('/statistik/transaksi', [StatistikController::class, 'transaksiPerBulan']);
Route::get('/statistik/produk-terlaris', [StatistikController::class, 'produkTerlaris']);