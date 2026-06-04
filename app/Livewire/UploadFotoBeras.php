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

    // Konteks record
    public $nomor_hpkk;
    public $tempat;
    public $tanggal;
    public $lokasi_record;

    // Form Inputs
    public $nama;
    public $photo;
    public $code_cabang;

    public function mount($id)
    {
        $data = MasHpkkBeras::findOrFail($id);

        $this->id_hpkk_beras = $data->id_hpkk_beras;
        $this->code_cabang   = $data->code_cabang;

        // Load konteks
        $this->nomor_hpkk    = $data->nomor_hpkk_beras;
        $this->tempat        = $data->tempat_pemeriksaan;
        $this->tanggal       = $data->tanggal_pemeriksaan
                                    ? $data->tanggal_pemeriksaan->format('d M Y')
                                    : '-';
        $this->lokasi_record = $data->lokasi;
    }

    public function save()
    {
        $this->validate([
            'nama'  => 'required|string|max:255',
            'photo' => 'required|image|max:10240',
            'code_cabang' => 'required',
        ]);

        try {
            $path = $this->photo->store('photos', 'public');

            RefUpload::create([
                'id_hpkk_beras' => $this->id_hpkk_beras,
                'nama'          => $this->nama,
                'file'          => $path,
                'code_cabang'   => $this->code_cabang,
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
