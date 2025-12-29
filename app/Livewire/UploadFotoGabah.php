<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads; 
use App\Models\MasHpkkGabah;
use App\Models\RefUpload; // Gunakan Model yang baru dibuat

class UploadFotoGabah extends Component
{
    use WithFileUploads;

    public $id_hpkk_gabah;
    
    // Form Inputs
    public $nama;   // Sesuai kolom 'nama' di tabel
    public $photo;  // Untuk temporary file
    public $group;  // Sesuai kolom 'group' di tabel

    public function mount($id)
    {
        // Ambil data Gabah induk untuk pre-fill data
        $data = MasHpkkGabah::findOrFail($id);
        
        $this->id_hpkk_gabah = $data->id_hpkk_gabah;
        $this->group = $data->group; 
    }

    public function save()
    {
    $this->validate([
        'nama' => 'required|string|max:255',
        // Ubah 2048 (2MB) menjadi 5120 (5MB) atau 10240 (10MB)
        'photo' => 'required|image|max:10240', 
        'group' =>  'required',
    ]);

        try {
            // 1. Simpan File ke Storage (public/photos)
            $path = $this->photo->store('photos', 'public');

            // 2. Simpan ke Database (Tabel ref_upload)
            RefUpload::create([
                'id_hpkk_gabah' => $this->id_hpkk_gabah,
                'nama' => $this->nama,
                'file' => $path,  // Simpan path file
                'group' => $this->group,
            ]);

            session()->flash('message', 'Foto berhasil diupload!');
            
            // Redirect kembali ke List Gabah
            return redirect()->route('list.gabah');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal upload: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('list.gabah');
    }

    public function render()
    {
        return view('livewire.upload-foto-gabah');
    }
}