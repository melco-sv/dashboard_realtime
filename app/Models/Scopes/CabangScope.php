<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;


class CabangScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Cek apakah user sedang login
        if (Auth::check()) {
            $user = Auth::user();

            // Jika User punya 'group' (artinya dia user cabang/inspektor)
            // Dan Groupnya tidak kosong
            if (!empty($user->group) && strtolower($user->level) !== 'verification') {
                $builder->where('group', $user->group);
            }
            
            // Jika User TIDAK punya group (misal Super Admin Pusat), 
            // maka dia bisa melihat semua data (tidak ada filter 'where')
        }
    }
}