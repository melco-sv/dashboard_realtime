<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkBeras;
use App\Models\RefUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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

    public function approve()
    {
        abort_unless(Auth::check() && Auth::user()->isVerification(), 403);

        $this->beras->update(['status' => 'Approve']);

        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'no_lhpk' => $this->beras->nomor_hpkk_beras,
                'no_mo'   => $this->beras->id_mo,
                'cabang'  => optional($this->beras->cabang)->name_cabang,
            ])
            ->log('Approve HGL');

        session()->flash('message', 'Data berhasil di-approve.');

        return redirect()->route('verifikasi.beras');
    }

    public function reject($catatan)
    {
        abort_unless(Auth::check() && Auth::user()->isVerification(), 403);

        $catatan = trim((string) $catatan);
        if ($catatan === '') {
            session()->flash('error', 'Catatan penolakan wajib diisi.');
            return;
        }

        $this->beras->update([
            'status'  => 'Reject',
            'catatan' => $catatan,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'no_lhpk' => $this->beras->nomor_hpkk_beras,
                'no_mo'   => $this->beras->id_mo,
                'cabang'  => optional($this->beras->cabang)->name_cabang,
                'catatan' => $catatan,
            ])
            ->log('Reject HGL');

        session()->flash('message', 'Data berhasil ditolak.');

        return redirect()->route('verifikasi.beras');
    }

    public function render()
    {
        return view('livewire.view-foto-beras');
    }
}
