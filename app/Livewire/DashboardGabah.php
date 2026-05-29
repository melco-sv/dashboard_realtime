<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use App\Models\MasHpkkBeras;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardGabah extends Component
{
    public $totalGabahKg;
    public $totalBerasKg;
    public $totalGabahKgDisplay;
    public $totalBerasKgDisplay;
    public $totalGabahAnalisa;
    public $totalBerasAnalisa;

    public $pendapatanGabahDisplay;
    public $pendapatanBerasDisplay;
    public $showFinancial = false;
    public $totalDidipatGabahDisplay;
    public $totalDidipatBerasDisplay;

    // Data chart disimpan sebagai properti agar bisa di-embed langsung ke HTML
    public $gabahLabels = [];
    public $gabahValues = [];
    public $berasLabels = [];
    public $berasValues = [];

    public function mount()
    {
        $this->loadData();
    }

    public function render()
    {
        // Hanya re-load saat polling (mount() sudah handle load pertama)
        if ($this->gabahLabels === []) {
            $this->loadData();
        }

        $this->dispatch('update-charts', [
            'gabah_labels' => $this->gabahLabels,
            'gabah_values' => $this->gabahValues,
            'beras_labels' => $this->berasLabels,
            'beras_values' => $this->berasValues,
        ]);

        return view('livewire.dashboard-gabah');
    }

    private function loadData()
    {
        // Gabah
        $gabahChart = MasHpkkGabah::select(
            DB::raw("DATE_FORMAT(tanggal_pelaksanaan, '%Y-%m') as bulan"),
            DB::raw("SUM(CAST(REPLACE(jumlah_timbangan, ',', '.') AS DECIMAL(15,2))) as total")
        )
            ->whereNotNull('tanggal_pelaksanaan')
            ->groupBy(DB::raw("DATE_FORMAT(tanggal_pelaksanaan, '%Y-%m')"))
            ->orderBy('bulan', 'asc')
            ->get();

        $this->totalGabahKg       = $gabahChart->sum('total');
        $this->totalGabahAnalisa  = MasHpkkGabah::count();
        $this->totalGabahKgDisplay = number_format($this->totalGabahKg, 2, ',', '.');
        $this->gabahLabels        = $gabahChart->pluck('bulan')->values()->all();
        $this->gabahValues        = $gabahChart->pluck('total')->map(fn($v) => (float)$v)->values()->all();

        // Beras
        $berasChart = MasHpkkBeras::select(
            DB::raw("DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m') as bulan"),
            DB::raw("SUM(CAST(REPLACE(kuantum_beras, ',', '.') AS DECIMAL(15,2))) as total")
        )
            ->whereNotNull('tanggal_pemeriksaan')
            ->groupBy(DB::raw("DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m')"))
            ->orderBy('bulan', 'asc')
            ->get();

        $this->totalBerasKg       = $berasChart->sum('total');
        $this->totalBerasAnalisa  = MasHpkkBeras::count();
        $this->totalBerasKgDisplay = number_format($this->totalBerasKg, 2, ',', '.');
        $this->berasLabels        = $berasChart->pluck('bulan')->values()->all();
        $this->berasValues        = $berasChart->pluck('total')->map(fn($v) => (float)$v)->values()->all();

        // Pendapatan (semua level)
        $this->pendapatanGabahDisplay = 'Rp ' . number_format($this->totalGabahKg * 36, 0, ',', '.');
        $this->pendapatanBerasDisplay = 'Rp ' . number_format($this->totalBerasKg * 46, 0, ',', '.');

        // Total Diterima dari BAST DIBAYAR (hanya Verification + SuperAdmin)
        $user = Auth::user();
        $this->showFinancial = $user instanceof \App\Models\User && ($user->isVerification() || $user->isSuperAdmin());

        if ($this->showFinancial) {
            $gabahDibayarKg = (float) DB::table('mas_hpkk_gabah as g')
                ->join('ref_bast_status as b', function ($join) {
                    $join->on(DB::raw('CONVERT(b.code_cabang USING utf8mb4)'), '=', DB::raw('CONVERT(g.`group` USING utf8mb4)'))
                         ->where('b.jenis', 'GKP')
                         ->where('b.status', 'DIBAYAR')
                         ->whereRaw('DATE(g.tanggal_pelaksanaan) BETWEEN b.tgl_mulai AND b.tgl_akhir');
                })
                ->sum(DB::raw("CAST(REPLACE(g.jumlah_timbangan, ',', '.') AS DECIMAL(15,2))"));

            $berasDibayarKg = (float) DB::table('mas_hpkk_beras as b2')
                ->join('ref_bast_status as s', function ($join) {
                    $join->on(DB::raw('CONVERT(s.code_cabang USING utf8mb4)'), '=', DB::raw('CONVERT(b2.`group` USING utf8mb4)'))
                         ->where('s.jenis', 'HGL')
                         ->where('s.status', 'DIBAYAR')
                         ->whereRaw('DATE(b2.tanggal_pemeriksaan) BETWEEN s.tgl_mulai AND s.tgl_akhir');
                })
                ->sum(DB::raw("CAST(REPLACE(b2.kuantum_beras, ',', '.') AS DECIMAL(15,2))"));

            $this->totalDidipatGabahDisplay = 'Rp ' . number_format($gabahDibayarKg * 36, 0, ',', '.');
            $this->totalDidipatBerasDisplay = 'Rp ' . number_format($berasDibayarKg * 46, 0, ',', '.');
        }
    }
}
