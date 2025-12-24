<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userLevel = strtolower(Auth::user()->level);
        
        // Ubah parameter roles menjadi array lowercase
        $allowedRoles = array_map('strtolower', $roles);

        // Khusus Inspektor biasanya boleh akses semua, jadi kita izinkan by default
        // Kecuali jika route spesifik melarangnya.
        // Tapi sesuai request: Inspektor = All, Admin/Verif = Dashboard Only.
        
        if (in_array($userLevel, $allowedRoles)) {
            return $next($request);
        }

        // Jika tidak punya akses, lempar ke dashboard atau halaman error 403
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    }
}