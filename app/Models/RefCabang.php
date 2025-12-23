<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefCabang extends Model
{
    use HasFactory;

    protected $table = 'ref_cabang'; 
    public $timestamps = false; 

    protected $fillable = [
        'id_cabang',
        'code_cabang',     // Kunci pencarian (sama dengan kolom 'group')
        'name_cabang',     // Untuk kolom Kantor Cabang
        'parent_company',  // Untuk kolom Kantor Wilayah
        // ... kolom lain jika perlu
    ];
}