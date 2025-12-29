<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // === KONFIGURASI TABEL ===
    protected $table = 'mas_user'; 
    
    // Primary Key (Sesuai screenshot database Anda: userid)
    protected $primaryKey = 'userid'; 
    
    // Auto Increment (Biasanya kolom ID berupa angka auto-increment)
    public $incrementing = true;      
    protected $keyType = 'int';

    // === MASS ASSIGNMENT ===
    // Saya menggunakan $guarded = [] agar SEMUA kolom di database 
    // (termasuk photo, nama_client, dll) otomatis bisa diisi tanpa perlu ditulis satu per satu.
    protected $guarded = []; 

    // === SECURITY ===
    // Sembunyikan data sensitif saat model dikonversi ke Array/JSON
    protected $hidden = [
        'password', 
        'password_md5',
        'remember_token',
    ];

    // === AUTHENTICATION HELPER ===
    // Mengembalikan password utama untuk verifikasi Auth
    public function getAuthPassword()
    {
        return $this->password;
    }

    // === ROLE HELPERS (Case Insensitive) ===
    // Memudahkan pengecekan di Blade: @if($user->isSuperAdmin())
    public function isSuperAdmin() {
        return strtolower($this->level) === 'super admin';
    }

    public function isInspektor() {
        return strtolower($this->level) === 'inspektor';
    }

    public function isVerification() {
        return strtolower($this->level) === 'verification';
    }

    // === RELATIONSHIPS ===
    // Relasi ke Tabel RefCabang untuk mengambil Nama Cabang berdasarkan Group
    public function cabang()
    {
        // belongsTo(ModelTujuan, Foreign Key di tabel user, Owner Key di tabel cabang)
        return $this->belongsTo(RefCabang::class, 'group', 'code_cabang');
    }
}