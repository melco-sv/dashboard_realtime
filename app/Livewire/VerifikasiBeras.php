<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class VerifikasiBeras extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $cabang_filter  = '';
    public string $status_filter  = '';
    public string $tgl_mulai      = '';
    public string $tgl_akhir      = '';

    public function mount(): void
    {
        $this->tgl_mulai = date('Y-m-01');
        $this->tgl_akhir = date('Y-m-d');
    }

    public function updatingSearch(): void        { $this->resetPage(); }
    public function updatingCabangFilter(): void  { $this->resetPage(); }
    public function updatingStatusFilter(): void  { $this->resetPage(); }
    public function updatingTglMulai(): void      { $this->resetPage(); }
    public function updatingTglAkhir(): void      { $this->resetPage(); }

    public function approve(int $id): void
    {
        DB::table('mas_hpkk_beras')
            ->where('id_hpkk_beras', $id)
            ->update(['status' => 'Approve']);

        session()->flash('message', 'Data berhasil di-approve.');
    }

    public function render()
    {
        $query = DB::table('mas_hpkk_beras as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->select(
                'm.id_hpkk_beras', 'm.nomor_hpkk_beras', 'm.id_mo',
                'm.tempat_pemeriksaan', 'm.tanggal_pemeriksaan',
                'm.kuantum_beras', 'm.status',
                'm.rata_rata', 'm.derajat_sosoh', 'm.butir_patah', 'm.menir',
                'm.rendemen_pengolahan',
                'r.name_cabang'
            )
            ->selectSub(
                DB::table('ref_upload')->selectRaw('COUNT(*)')->whereColumn('id_hpkk_beras', 'm.id_hpkk_beras'),
                'fotos_count'
            )
            ->orderBy('m.id_hpkk_beras', 'desc');

        if ($this->tgl_mulai && $this->tgl_akhir) {
            $query->whereBetween('m.tanggal_pemeriksaan', [
                $this->tgl_mulai . ' 00:00:00',
                $this->tgl_akhir . ' 23:59:59',
            ]);
        }

        if ($this->cabang_filter) {
            $query->where('m.group', $this->cabang_filter);
        }

        if ($this->status_filter === 'Approve') {
            $query->where('m.status', 'Approve');
        } elseif ($this->status_filter === 'pending') {
            $query->where(function ($q) {
                $q->where('m.status', '!=', 'Approve')
                  ->orWhereNull('m.status')
                  ->orWhere('m.status', '');
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('m.nomor_hpkk_beras',    'like', "%{$this->search}%")
                  ->orWhere('m.id_mo',              'like', "%{$this->search}%")
                  ->orWhere('m.tempat_pemeriksaan', 'like', "%{$this->search}%")
                  ->orWhere('r.name_cabang',        'like', "%{$this->search}%");
            });
        }

        // Stats
        $statsQuery    = clone $query;
        $allRows       = $statsQuery->get(['m.kuantum_beras', 'm.status']);
        $totalKg       = $allRows->sum(fn($r) => (float) str_replace(',', '.', $r->kuantum_beras ?? 0));
        $totalApproved = $allRows->where('status', 'Approve')->count();
        $totalPending  = $allRows->where('status', '!=', 'Approve')->count();

        $cabangs = DB::table('ref_cabang')
            ->orderBy('name_cabang')
            ->get(['code_cabang', 'name_cabang']);

        return view('livewire.verifikasi-beras', [
            'dataList'      => $query->paginate(15),
            'cabangs'       => $cabangs,
            'totalKg'       => $totalKg,
            'totalApproved' => $totalApproved,
            'totalPending'  => $totalPending,
        ]);
    }
}
