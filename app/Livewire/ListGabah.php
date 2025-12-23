<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasHpkkGabah;

class ListGabah extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $data = MasHpkkGabah::query()
            ->when($this->search, function($q) {
                $q->where('nomor_hpkk_gabah', 'like', '%'.$this->search.'%')
                  ->orWhere('mitra', 'like', '%'.$this->search.'%')
                  ->orWhere('kode_sample', 'like', '%'.$this->search.'%');
            })
            ->orderBy('id_hpkk_gabah', 'desc')
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
            $data->delete();
            session()->flash('message', 'Data berhasil dihapus.');
        }
    }

    // === ACTION APPROVE ===
    public function approve($id)
    {
        $data = MasHpkkGabah::find($id);
        if ($data) {
            // Ubah kolom status_data menjadi 'Approve'
            $data->update(['status_data' => 'Approve']);
            session()->flash('message', 'Status Data berhasil diubah menjadi Approve.');
        }
    }
}