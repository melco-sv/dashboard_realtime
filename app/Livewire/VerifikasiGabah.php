<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class VerifikasiGabah extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $cabang_filter  = '';
    public string $status_filter  = '';   // '' = semua, 'Approve' = approved, 'pending' = belum
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
        $row = DB::table('mas_hpkk_gabah as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->where('m.id_po', $id)
            ->select('m.nomor_hpkk_gabah', 'm.mitra', 'r.name_cabang')
            ->first();

        DB::table('mas_hpkk_gabah')
            ->where('id_po', $id)
            ->update(['status_data' => 'Approve']);

        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'no_hpk'  => $row?->nomor_hpkk_gabah,
                'mitra'   => $row?->mitra,
                'cabang'  => $row?->name_cabang,
            ])
            ->log('Approve GKP');

        session()->flash('message', 'Data berhasil di-approve.');
    }

    public function render()
    {
        $query = DB::table('mas_hpkk_gabah as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->select(
                'm.id_po', 'm.nomor_hpkk_gabah', 'm.mitra', 'm.pengirim',
                'm.tanggal_pelaksanaan', 'm.jumlah_timbangan',
                'm.status_data', 'm.lokasi', 'm.no_order_pembelian',
                'm.kadar_air_rata_rata', 'm.kadar_hampa', 'm.butir_hijau',
                'r.name_cabang'
            )
            ->selectSub(
                DB::table('ref_upload')->selectRaw('COUNT(*)')->whereColumn('id_hpkk_gabah', 'm.id_po'),
                'fotos_count'
            )
            ->orderBy('m.id_po', 'desc');

        if ($this->tgl_mulai && $this->tgl_akhir) {
            $query->whereBetween('m.tanggal_pelaksanaan', [
                $this->tgl_mulai . ' 00:00:00',
                $this->tgl_akhir . ' 23:59:59',
            ]);
        }

        if ($this->cabang_filter) {
            $query->where('m.group', $this->cabang_filter);
        }

        if ($this->status_filter === 'Approve') {
            $query->where('m.status_data', 'Approve');
        } elseif ($this->status_filter === 'pending') {
            $query->where(function ($q) {
                $q->where('m.status_data', '!=', 'Approve')
                  ->orWhereNull('m.status_data')
                  ->orWhere('m.status_data', '');
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('m.nomor_hpkk_gabah', 'like', "%{$this->search}%")
                  ->orWhere('m.mitra',           'like', "%{$this->search}%")
                  ->orWhere('m.pengirim',         'like', "%{$this->search}%")
                  ->orWhere('r.name_cabang',      'like', "%{$this->search}%");
            });
        }

        // Stats via DB aggregate — efisien, tidak load semua row ke PHP
        $stats = (clone $query)->selectRaw("
            SUM(CAST(REPLACE(COALESCE(m.jumlah_timbangan, '0'), ',', '.') AS DECIMAL(15,2))) as total_kg,
            SUM(CASE WHEN m.status_data = 'Approve' THEN 1 ELSE 0 END) as total_approved,
            SUM(CASE WHEN COALESCE(m.status_data, '') != 'Approve' THEN 1 ELSE 0 END) as total_pending
        ")->first();

        $totalKg       = (float) ($stats?->total_kg ?? 0);
        $totalApproved = (int)   ($stats?->total_approved ?? 0);
        $totalPending  = (int)   ($stats?->total_pending ?? 0);

        $cabangs = Cache::remember('ref_cabang_all', 3600, fn () =>
            DB::table('ref_cabang')->orderBy('name_cabang')->get(['code_cabang', 'name_cabang'])
        );

        return view('livewire.verifikasi-gabah', [
            'dataList'      => $query->paginate(15),
            'cabangs'       => $cabangs,
            'totalKg'       => $totalKg,
            'totalApproved' => $totalApproved,
            'totalPending'  => $totalPending,
        ]);
    }
}
