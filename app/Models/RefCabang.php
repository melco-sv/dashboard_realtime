<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefCabang extends Model
{
    use HasFactory;

    // 1. Nama Tabel
    protected $table = 'ref_cabang'; 

    // 2. Primary Key (PENTING: Ubah dari default 'id' ke 'code_cabang')
    // Ini akan memperbaiki error "Unknown column 'ref_cabang.id'"
    protected $primaryKey = 'code_cabang';

    // 3. Konfigurasi Primary Key (String & Non-Increment)
    // Agar kode seperti '001' tidak berubah menjadi angka 1
    public $incrementing = false;
    protected $keyType = 'string';

    // 4. Matikan Timestamps (sesuai request Anda)
    public $timestamps = false; 

    // 5. Daftar Kolom yang bisa diisi
    protected $fillable = [
        'id_cabang',       // ID Asli (jika ada)
        'code_cabang',     // Kunci Relasi ke User
        'name_cabang',     // Nama Cabang
        'parent_company',  // Kantor Wilayah
    ];
}