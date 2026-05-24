<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merk extends Model
{
    protected $table = 'merk';
    protected $primaryKey = 'id_merk';
    protected $fillable = ['nama_merk', 'asal_negara'];

    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_merk');
    }
}