<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasHpkkGabah extends Model
{
    use HasFactory;

    protected $table = 'mas_hpkk_gabah';
    protected $primaryKey = 'id_hpkk_gabah'; // Sesuaikan primary key dari foto
    
    // MATIKAN TIMESTAMP OTOMATIS
    public $timestamps = false; 

    protected $guarded = [];
}