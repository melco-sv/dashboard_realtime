<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\CabangScope;

class MasHpkkGabah extends Model
{
    use HasFactory;
    protected $table = 'mas_hpkk_gabah';
    protected $primaryKey = 'id_hpkk_gabah';
    protected $guarded = [];
    public $timestamps = false; // Sesuaikan jika ada created_at

    // === TAMBAHKAN INI ===
    public function cabang()
    {
        // Menghubungkan kolom 'group' (transaksi) dengan 'code_cabang' (ref)
        return $this->belongsTo(RefCabang::class, 'group', 'code_cabang');
    }
    protected static function booted()
    {
        static::addGlobalScope(new CabangScope);
    }
}