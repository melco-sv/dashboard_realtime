<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\MasHpkkBeras;
use App\Models\RefUpload;

class UploadFotoBeras extends Component
{
    use WithFileUploads;

    public $id_hpkk_beras;
    public $nomor_hpkk_beras;

    // Form Inputs
    public $nama;
    public $photo;
    public $group;

    public function mount($id)
    {
        // Ambil data Beras Induk
        $data = MasHpkkBeras::findOrFail($id);

        $this->id_hpkk_beras = $data->id_hpkk_beras;
        $this->nomor_hpkk_beras = $data->nomor_hpkk_beras;
        $this->group = $data->group;
    }

    public function save()
    {
        $this->validate([
            'nama' => 'required|string|max:255',
            'photo' => 'required|image|max:10240', // Max 10MB
            'group' => 'required',
        ]);

        try {
            // 1. Simpan File
            $path = $this->photo->store('photos', 'public');

            // 2. Simpan ke Database RefUpload
            // NOTE: Kita simpan ID Beras ke kolom 'id_hpkk_gabah' karena strukturnya begitu,
            // tapi dibedakan dengan group/konteks
            RefUpload::create([
                'id_hpkk_gabah' => $this->id_hpkk_beras, // Simpan ID Beras disini
                'nama' => $this->nama,
                'file' => $path,
                'group' => $this->group,
            ]);

            session()->flash('message', 'Foto Beras berhasil diupload!');
            return redirect()->route('list.beras');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal upload: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('list.beras');
    }

    public function render()
    {
        return view('livewire.upload-foto-beras');
    }
}
