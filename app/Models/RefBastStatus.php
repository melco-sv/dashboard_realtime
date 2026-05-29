<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefBastStatus extends Model
{
    protected $table = 'ref_bast_status';

    protected $fillable = [
        'code_cabang',
        'jenis',
        'tgl_mulai',
        'tgl_akhir',
        'nomor_surat',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_akhir' => 'date',
    ];

    public function cabang()
    {
        return $this->belongsTo(RefCabang::class, 'code_cabang', 'code_cabang');
    }
}
