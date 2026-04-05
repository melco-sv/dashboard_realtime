<?php

namespace App\Models;

// Pastikan import Hash agar bisa dipakai jika perlu
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// PENTING: Import model RefCabang agar tidak error saat dipanggil relasinya
use App\Models\RefCabang;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // === KONFIGURASI TABEL ===
    protected $table = 'mas_user';

    // === PERBAIKAN 1: PRIMARY KEY ===
    // Sesuai dengan query CREATE TABLE sebelumnya, namanya adalah 'id_user'
    protected $primaryKey = 'id_user';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'password_md5',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }

    // === ROLE HELPERS ===
    public function isSuperAdmin()
    {
        return strtolower($this->level) === 'super admin';
    }

    public function isInspektor()
    {
        return strtolower($this->level) === 'inspektor';
    }

    public function isVerification()
    {
        return strtolower($this->level) === 'verification';
    }

    // === RELATIONSHIPS ===
    public function cabang()
    {
        return $this->belongsTo(RefCabang::class, 'group', 'code_cabang');
    }
}
