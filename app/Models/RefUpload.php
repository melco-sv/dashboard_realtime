<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefUpload extends Model
{
    use HasFactory;

    protected $table = 'ref_upload';
    protected $primaryKey = 'id_upload';
    public $timestamps = false;

    protected $fillable = [
        'id_hpkk_gabah',
        'id_hpkk_beras',
        'nama',
        'file',
        'group',
    ];
}
