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

    // Widget baru (SuperAdmin / Verification)
    public $totalPendapatanGabungDisplay = '';
    public $totalDiterimaGabungDisplay   = '';
    public $bastBelumDibayarCount        = 0;
    public $cabangAktifBulanIni          = 0;
    // Quality averages
    public $avgKadarAir    = 0;
    public $avgKadarHampa  = 0;
    public $avgButirHijau  = 0;
    public $avgDerajatSosoh = 0;
    public $avgButirPatah  = 0;
    public $avgMenir       = 0;
    // Top cabang & aktivitas
    public $topCabang      = [];
    public $recentActivity = [];

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
        $this->loadData();

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

        // Ambil tarif dari settings
        $tarifGabah = (float) (DB::table('ref_settings')->where('key', 'tarif_bast_gabah')->value('value') ?? 36);
        $tarifBeras = (float) (DB::table('ref_settings')->where('key', 'tarif_bast_beras')->value('value') ?? 46);

        // Pendapatan per komoditas
        $this->pendapatanGabahDisplay = 'Rp ' . number_format($this->totalGabahKg * $tarifGabah, 0, ',', '.');
        $this->pendapatanBerasDisplay = 'Rp ' . number_format($this->totalBerasKg * $tarifBeras, 0, ',', '.');

        // Total Pendapatan Gabungan
        $this->totalPendapatanGabungDisplay = 'Rp ' . number_format(
            ($this->totalGabahKg * $tarifGabah) + ($this->totalBerasKg * $tarifBeras), 0, ',', '.'
        );

        // Total Diterima dari BAST DIBAYAR (hanya Verification + SuperAdmin)
        $user = Auth::user();
        $this->showFinancial = $user instanceof \App\Models\User && ($user->isVerification() || $user->isSuperAdmin());

        if ($this->showFinancial) {
            $gabahDibayarKg = (float) DB::table('mas_hpkk_gabah as g')
                ->join('ref_bast_status as b', function ($join) {
                    $join->on(DB::raw('CONVERT(b.code_cabang USING utf8mb4)'), '=', DB::raw('CONVERT(g.`code_cabang` USING utf8mb4)'))
                         ->where('b.jenis', 'GKP')
                         ->where('b.status', 'DIBAYAR')
                         ->whereRaw('DATE(g.tanggal_pelaksanaan) BETWEEN b.tgl_mulai AND b.tgl_akhir');
                })
                ->sum(DB::raw("CAST(REPLACE(g.jumlah_timbangan, ',', '.') AS DECIMAL(15,2))"));

            $berasDibayarKg = (float) DB::table('mas_hpkk_beras as b2')
                ->join('ref_bast_status as s', function ($join) {
                    $join->on(DB::raw('CONVERT(s.code_cabang USING utf8mb4)'), '=', DB::raw('CONVERT(b2.`code_cabang` USING utf8mb4)'))
                         ->where('s.jenis', 'HGL')
                         ->where('s.status', 'DIBAYAR')
                         ->whereRaw('DATE(b2.tanggal_pemeriksaan) BETWEEN s.tgl_mulai AND s.tgl_akhir');
                })
                ->sum(DB::raw("CAST(REPLACE(b2.kuantum_beras, ',', '.') AS DECIMAL(15,2))"));

            $this->totalDidipatGabahDisplay = 'Rp ' . number_format($gabahDibayarKg * $tarifGabah, 0, ',', '.');
            $this->totalDidipatBerasDisplay = 'Rp ' . number_format($berasDibayarKg * $tarifBeras, 0, ',', '.');
            $this->totalDiterimaGabungDisplay = 'Rp ' . number_format(
                ($gabahDibayarKg * $tarifGabah) + ($berasDibayarKg * $tarifBeras), 0, ',', '.'
            );

            // BAST belum dibayar
            $this->bastBelumDibayarCount = DB::table('ref_bast_status')
                ->where('status', '!=', 'DIBAYAR')
                ->count();

            // Cabang aktif bulan ini
            $bulanIni = date('Y-m');
            $this->cabangAktifBulanIni = DB::table('mas_hpkk_gabah')
                ->where('tanggal_pelaksanaan', 'like', $bulanIni . '%')
                ->whereNotNull('code_cabang')
                ->distinct()->count('code_cabang');

            // Quality averages gabah
            $qg = DB::table('mas_hpkk_gabah')->selectRaw("
                AVG(CAST(REPLACE(kadar_air_rata_rata, ',', '.') AS DECIMAL(10,2))) as avg_ka,
                AVG(CAST(REPLACE(kadar_hampa, ',', '.') AS DECIMAL(10,2))) as avg_hampa,
                AVG(CAST(REPLACE(butir_hijau, ',', '.') AS DECIMAL(10,2))) as avg_hijau
            ")->first();
            $this->avgKadarAir   = round((float) ($qg->avg_ka    ?? 0), 1);
            $this->avgKadarHampa = round((float) ($qg->avg_hampa ?? 0), 1);
            $this->avgButirHijau = round((float) ($qg->avg_hijau ?? 0), 1);

            // Quality averages beras
            $qb = DB::table('mas_hpkk_beras')->selectRaw("
                AVG(CAST(REPLACE(derajat_sosoh, ',', '.') AS DECIMAL(10,2))) as avg_sosoh,
                AVG(CAST(REPLACE(butir_patah, ',', '.') AS DECIMAL(10,2))) as avg_patah,
                AVG(CAST(REPLACE(menir, ',', '.') AS DECIMAL(10,2))) as avg_menir
            ")->first();
            $this->avgDerajatSosoh = round((float) ($qb->avg_sosoh ?? 0), 1);
            $this->avgButirPatah   = round((float) ($qb->avg_patah ?? 0), 1);
            $this->avgMenir        = round((float) ($qb->avg_menir ?? 0), 1);

            // Top 5 cabang bulan ini by pendapatan
            $gPerCabang = DB::table('mas_hpkk_gabah as g')
                ->join('ref_cabang as rc',
                    DB::raw('CONVERT(g.`code_cabang` USING utf8mb4)'), '=',
                    DB::raw('CONVERT(rc.code_cabang USING utf8mb4)'))
                ->select('rc.name_cabang', 'g.code_cabang as code',
                    DB::raw("SUM(CAST(REPLACE(g.jumlah_timbangan, ',', '.') AS DECIMAL(15,2))) as kg"))
                ->where('g.tanggal_pelaksanaan', 'like', $bulanIni . '%')
                ->groupBy('g.code_cabang', 'rc.name_cabang')
                ->get()->keyBy('code');

            $bPerCabang = DB::table('mas_hpkk_beras')
                ->select('code_cabang as code',
                    DB::raw("SUM(CAST(REPLACE(kuantum_beras, ',', '.') AS DECIMAL(15,2))) as kg"))
                ->where('tanggal_pemeriksaan', 'like', $bulanIni . '%')
                ->groupBy('code_cabang')
                ->get()->keyBy('code');

            $allPendapatan = $gPerCabang->map(function ($g) use ($bPerCabang, $tarifGabah, $tarifBeras) {
                $b = $bPerCabang->get($g->code);
                return [
                    'name'      => $g->name_cabang,
                    'pendapatan' => ((float) $g->kg * $tarifGabah) + ($b ? (float) $b->kg * $tarifBeras : 0),
                ];
            })->sortByDesc('pendapatan');

            $maxPendapatan = $allPendapatan->first()['pendapatan'] ?? 1;
            $this->topCabang = $allPendapatan->take(5)->map(function ($row) use ($maxPendapatan) {
                $row['pct'] = $maxPendapatan > 0 ? round(($row['pendapatan'] / $maxPendapatan) * 100) : 0;
                return $row;
            })->values()->toArray();

            // 5 aktivitas terbaru
            $this->recentActivity = DB::table('activity_log as al')
                ->leftJoin('mas_user as u', 'al.causer_id', '=', 'u.id_user')
                ->select('al.description', 'u.nama', 'al.created_at')
                ->orderBy('al.created_at', 'desc')
                ->limit(5)->get()
                ->map(fn($r) => [
                    'desc' => $r->description,
                    'nama' => $r->nama ?? '—',
                    'at'   => $r->created_at,
                ])->toArray();
        }
    }
}
