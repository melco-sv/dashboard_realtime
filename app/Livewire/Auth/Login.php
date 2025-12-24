<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Login extends Component
{
    public $username;
    public $password;
    public $error = '';

    public function login()
    {
        $this->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // 1. Cari User berdasarkan Username
        $user = User::where('username', $this->username)->first();

        // 2. Cek apakah User ada & Password MD5 cocok
        // Database Anda menggunakan MD5
        if ($user && $user->password == md5($this->password)) {
            
            // Cek Status Active
            if($user->status !== 'Active') {
                $this->error = 'Akun anda tidak aktif.';
                return;
            }

            // Login Manual ke Laravel
            Auth::login($user);

            // Redirect ke Dashboard
            return redirect()->intended('/dashboard'); // Ganti dengan route dashboard Anda
        } else {
            $this->error = 'Username atau Password salah.';
        }
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest'); 
        // Pastikan Anda punya layout guest (kosong) agar tidak muncul sidebar saat login
    }
}