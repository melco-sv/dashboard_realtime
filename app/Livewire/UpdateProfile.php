<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UpdateProfile extends Component
{
    // Property untuk Form
    public $username;
    public $nama;
    public $email;
    public $phone;
    public $position;
    
    // Read Only Property
    public $level;
    public $group;
    public $status;

    // Property Password Baru
    public $new_password;
    public $new_password_confirmation; // Retype Password

    public function mount()
    {
        $user = Auth::user();

        // Isi form dengan data database saat ini
        $this->username = $user->username;
        $this->nama     = $user->nama;
        $this->email    = $user->email;
        $this->phone    = $user->phone;
        $this->position = $user->position;
        
        // Data Read Only
        $this->level    = $user->level;
        $this->group    = $user->group;
        $this->status   = $user->status;
    }

    public function update()
    {
        // 1. Validasi Input
        $this->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            // Validasi Password: Min 6 karakter, dan harus sama dengan retype
            'new_password' => 'nullable|min:6|same:new_password_confirmation',
        ]);

        // 2. Ambil User yang sedang login
        $user = User::find(Auth::id());
        
        // 3. Siapkan data yang akan diupdate
        $dataToUpdate = [
            'nama' => $this->nama,
            'email' => $this->email,
            'phone' => $this->phone,
            'position' => $this->position,
        ];

        // 4. Cek apakah user mengisi password baru
        if (!empty($this->new_password)) {
            // Enkripsi MD5 sesuai standar database Anda
            $dataToUpdate['password'] = md5($this->new_password);
            $dataToUpdate['password_md5'] = md5($this->new_password); 
        }

        // 5. Simpan ke Database
        $user->update($dataToUpdate);

        // 6. Reset field password & Kirim pesan sukses
        $this->new_password = '';
        $this->new_password_confirmation = '';
        
        session()->flash('message', 'Data user berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.update-profile');
    }
}