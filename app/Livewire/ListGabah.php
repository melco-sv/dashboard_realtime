<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasHpkkGabah;
use Illuminate\Support\Facades\Auth;

class ListGabah extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $data = MasHpkkGabah::query()
            ->withCount('fotos')
            ->when($this->search, function ($q) {
                $q->where('nomor_hpkk_gabah', 'like', '%' . $this->search . '%')
                    ->orWhere('mitra', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_sample', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id_po', 'desc')
            ->paginate(10);

        return view('livewire.list-gabah', [
            'gabahList' => $data
        ]);
    }

    // === ACTION DELETE ===
    public function delete($id)
    {
        $data = MasHpkkGabah::find($id);
        if ($data) {
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'no_hpk' => $data->nomor_hpkk_gabah,
                    'mitra'  => $data->mitra,
                ])
                ->log('Delete GKP');

            $data->delete();
            session()->flash('message', 'Data berhasil dihapus.');
        }
    }

    // === ACTION APPROVE ===
    public function approve($id)
    {
        $data = MasHpkkGabah::find($id);
        if ($data) {
            $data->update(['status_data' => 'Approve']);
            session()->flash('message', 'Status Data berhasil diubah menjadi Approve.');
        }
    }
}
