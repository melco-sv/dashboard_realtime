<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasHpkkBeras;
use Illuminate\Support\Facades\Auth;

class ListBeras extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $data = MasHpkkBeras::query()
            ->withCount('fotos')
            ->when($this->search, function ($q) {
                $q->where('nomor_hpkk_beras', 'like', '%' . $this->search . '%')
                    ->orWhere('id_mo', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_sample', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id_hpkk_beras', 'desc')
            ->paginate(10);

        return view('livewire.list-beras', [
            'berasList' => $data
        ]);
    }

    public function delete($id)
    {
        try {
            $data = MasHpkkBeras::findOrFail($id);

            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'no_lhpk' => $data->nomor_hpkk_beras,
                    'no_mo'   => $data->id_mo,
                ])
                ->log('Delete HGL');

            $data->delete();
            session()->flash('message', 'Data Beras berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data.');
        }
    }

    public function approve($id)
    {
        try {
            $data = MasHpkkBeras::findOrFail($id);
            $data->update(['status' => 'Approve']);
            session()->flash('message', 'Status Data berhasil diubah menjadi Approve.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyetujui data.');
        }
    }
}
