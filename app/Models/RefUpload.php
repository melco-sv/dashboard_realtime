<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefUpload extends Model
{
    use HasFactory;

    protected $table = 'ref_upload'; // Nama tabel sesuai database
    protected $primaryKey = 'id_upload'; // Primary Key
    
    // Matikan timestamp jika tabel tidak punya kolom created_at/updated_at
    public $timestamps = false; 

    protected $fillable = [
        'id_hpkk_gabah',
        'nama',
        'file',
        'group'
    ];
}