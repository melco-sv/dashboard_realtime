<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use App\Models\RefUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ViewFotoGabah extends Component
{
    public $gabah;
    public $fotos;

    public function mount($id)
    {
        $this->gabah = MasHpkkGabah::findOrFail($id);
        $this->fotos = RefUpload::where('id_hpkk_gabah', $this->gabah->id_hpkk_gabah)->get();
    }

    public function deleteFoto($idUpload)
    {
        $foto = RefUpload::find($idUpload);
        if ($foto) {
            Storage::disk('public')->delete($foto->file);
            $foto->delete();
        }

        // Refresh daftar foto
        $this->fotos = RefUpload::where('id_hpkk_gabah', $this->gabah->id_hpkk_gabah)->get();
        session()->flash('message', 'Foto berhasil dihapus.');
    }

    public function approve()
    {
        abort_unless(Auth::check() && Auth::user()->isVerification(), 403);

        $this->gabah->update(['status_data' => 'Approve']);

        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'no_hpk'  => $this->gabah->nomor_hpkk_gabah,
                'mitra'   => $this->gabah->mitra,
                'cabang'  => optional($this->gabah->cabang)->name_cabang,
            ])
            ->log('Approve GKP');

        session()->flash('message', 'Data berhasil di-approve.');

        return redirect()->route('verifikasi.gabah');
    }

    public function reject($catatan)
    {
        abort_unless(Auth::check() && Auth::user()->isVerification(), 403);

        $catatan = trim((string) $catatan);
        if ($catatan === '') {
            session()->flash('error', 'Catatan penolakan wajib diisi.');
            return;
        }

        $this->gabah->update([
            'status_data' => 'Reject',
            'catatan'     => $catatan,
        ]);

        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'no_hpk'  => $this->gabah->nomor_hpkk_gabah,
                'mitra'   => $this->gabah->mitra,
                'cabang'  => optional($this->gabah->cabang)->name_cabang,
                'catatan' => $catatan,
            ])
            ->log('Reject GKP');

        session()->flash('message', 'Data berhasil ditolak.');

        return redirect()->route('verifikasi.gabah');
    }

    public function render()
    {
        return view('livewire.view-foto-gabah');
    }
}
