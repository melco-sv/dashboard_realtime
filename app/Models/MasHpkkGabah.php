<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CabangScope;

class MasHpkkGabah extends Model
{
    use HasFactory;

    protected $table = 'mas_hpkk_gabah';
    protected $primaryKey = 'id_po'; // Pastikan ini sesuai dengan PK di database

    // KUNCI PENTING: guarded kosong [] mengizinkan 'Mass Assignment'
    // Ini wajib agar MasHpkkGabah::create($data) di Livewire bisa berjalan
    protected $guarded = [];

    // Set ke 'true' jika tabel Anda memiliki kolom created_at dan updated_at
    // Set ke 'false' jika tidak punya
    public $timestamps = false;

    // OPTIONAL TAPI BAGUS: Casting Data
    // Mengubah data string dari database menjadi tipe data asli (Date/Float)
    // Ini memudahkan saat nanti Anda membuat laporan atau perhitungan
    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
        'tanggal_doc' => 'date',
        'jumlah_timbangan' => 'float',
        'ulangan_1' => 'float',
        'ulangan_2' => 'float',
        'ulangan_3' => 'float',
        'kadar_air_rata_rata' => 'float',
        'kadar_hampa' => 'float',
        'butir_hijau' => 'float',
    ];

    // === RELATIONSHIP ===
    public function cabang()
    {
        // Menghubungkan kolom 'group' (di tabel ini) ke 'code_cabang' (di tabel RefCabang)
        return $this->belongsTo(RefCabang::class, 'group', 'code_cabang');
    }

    // === GLOBAL SCOPE ===
    // Filter data otomatis berdasarkan user login (jika logic ada di CabangScope)
    protected static function booted()
    {
        static::addGlobalScope(new CabangScope);
    }
}
