<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class VerifikasiBeras extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $cabang_filter  = '';
    public string $status_filter  = 'pending';   // '' = semua, 'pending' = belum diproses, 'Approve' = approved, 'Reject' = ditolak
    public string $tgl_mulai      = '';
    public string $tgl_akhir      = '';

    protected $queryString = [
        'status_filter' => ['except' => 'pending'],
        'cabang_filter' => ['except' => ''],
        'search'        => ['except' => ''],
    ];

    public function mount(): void
    {
        // Tidak ada filter tanggal default — status menjadi filter utama.
    }

    public function updatingSearch(): void        { $this->resetPage(); }
    public function updatingCabangFilter(): void  { $this->resetPage(); }
    public function updatingStatusFilter(): void  { $this->resetPage(); }
    public function updatingTglMulai(): void      { $this->resetPage(); }
    public function updatingTglAkhir(): void      { $this->resetPage(); }

    public function approve(int $id): void
    {
        $row = DB::table('mas_hpkk_beras as m')
            ->leftJoin('ref_cabang as r', 'm.code_cabang', '=', 'r.code_cabang')
            ->where('m.id_hpkk_beras', $id)
            ->select('m.nomor_hpkk_beras', 'm.id_mo', 'r.name_cabang')
            ->first();

        DB::table('mas_hpkk_beras')
            ->where('id_hpkk_beras', $id)
            ->update(['status' => 'Approve']);

        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'no_lhpk' => $row?->nomor_hpkk_beras,
                'no_mo'   => $row?->id_mo,
                'cabang'  => $row?->name_cabang,
            ])
            ->log('Approve HGL');

        session()->flash('message', 'Data berhasil di-approve.');
    }

    public function reject(int $id, string $catatan): void
    {
        $catatan = trim($catatan);

        if ($catatan === '') {
            session()->flash('error', 'Catatan penolakan wajib diisi.');
            return;
        }

        $row = DB::table('mas_hpkk_beras as m')
            ->leftJoin('ref_cabang as r', 'm.code_cabang', '=', 'r.code_cabang')
            ->where('m.id_hpkk_beras', $id)
            ->select('m.nomor_hpkk_beras', 'm.id_mo', 'r.name_cabang')
            ->first();

        DB::table('mas_hpkk_beras')
            ->where('id_hpkk_beras', $id)
            ->update([
                'status'  => 'Reject',
                'catatan' => $catatan,
            ]);

        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'no_lhpk' => $row?->nomor_hpkk_beras,
                'no_mo'   => $row?->id_mo,
                'cabang'  => $row?->name_cabang,
                'catatan' => $catatan,
            ])
            ->log('Reject HGL');

        session()->flash('message', 'Data berhasil ditolak.');
    }

    public function render()
    {
        $query = DB::table('mas_hpkk_beras as m')
            ->leftJoin('ref_cabang as r', 'm.code_cabang', '=', 'r.code_cabang')
            ->select(
                'm.id_hpkk_beras', 'm.nomor_hpkk_beras', 'm.id_mo',
                'm.tempat_pemeriksaan', 'm.tanggal_pemeriksaan',
                'm.kuantum_beras', 'm.status', 'm.catatan',
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
            $query->where('m.code_cabang', $this->cabang_filter);
        }

        if ($this->status_filter === 'Approve') {
            $query->where('m.status', 'Approve');
        } elseif ($this->status_filter === 'Reject') {
            $query->where('m.status', 'Reject');
        } elseif ($this->status_filter === 'pending') {
            // Truly pending: bukan 'Approve' DAN bukan 'Reject' (NULL / '' / lainnya).
            $query->where(function ($q) {
                $q->whereNull('m.status')
                  ->orWhereNotIn('m.status', ['Approve', 'Reject']);
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

        // Stats — clone the query (keeps WHERE/JOIN), reset SELECT+ORDER, then aggregate
        $statsQuery = clone $query;
        $statsQuery->columns = null;
        $statsQuery->orders  = null;
        $stats = $statsQuery->selectRaw("
            SUM(CAST(REPLACE(COALESCE(m.kuantum_beras, '0'), ',', '.') AS DECIMAL(15,2))) as total_kg,
            SUM(CASE WHEN m.status = 'Approve' THEN 1 ELSE 0 END) as total_approved,
            SUM(CASE WHEN m.status = 'Reject' THEN 1 ELSE 0 END) as total_rejected,
            SUM(CASE WHEN COALESCE(m.status, '') NOT IN ('Approve', 'Reject') THEN 1 ELSE 0 END) as total_pending
        ")->first();

        $totalKg       = (float) ($stats?->total_kg ?? 0);
        $totalApproved = (int)   ($stats?->total_approved ?? 0);
        $totalRejected = (int)   ($stats?->total_rejected ?? 0);
        $totalPending  = (int)   ($stats?->total_pending ?? 0);

        $cabangs = Cache::remember('ref_cabang_all', 3600, fn () =>
            DB::table('ref_cabang')->orderBy('name_cabang')->get(['code_cabang', 'name_cabang'])
        );

        return view('livewire.verifikasi-beras', [
            'dataList'      => $query->paginate(15),
            'cabangs'       => $cabangs,
            'totalKg'       => $totalKg,
            'totalApproved' => $totalApproved,
            'totalRejected' => $totalRejected,
            'totalPending'  => $totalPending,
        ]);
    }
}
