<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\RefCabang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ManageUser extends Component
{
    use WithPagination;

    // === PROPERTI FORM ===
    public $username, $nama, $email, $phone, $position, $group, $level, $status;
    public $password, $password_confirmation;

    // === STATE MANAGEMENT ===
    public $isEditMode = false;
    public $showModal = false;
    public $usernameBeingEdited = null;
    public $search = '';

    // === LIST DATA ===
    public $listCabang = [];
    public $listLevel = ['Super Admin',  'Inspektor', 'Verification'];

    public function mount()
    {
        // 1. SECURITY CHECK: Hanya Super Admin yang boleh akses
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return redirect()->route('dashboard.gabah');
        }

        // 2. Load Data Cabang untuk Dropdown
        $this->listCabang = RefCabang::orderBy('name_cabang')->get();
    }

    // Reset pagination saat search berubah
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::query();

        // Fitur Search
        if (!empty($this->search)) {
            $query->where('nama', 'like', '%' . $this->search . '%')
                ->orWhere('username', 'like', '%' . $this->search . '%');
        }

        // Tampilkan user terbaru dulu
        $users = $query->orderBy('id_user', 'desc')->paginate(10);

        return view('livewire.manage-user', [
            'users' => $users
        ]);
    }

    // === BUKA MODAL CREATE ===
    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    // === BUKA MODAL EDIT ===
    public function edit($username)
    {
        $this->resetForm();
        $user = User::where('username', $username)->firstOrFail();

        $this->usernameBeingEdited = $user->username;
        $this->username = $user->username;
        $this->nama = $user->nama;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->position = $user->position;
        $this->group = $user->group;
        $this->level = $user->level;
        $this->status = $user->status;

        $this->isEditMode = true;
        $this->showModal = true;
    }

    // === SIMPAN DATA (CREATE / UPDATE) ===
    public function store()
    {
        // 1. Aturan Validasi
        $rules = [
            'nama' => 'required',
            'email' => 'nullable|email',
            'level' => 'required',
            'group' => 'required', // Cabang
            'status' => 'required',
        ];

        if ($this->isEditMode) {
            // Validasi Edit (Username tidak boleh diubah, Password opsional)
            $rules['username'] = 'required';
            if (!empty($this->password)) {
                $rules['password'] = 'min:5|confirmed';
            }
        } else {
            // Validasi Create (Username Unik, Password Wajib)
            $rules['username'] = 'required|unique:mas_user,username';
            $rules['password'] = 'required|min:5|confirmed';
        }

        $this->validate($rules);

        // 2. Persiapan Data
        $data = [
            'nama' => $this->nama,
            'email' => $this->email,
            'phone' => $this->phone,
            'position' => $this->position,
            'group' => $this->group,
            'level' => $this->level,
            'status' => $this->status,
        ];

        // 3. Handle Password (MD5 sesuai sistem lama)
        if (!empty($this->password)) {
            $data['password'] = md5($this->password);
            $data['password_md5'] = md5($this->password); // Simpan di kedua kolom agar aman
        }

        // 4. Eksekusi Database
        if ($this->isEditMode) {
            User::where('username', $this->usernameBeingEdited)->update($data);
            activity()
                ->causedBy(Auth::user())
                ->withProperties(['username' => $this->usernameBeingEdited, 'level' => $this->level])
                ->log('Edit User');
            session()->flash('message', 'User berhasil diperbarui!');
        } else {
            $data['username'] = $this->username;
            User::create($data);
            activity()
                ->causedBy(Auth::user())
                ->withProperties(['username' => $this->username, 'level' => $this->level, 'cabang' => $this->group])
                ->log('Buat User Baru');
            session()->flash('message', 'User baru berhasil dibuat!');
        }

        // 5. Tutup Modal
        $this->showModal = false;
        $this->resetForm();
    }

    // === HAPUS USER ===
    public function delete($username)
    {
        // Cegah hapus diri sendiri
        if ($username == Auth::user()->username) {
            session()->flash('error', 'Anda tidak bisa menghapus akun sendiri!');
            return;
        }

        $user = User::where('username', $username)->first();
        User::where('username', $username)->delete();

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['username' => $username, 'nama' => $user?->nama, 'level' => $user?->level])
            ->log('Hapus User');

        session()->flash('message', 'User berhasil dihapus.');
    }

    // === RESET FORM ===
    public function resetForm()
    {
        $this->reset([
            'username',
            'nama',
            'email',
            'phone',
            'position',
            'group',
            'level',
            'status',
            'password',
            'password_confirmation',
            'usernameBeingEdited'
        ]);
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->showModal = false;
    }
}
