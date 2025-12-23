<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasHpkkBeras extends Model
{
    use HasFactory;

    protected $table = 'mas_hpkk_beras';
    protected $primaryKey = 'id_hpkk_beras';
    
    // Matikan timestamp karena tidak ada kolom created_at/updated_at
    public $timestamps = false;

    protected $guarded = [];
}