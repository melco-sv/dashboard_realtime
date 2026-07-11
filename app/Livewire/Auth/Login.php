<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter; // Import RateLimiter
use Illuminate\Support\Str; // Import Str
use App\Models\User;

class Login extends Component
{
    public $username;
    public $password;
    
    // public $error = ''; // Kita ganti pakai standard error bag Laravel

    public function login()
    {
        // 1. Validasi Input
        $this->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // 2. CEK RATE LIMITER (Anti Brute Force)
        // Kunci unik berdasarkan username dan IP address user
        $throttleKey = Str::lower($this->username) . '|' . request()->ip();

        // Jika salah 5x, kunci selama 60 detik
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('username', "Terlalu banyak percobaan login. Silakan tunggu $seconds detik.");
            return;
        }

        // 3. Cari User
        $user = User::where('username', $this->username)->first();

        // 4. Cek Password & User Exists
        // Dual-check: bcrypt (format baru) dicek dulu, lalu fallback MD5 (format lama).
        // Jika cocok via MD5, hash langsung ditulis ulang sebagai bcrypt — migrasi
        // bertahap tanpa perlu reset password user lama.
        if ($user && $this->passwordMatches($user)) {
            
            // Cek Status Active
            if ($user->status !== 'Active') {
                $this->addError('username', 'Akun Anda dinonaktifkan / Tidak Aktif.');
                return;
            }

            // === LOGIN SUKSES ===
            
            // A. Login User
            Auth::login($user);

            // B. Regenerasi Session ID (PENTING untuk keamanan Session Fixation)
            session()->regenerate();

            // C. Hapus catatan gagal login di Rate Limiter
            RateLimiter::clear($throttleKey);

            // D. Redirect
            // Gunakan intended agar jika user tadi mau buka halaman X tapi disuruh login dulu,
            // dia akan dikembalikan ke halaman X tersebut, bukan paksa ke dashboard.
            return redirect()->intended(route('dashboard.gabah')); 
            // Pastikan Anda punya route bernama 'dashboard.gabah' atau ganti url '/dashboard'
        }

        // === LOGIN GAGAL ===
        
        // Catat kegagalan login (tambah hitungan ke Rate Limiter)
        RateLimiter::hit($throttleKey);

        // Kirim error ke input username
        $this->addError('username', 'Username atau Password salah.');
    }

    /**
     * Verifikasi password dengan dukungan dua format hash:
     * - bcrypt ($2y$...) — format baru.
     * - MD5 (32 hex) — format lama; bila cocok, langsung di-upgrade ke bcrypt
     *   dan kolom legacy password_md5 dikosongkan (rehash progresif).
     */
    private function passwordMatches(User $user): bool
    {
        $stored = (string) $user->password;

        // Format baru: bcrypt
        if (str_starts_with($stored, '$2')) {
            return Hash::check($this->password, $stored);
        }

        // Format lama: MD5 → cocok berarti login sah, upgrade hash saat itu juga
        if (hash_equals($stored, md5($this->password))) {
            User::where('id_user', $user->id_user)->update([
                'password'     => Hash::make($this->password),
                'password_md5' => '',
            ]);
            return true;
        }

        return false;
    }

    public function render()
    {
        // Pastikan layout guest sudah ada
        return view('livewire.auth.login')->layout('components.layouts.guest');
    }
}