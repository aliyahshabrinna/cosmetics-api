<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <-- PASTIKAN INI ADA

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // <-- WAJIB MASUKKAN HasApiTokens DI SINI

    protected $table = 'users';

    protected $fillable = [
        'username',
        'nama',
        'email',
        'password',
        'hp',
        'role',
        'kode_unik',
        'token',
        'token_created_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}