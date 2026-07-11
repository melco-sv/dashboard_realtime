<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use App\Models\MasHpkkBeras;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    // Filter tahun untuk grafik tren + kartu total
    public $selectedYear;
    public $availableYears = [];

    public function mount()
    {
        $this->initYear();
        // loadData() tidak perlu dipanggil di sini — render() selalu dijalankan
        // setelah mount() pada permintaan pertama dan sudah memanggil loadData().
    }

    private function initYear(): void
    {
        $currentYear = (int) date('Y');

        // Cache singkat (5 menit): scan DISTINCT YEAR pada dua tabel besar
        // hanya untuk daftar opsi dropdown tahun yang sangat jarang berubah.
        // Query tetap memakai CabangScope; key cache dibedakan per (level, cabang)
        // agar hasil scoped tidak bocor antar user.
        $user = Auth::user();
        $cacheKey = 'dashboard_data_years_'
            . strtolower($user->level ?? 'guest') . '_'
            . ($user->code_cabang ?? 'all');

        $dataYears = collect(Cache::remember($cacheKey, 300, fn () =>
            MasHpkkGabah::whereNotNull('tanggal_pelaksanaan')
                ->select(DB::raw('DISTINCT YEAR(tanggal_pelaksanaan) as y'))->pluck('y')
                ->merge(
                    MasHpkkBeras::whereNotNull('tanggal_pemeriksaan')
                        ->select(DB::raw('DISTINCT YEAR(tanggal_pemeriksaan) as y'))->pluck('y')
                )
                ->map(fn ($y) => (int) $y)
                ->filter()
                ->unique()
                ->sortDesc()
                ->values()
                ->all()
        ));

        // Default: tahun sekarang bila ada datanya; jika tidak → tahun berdata terbaru; jika kosong → tahun sekarang
        $this->selectedYear = $dataYears->contains($currentYear)
            ? $currentYear
            : ($dataYears->first() ?? $currentYear);

        // Opsi dropdown: tahun berdata + tahun berjalan (agar selalu bisa dipilih)
        $this->availableYears = $dataYears->contains($currentYear)
            ? $dataYears->values()->all()
            : $dataYears->concat([$currentYear])->sortDesc()->values()->all();
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
        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $year = (int) $this->selectedYear;

        // Rentang tanggal setahun penuh — ekuivalen dengan whereYear() (diverifikasi
        // hasil identik pada DB), tetapi sargable sehingga bisa memakai index tanggal.
        $yearStart = "{$year}-01-01";
        $yearEnd   = "{$year}-12-31";

        // Gabah — total per bulan untuk tahun terpilih (dinormalisasi 12 bulan)
        $gabahByMonth = MasHpkkGabah::select(
            DB::raw("MONTH(tanggal_pelaksanaan) as bulan"),
            DB::raw("SUM(CAST(REPLACE(jumlah_timbangan, ',', '.') AS DECIMAL(15,2))) as total")
        )
            ->whereBetween('tanggal_pelaksanaan', [$yearStart, $yearEnd])
            ->groupBy(DB::raw("MONTH(tanggal_pelaksanaan)"))
            ->pluck('total', 'bulan');

        $this->gabahLabels = $namaBulan;
        $this->gabahValues = [];
        for ($m = 1; $m <= 12; $m++) {
            $this->gabahValues[] = (float) $gabahByMonth->get($m, 0);
        }
        $this->totalGabahKg        = array_sum($this->gabahValues);
        $this->totalGabahAnalisa   = MasHpkkGabah::whereBetween('tanggal_pelaksanaan', [$yearStart, $yearEnd])->count();
        $this->totalGabahKgDisplay = number_format($this->totalGabahKg, 2, ',', '.');

        // Beras — total per bulan untuk tahun terpilih (dinormalisasi 12 bulan)
        $berasByMonth = MasHpkkBeras::select(
            DB::raw("MONTH(tanggal_pemeriksaan) as bulan"),
            DB::raw("SUM(CAST(REPLACE(kuantum_beras, ',', '.') AS DECIMAL(15,2))) as total")
        )
            ->whereBetween('tanggal_pemeriksaan', [$yearStart, $yearEnd])
            ->groupBy(DB::raw("MONTH(tanggal_pemeriksaan)"))
            ->pluck('total', 'bulan');

        $this->berasLabels = $namaBulan;
        $this->berasValues = [];
        for ($m = 1; $m <= 12; $m++) {
            $this->berasValues[] = (float) $berasByMonth->get($m, 0);
        }
        $this->totalBerasKg        = array_sum($this->berasValues);
        $this->totalBerasAnalisa   = MasHpkkBeras::whereBetween('tanggal_pemeriksaan', [$yearStart, $yearEnd])->count();
        $this->totalBerasKgDisplay = number_format($this->totalBerasKg, 2, ',', '.');

        // Ambil tarif dari settings — cache permanen dengan key yang sama seperti
        // BastGabah/BastBeras; TarifSetting::save() memanggil Cache::forget() untuk
        // kedua key ini sehingga nilai selalu segar setelah tarif diubah.
        $tarifGabahRow = Cache::rememberForever('tarif_bast_gabah', fn () =>
            DB::table('ref_settings')->where('key', 'tarif_bast_gabah')->first()
        );
        $tarifBerasRow = Cache::rememberForever('tarif_bast_beras', fn () =>
            DB::table('ref_settings')->where('key', 'tarif_bast_beras')->first()
        );
        $tarifGabah = (float) ($tarifGabahRow->value ?? 36);
        $tarifBeras = (float) ($tarifBerasRow->value ?? 46);

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
            // Join langsung tanpa CONVERT(... USING utf8mb4): kedua kolom code_cabang
            // sudah INT UNSIGNED (migrasi 2026_05_31), CONVERT justru mematikan index.
            // Hasil diverifikasi identik pada DB (sebelum vs sesudah).
            $gabahDibayarKg = (float) DB::table('mas_hpkk_gabah as g')
                ->join('ref_bast_status as b', function ($join) {
                    $join->on('b.code_cabang', '=', 'g.code_cabang')
                         ->where('b.jenis', 'GKP')
                         ->where('b.status', 'DIBAYAR')
                         ->whereRaw('g.tanggal_pelaksanaan BETWEEN b.tgl_mulai AND b.tgl_akhir');
                })
                ->sum(DB::raw("CAST(REPLACE(g.jumlah_timbangan, ',', '.') AS DECIMAL(15,2))"));

            $berasDibayarKg = (float) DB::table('mas_hpkk_beras as b2')
                ->join('ref_bast_status as s', function ($join) {
                    $join->on('s.code_cabang', '=', 'b2.code_cabang')
                         ->where('s.jenis', 'HGL')
                         ->where('s.status', 'DIBAYAR')
                         ->whereRaw('b2.tanggal_pemeriksaan BETWEEN s.tgl_mulai AND s.tgl_akhir');
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

            // Cabang aktif bulan ini — range tanggal (bukan LIKE pada kolom DATE)
            // agar bisa memakai index; hasil diverifikasi identik.
            $bulanIni      = date('Y-m');
            $bulanIniAwal  = $bulanIni . '-01';
            $bulanDepanAwal = date('Y-m-01', strtotime($bulanIniAwal . ' +1 month'));

            $this->cabangAktifBulanIni = DB::table('mas_hpkk_gabah')
                ->where('tanggal_pelaksanaan', '>=', $bulanIniAwal)
                ->where('tanggal_pelaksanaan', '<', $bulanDepanAwal)
                ->whereNotNull('code_cabang')
                ->distinct()->count('code_cabang');

            // Quality averages (rata-rata seluruh riwayat, full scan 2 tabel besar) —
            // di-cache 60 detik agar tidak dihitung ulang pada setiap poll 30 detik.
            $quality = Cache::remember('dashboard_quality_avgs', 60, function () {
                $qg = DB::table('mas_hpkk_gabah')->selectRaw("
                    AVG(CAST(REPLACE(kadar_air_rata_rata, ',', '.') AS DECIMAL(10,2))) as avg_ka,
                    AVG(CAST(REPLACE(kadar_hampa, ',', '.') AS DECIMAL(10,2))) as avg_hampa,
                    AVG(CAST(REPLACE(butir_hijau, ',', '.') AS DECIMAL(10,2))) as avg_hijau
                ")->first();

                $qb = DB::table('mas_hpkk_beras')->selectRaw("
                    AVG(CAST(REPLACE(derajat_sosoh, ',', '.') AS DECIMAL(10,2))) as avg_sosoh,
                    AVG(CAST(REPLACE(butir_patah, ',', '.') AS DECIMAL(10,2))) as avg_patah,
                    AVG(CAST(REPLACE(menir, ',', '.') AS DECIMAL(10,2))) as avg_menir
                ")->first();

                return [
                    'ka'    => round((float) ($qg->avg_ka    ?? 0), 1),
                    'hampa' => round((float) ($qg->avg_hampa ?? 0), 1),
                    'hijau' => round((float) ($qg->avg_hijau ?? 0), 1),
                    'sosoh' => round((float) ($qb->avg_sosoh ?? 0), 1),
                    'patah' => round((float) ($qb->avg_patah ?? 0), 1),
                    'menir' => round((float) ($qb->avg_menir ?? 0), 1),
                ];
            });
            $this->avgKadarAir     = $quality['ka'];
            $this->avgKadarHampa   = $quality['hampa'];
            $this->avgButirHijau   = $quality['hijau'];
            $this->avgDerajatSosoh = $quality['sosoh'];
            $this->avgButirPatah   = $quality['patah'];
            $this->avgMenir        = $quality['menir'];

            // Top 5 cabang bulan ini by pendapatan — join tanpa CONVERT + filter range
            // (hasil diverifikasi identik dengan versi CONVERT + LIKE).
            $gPerCabang = DB::table('mas_hpkk_gabah as g')
                ->join('ref_cabang as rc', 'g.code_cabang', '=', 'rc.code_cabang')
                ->select('rc.name_cabang', 'g.code_cabang as code',
                    DB::raw("SUM(CAST(REPLACE(g.jumlah_timbangan, ',', '.') AS DECIMAL(15,2))) as kg"))
                ->where('g.tanggal_pelaksanaan', '>=', $bulanIniAwal)
                ->where('g.tanggal_pelaksanaan', '<', $bulanDepanAwal)
                ->groupBy('g.code_cabang', 'rc.name_cabang')
                ->get()->keyBy('code');

            $bPerCabang = DB::table('mas_hpkk_beras')
                ->select('code_cabang as code',
                    DB::raw("SUM(CAST(REPLACE(kuantum_beras, ',', '.') AS DECIMAL(15,2))) as kg"))
                ->where('tanggal_pemeriksaan', '>=', $bulanIniAwal)
                ->where('tanggal_pemeriksaan', '<', $bulanDepanAwal)
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
