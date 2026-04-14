<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkBeras;
use App\Models\RefUpload;
use Illuminate\Support\Facades\Storage;

class ViewFotoBeras extends Component
{
    public $beras;
    public $fotos;

    public function mount($id)
    {
        $this->beras = MasHpkkBeras::findOrFail($id);
        $this->fotos = RefUpload::where('id_hpkk_beras', $this->beras->id_hpkk_beras)->get();
    }

    public function deleteFoto($idUpload)
    {
        $foto = RefUpload::find($idUpload);
        if ($foto) {
            Storage::disk('public')->delete($foto->file);
            $foto->delete();
        }

        $this->fotos = RefUpload::where('id_hpkk_beras', $this->beras->id_hpkk_beras)->get();
        session()->flash('message', 'Foto berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.view-foto-beras');
    }
}
