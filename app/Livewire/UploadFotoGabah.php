<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\MasHpkkGabah;
use App\Models\RefUpload;

class UploadFotoGabah extends Component
{
    use WithFileUploads;

    public $id_hpkk_gabah;

    // Konteks record
    public $nomor_hpkk;
    public $mitra_nama;
    public $tanggal;
    public $lokasi_record;

    // Form Inputs
    public $nama;
    public $photo;
    public $group;

    public function mount($id)
    {
        $data = MasHpkkGabah::findOrFail($id);

        $this->id_hpkk_gabah = $data->id_po; // FIX: PK adalah id_po, bukan id_hpkk_gabah
        $this->group = $data->group;

        // Load konteks untuk ditampilkan di form
        $this->nomor_hpkk    = $data->nomor_hpkk_gabah;
        $this->mitra_nama    = $data->mitra;
        $this->tanggal       = $data->tanggal_pelaksanaan
                                    ? $data->tanggal_pelaksanaan->format('d M Y')
                                    : '-';
        $this->lokasi_record = $data->lokasi;
    }

    public function save()
    {
        $this->validate([
            'nama'  => 'required|string|max:255',
            'photo' => 'required|image|max:10240',
            'group' => 'required',
        ]);

        try {
            $path = $this->photo->store('photos', 'public');

            RefUpload::create([
                'id_hpkk_gabah' => $this->id_hpkk_gabah,
                'nama'          => $this->nama,
                'file'          => $path,
                'group'         => $this->group,
            ]);

            session()->flash('message', 'Foto berhasil diupload!');
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
