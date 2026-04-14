<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasHpkkBeras;

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
            MasHpkkBeras::findOrFail($id)->delete();
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
