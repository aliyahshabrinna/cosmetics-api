<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Varian extends Model
{
    protected $table = 'varian';
    protected $primaryKey = 'id_varian';
    protected $fillable = ['id_produk', 'nama_varian', 'kode_warna_hex', 'stok_varian'];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}