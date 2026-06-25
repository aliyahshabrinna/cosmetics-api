<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk'); // Menggunakan id_produk sesuai HeidiSQL
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->unsignedBigInteger('id_merk')->nullable();
            $table->string('nama_produk', 150);
            $table->decimal('harga', 12, 2)->default(0.00);
            $table->integer('stok')->default(0);
            $table->text('deskripsi')->nullable();
            $table->text('cara_pakai')->nullable();
            $table->text('ingredients')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};