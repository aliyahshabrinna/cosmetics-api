<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';
    
    // SINKRONISASI: Ubah id_cart menjadi id sesuai database asli
    protected $primaryKey = 'id'; 
    
    protected $fillable = ['user_id', 'produk_id', 'qty'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function produk()
    {
        // Sesuaikan target primary key tabel produk kamu (id_produk)
        return $this->belongsTo(Produk::class, 'produk_id', 'id_produk');
    }
}