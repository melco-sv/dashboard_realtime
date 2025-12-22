<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasHpkkBeras extends Model
{
    protected $table = 'mas_hpkk_beras';
    protected $primaryKey = 'id_mo';
    public $incrementing = false; // Karena primary key-nya String (MO/...)
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];
}