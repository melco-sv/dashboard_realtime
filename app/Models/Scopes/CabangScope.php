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

            if (!empty($user->code_cabang) && strtolower($user->level) !== 'verification') {
                $builder->where('code_cabang', $user->code_cabang);
            }
        }
    }
}