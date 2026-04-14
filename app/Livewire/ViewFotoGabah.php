<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use App\Models\RefUpload;
use Illuminate\Support\Facades\Storage;

class ViewFotoGabah extends Component
{
    public $gabah;
    public $fotos;

    public function mount($id)
    {
        $this->gabah = MasHpkkGabah::findOrFail($id);
        $this->fotos = RefUpload::where('id_hpkk_gabah', $this->gabah->id_po)->get();
    }

    public function deleteFoto($idUpload)
    {
        $foto = RefUpload::find($idUpload);
        if ($foto) {
            Storage::disk('public')->delete($foto->file);
            $foto->delete();
        }

        // Refresh daftar foto
        $this->fotos = RefUpload::where('id_hpkk_gabah', $this->gabah->id_po)->get();
        session()->flash('message', 'Foto berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.view-foto-gabah');
    }
}
