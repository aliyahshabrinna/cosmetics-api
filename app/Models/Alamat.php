<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    protected $table = 'alamat';
    protected $primaryKey = 'id_alamat';
    protected $fillable = ['user_id', 'nama_penerima', 'no_hp', 'alamat', 'kota', 'provinsi', 'kode_pos'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
