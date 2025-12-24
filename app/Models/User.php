<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'mas_user'; // Nama tabel sesuai database
    protected $primaryKey = 'username'; // Primary key string
    public $incrementing = false;
    protected $keyType = 'string';

    // KOLOM YANG BISA DIEDIT (WAJIB DITAMBAHKAN AGAR FORM UPDATE BERFUNGSI)
    protected $fillable = [
        'username', 
        'nama', 
        'password', 
        'level', 
        'group', 
        'status', 
        'nama_client',
        // Tambahan kolom baru untuk fitur Update Profile:
        'email',
        'phone',
        'position',
        'password_md5' 
    ];

    // Sembunyikan password dari return array/json
    protected $hidden = [
        'password', 'password_md5',
    ];

    // Override validasi password karena database menggunakan MD5
    public function getAuthPassword()
    {
        return $this->password;
    }

    // --- Helper Cek Role ---
    public function isInspektor() {
        return strtolower($this->level) === 'inspektor';
    }

    public function isSuperAdmin() {
        return strtolower($this->level) === 'super admin';
    }

    public function isVerification() {
        return strtolower($this->level) === 'verification';
    }

    // --- Relasi ke Tabel Cabang (RefCabang) ---
    // Digunakan untuk menampilkan Nama Cabang di Header
    public function cabang()
    {
        // Menghubungkan kolom 'group' di tabel user dengan 'code_cabang' di tabel ref_cabang
        return $this->belongsTo(RefCabang::class, 'group', 'code_cabang');
    }
}