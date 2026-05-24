<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $fillable = ['id_kategori', 'id_merk', 'nama_produk', 'harga', 'stok', 'deskripsi', 'gambar'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function merk()
    {
        return $this->belongsTo(Merk::class, 'id_merk');
    }

    public function varian()
    {
        return $this->hasMany(Varian::class, 'id_produk');
    }

    public function details()
    {
        return $this->hasMany(\App\Models\DetailTransaksi::class, 'id_produk');
    }

    protected static function booted()
    {
        static::deleting(function ($produk) {
            // delete related varian and detail_transaksi
            $produk->varian()->delete();
            $produk->details()->delete();
        });
    }
}