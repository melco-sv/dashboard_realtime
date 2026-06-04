<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CabangScope; // Pastikan file Scope ini ada
use App\Models\RefCabang;

class MasHpkkBeras extends Model
{
    use HasFactory;

    // === KONFIGURASI TABEL ===
    protected $table = 'mas_hpkk_beras';
    protected $primaryKey = 'id_hpkk_beras'; 
    public $timestamps = false; // Mematikan created_at & updated_at

    // === MASS ASSIGNMENT ===
    // Menggunakan guarded = [] berarti "Semua kolom BOLEH diisi".
    // Ini lebih praktis daripada menulis $fillable satu per satu.
    protected $guarded = []; 

    // === CASTING TIPE DATA ===
    // Mengubah data dari database menjadi tipe data asli PHP secara otomatis.
    // Sangat penting untuk perhitungan matematika di Livewire.
    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'tanggal_doc' => 'date',
        
        // Pastikan semua kolom angka di-cast ke float/double
        'ulangan_1' => 'float',
        'ulangan_2' => 'float',
        'ulangan_3' => 'float',
        'rata_rata' => 'float',
        
        'derajat_sosoh' => 'float',
        'butir_patah' => 'float',
        'menir' => 'float',
        
        'kuantum_gabah_sesuai_mo' => 'float',
        'kuantum_beras' => 'float',
        'rendemen_pengolahan' => 'float',
        
        'hasil_samping_menir' => 'float',
        'hasil_samping_butir_patah' => 'float',
        'hasil_samping_dedak_katul' => 'float',
        'hasil_samping_butir_kuning_rusak' => 'float',
    ];

    // === RELATIONSHIP (HUBUNGAN ANTAR TABEL) ===
    
    /**
     * Relasi ke tabel RefCabang.
     * Digunakan untuk mengambil Nama Cabang berdasarkan kolom 'group'.
     */
    public function cabang()
    {
        return $this->belongsTo(RefCabang::class, 'code_cabang', 'code_cabang');
    }

    public function fotos()
    {
        return $this->hasMany(\App\Models\RefUpload::class, 'id_hpkk_beras', 'id_hpkk_beras');
    }

    // === GLOBAL SCOPE ===
    // Fungsi ini akan otomatis dijalankan setiap kali Model ini dipanggil.
    // Tujuannya: Agar User Cabang A hanya bisa melihat data Cabang A (Security).
    protected static function booted()
    {
        static::addGlobalScope(new CabangScope);
    }
}