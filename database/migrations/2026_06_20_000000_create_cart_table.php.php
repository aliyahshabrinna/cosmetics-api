<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart', function (Blueprint $table) {
            $table->id(); // Menggunakan id sesuai screenshot HeidiSQL lokal kamu
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Kolom produk_id bertipe INT/BIGINT yang mengarah ke id_produk di tabel produk
            $table->foreignId('produk_id')->constrained('produk', 'id_produk')->onDelete('cascade');
            
            $table->integer('qty')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};