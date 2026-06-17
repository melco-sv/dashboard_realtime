<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RefBastStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StatusBayarBast extends Component
{
    use WithPagination;

    // Filter
    public $filter_cabang = '';
    public $filter_jenis = '';
    public $filter_status = '';
    public $filter_bulan = ''; // format YYYY-MM; kosong = tampilkan semua data

    protected $queryString = ['filter_cabang', 'filter_jenis', 'filter_status', 'filter_bulan'];

    public function mount()
    {
        $user = Auth::user();
        if (!Auth::check() || !($user->isVerification() || $user->isSuperAdmin())) {
            return redirect()->route('dashboard');
        }
    }

    public function updatingFilterCabang() { $this->resetPage(); }
    public function updatingFilterJenis()  { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterBulan()  { $this->resetPage(); }

    public function toggleStatus($id)
    {
        $row = RefBastStatus::findOrFail($id);
        $row->status = $row->status === 'DIBAYAR' ? 'BELUM DIBAYAR' : 'DIBAYAR';
        $row->save();
    }

    public function hapus($id)
    {
        RefBastStatus::destroy($id);
        session()->flash('message', 'Record berhasil dihapus.');
    }

    public function render()
    {
        $cabangs = DB::table('ref_cabang')->orderBy('name_cabang')->get(['code_cabang', 'name_cabang']);

        $data = RefBastStatus::with('cabang')
            ->when($this->filter_cabang, fn($q) => $q->where('code_cabang', $this->filter_cabang))
            ->when($this->filter_jenis,  fn($q) => $q->where('jenis', $this->filter_jenis))
            ->when($this->filter_status, fn($q) => $q->where('status', $this->filter_status))
            ->when($this->filter_bulan, function ($q) {
                // Filter opsional berdasarkan bulan periode (tgl_mulai). Kosong = semua data tampil.
                [$year, $month] = explode('-', $this->filter_bulan);
                $q->whereYear('tgl_mulai', $year)->whereMonth('tgl_mulai', $month);
            })
            ->orderByDesc('tgl_mulai')
            ->paginate(15);

        return view('livewire.status-bayar-bast', compact('data', 'cabangs'));
    }
}
