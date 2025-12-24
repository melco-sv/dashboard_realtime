<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasHpkkBeras extends Model
{
    use HasFactory;

    // Nama Tabel
    protected $table = 'mas_hpkk_beras';
    
    // Primary Key (Karena bukan 'id' default, harus didefinisikan)
    protected $primaryKey = 'id_hpkk_beras'; 

    // PENTING: Matikan timestamp karena tabel Anda tidak punya kolom created_at & updated_at
    // Jika ini true (default), Laravel akan error saat insert data.
    public $timestamps = false;

    // Daftar kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'id_mo',
        'nomor_hpkk_beras',
        'nomor_order',
        'tempat_pemeriksaan',
        'tanggal_pemeriksaan',
        'kode_sample',
        'dasar_pemeriksaan',
        'kondisi_kemasan',
        'hama',
        'dedak_katul_sekam',
        'bau',
        'bahan_kimia', // Sesuai struktur tabel
        
        // Data Lab Angka
        'ulangan_1',
        'ulangan_2',
        'ulangan_3',
        'rata_rata',
        
        // Fisik Beras
        'derajat_sosoh',
        'butir_patah',
        'menir',
        
        // Kuantum
        'kuantum_gabah_sesuai_mo',
        'kuantum_beras',
        'rendemen_pengolahan',
        
        // Hasil Samping
        'hasil_samping_menir',
        'hasil_samping_butir_patah',
        'hasil_samping_dedak_katul',
        'hasil_samping_butir_kuning_rusak',
        
        // Footer & Identitas
        'catatan',
        'tanggal_doc',
        'lokasi',
        'mengetahui',
        'petugas',
        'group',
        'status',
        
        // Kolom Tambahan (jika nanti digunakan)
        'nomor_lhpk_beras',
        'periode'
    ];
}