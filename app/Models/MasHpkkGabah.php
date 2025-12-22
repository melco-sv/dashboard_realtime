<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasHpkkGabah extends Model
{
    protected $table = 'mas_hpkk_gabah'; // Nama tabel di database
    protected $primaryKey = 'id_hpkk_gabah'; // Primary key-nya bukan 'id'
    public $timestamps = false; // Tabel ini tidak punya created_at/updated_at
    protected $guarded = [];
}