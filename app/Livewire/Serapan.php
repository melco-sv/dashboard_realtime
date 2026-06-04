<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use App\Models\MasHpkkBeras;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Serapan extends Component
{
    public $cabang  = '';
    public $periode = '';

    public $listCabang       = [];
    public $gabahStats       = [];
    public $berasStats       = [];
    public $aktivitasStats   = [];
    public $complianceStats  = [];
    public $momStats         = [];
    public $hasilSampingStats = [];
    public $advancedStats    = null;
    public $rankingCabang    = [];

    public function mount()
    {
        $this->periode = date('Y-m');

        $user  = Auth::user();
        $query = DB::table('ref_cabang')
            ->whereNotNull('name_cabang')
            ->orderBy('name_cabang', 'asc');

        if ($user) {
            if ($user->level == 'Inspektor' && !empty($user->code_cabang)) {
                $query->where('code_cabang', $user->code_cabang);
            }
            // Verification & Super Admin: tidak ada filter → tampil semua cabang
        }

        try {
            $this->listCabang = $query->pluck('name_cabang', 'code_cabang')->toArray();
        } catch (\Exception $e) {
            $this->listCabang = [];
        }
    }

    public function render()
    {
        $cg = 'code_cabang';
        $cb = 'code_cabang';

        $this->gabahStats = [
            'ka'    => $this->getStats(MasHpkkGabah::class, 'kadar_air_rata_rata', 'tanggal_pelaksanaan', $cg),
            'hampa' => $this->getStats(MasHpkkGabah::class, 'kadar_hampa', 'tanggal_pelaksanaan', $cg),
            'hijau' => $this->getStats(MasHpkkGabah::class, 'butir_hijau', 'tanggal_pelaksanaan', $cg),
        ];

        $this->berasStats = [
            'ka'       => $this->getStats(MasHpkkBeras::class, 'rata_rata', 'tanggal_pemeriksaan', $cb),
            'sosoh'    => $this->getStats(MasHpkkBeras::class, 'derajat_sosoh', 'tanggal_pemeriksaan', $cb),
            'patah'    => $this->getStats(MasHpkkBeras::class, 'butir_patah', 'tanggal_pemeriksaan', $cb),
            'menir'    => $this->getStats(MasHpkkBeras::class, 'menir', 'tanggal_pemeriksaan', $cb),
            'rendemen' => $this->getStats(MasHpkkBeras::class, 'rendemen_pengolahan', 'tanggal_pemeriksaan', $cb),
        ];

        $this->aktivitasStats   = $this->getAktivitasStats($cg, $cb);
        $this->complianceStats  = $this->getComplianceStats($cg, $cb);
        $this->momStats         = $this->getMomStats($cg, $cb);
        $this->hasilSampingStats = $this->getHasilSampingStats($cb);

        $lvl = strtolower(trim(Auth::user()->level ?? ''));
        if (Auth::check() && ($lvl === 'super admin' || $lvl === 'verification')) {
            $this->calculateAdvancedStats($cg, $cb);
            $this->rankingCabang = $this->getRankingCabang();
        }

        return view('livewire.serapan');
    }

    // === OPTIMIZED: 1 query per parameter (was 3) ===
    private function getStats($model, $column, $dateColumn, $cabangColumn): array
    {
        try {
            $q = $model::query();
            if (!empty($this->cabang)) $q->where($cabangColumn, $this->cabang);
            if (!empty($this->periode)) $q->where($dateColumn, 'like', $this->periode . '%');

            $r = $q->select([
                DB::raw("MIN(CAST(REPLACE($column, ',', '.') AS DECIMAL(10,2))) as min_val"),
                DB::raw("MAX(CAST(REPLACE($column, ',', '.') AS DECIMAL(10,2))) as max_val"),
                DB::raw("AVG(CAST(REPLACE($column, ',', '.') AS DECIMAL(10,2))) as avg_val"),
            ])->first();

            return [
                'min' => (float) ($r->min_val ?? 0),
                'max' => (float) ($r->max_val ?? 0),
                'avg' => (float) ($r->avg_val ?? 0),
            ];
        } catch (\Exception $e) {
            return ['min' => 0, 'max' => 0, 'avg' => 0];
        }
    }

    // === RINGKASAN AKTIVITAS: jumlah + total kg ===
    private function getAktivitasStats(string $cg, string $cb): array
    {
        try {
            $gabahQ = MasHpkkGabah::query();
            if (!empty($this->cabang)) $gabahQ->where($cg, $this->cabang);
            if (!empty($this->periode)) $gabahQ->where('tanggal_pelaksanaan', 'like', $this->periode . '%');

            $berasQ = MasHpkkBeras::query();
            if (!empty($this->cabang)) $berasQ->where($cb, $this->cabang);
            if (!empty($this->periode)) $berasQ->where('tanggal_pemeriksaan', 'like', $this->periode . '%');

            $g = (clone $gabahQ)->select([
                DB::raw('COUNT(*) as cnt'),
                DB::raw("SUM(CAST(REPLACE(jumlah_timbangan, ',', '.') AS DECIMAL(15,2))) as total_kg"),
            ])->first();

            $b = (clone $berasQ)->select([
                DB::raw('COUNT(*) as cnt'),
                DB::raw("SUM(CAST(REPLACE(kuantum_beras, ',', '.') AS DECIMAL(15,2))) as total_kg"),
            ])->first();

            return [
                'gabah_count' => (int)   ($g->cnt      ?? 0),
                'gabah_kg'    => (float) ($g->total_kg ?? 0),
                'beras_count' => (int)   ($b->cnt      ?? 0),
                'beras_kg'    => (float) ($b->total_kg ?? 0),
            ];
        } catch (\Exception $e) {
            return ['gabah_count' => 0, 'gabah_kg' => 0, 'beras_count' => 0, 'beras_kg' => 0];
        }
    }

    // === COMPLIANCE: % lulus standar ===
    private function getComplianceStats(string $cg, string $cb): array
    {
        try {
            $gBase = MasHpkkGabah::query()
                ->when($this->cabang, fn($q) => $q->where($cg, $this->cabang))
                ->when($this->periode, fn($q) => $q->where('tanggal_pelaksanaan', 'like', $this->periode . '%'));

            $gTotal = (clone $gBase)->count();
            $gLulus = (clone $gBase)
                ->whereRaw("CAST(REPLACE(kadar_air_rata_rata, ',', '.') AS DECIMAL(10,2)) <= 38")
                ->whereRaw("CAST(REPLACE(kadar_hampa, ',', '.') AS DECIMAL(10,2)) <= 40")
                ->whereRaw("CAST(REPLACE(butir_hijau, ',', '.') AS DECIMAL(10,2)) <= 30")
                ->count();

            $bBase = MasHpkkBeras::query()
                ->when($this->cabang, fn($q) => $q->where($cb, $this->cabang))
                ->when($this->periode, fn($q) => $q->where('tanggal_pemeriksaan', 'like', $this->periode . '%'));

            $bTotal = (clone $bBase)->count();
            $bLulus = (clone $bBase)
                ->whereRaw("CAST(REPLACE(rendemen_pengolahan, ',', '.') AS DECIMAL(10,2)) >= 50")
                ->whereRaw("CAST(REPLACE(butir_patah, ',', '.') AS DECIMAL(10,2)) <= 25")
                ->whereRaw("CAST(REPLACE(menir, ',', '.') AS DECIMAL(10,2)) <= 2")
                ->whereRaw("CAST(REPLACE(derajat_sosoh, ',', '.') AS DECIMAL(10,2)) >= 95")
                ->count();

            return [
                'gabah_total' => $gTotal,
                'gabah_lulus' => $gLulus,
                'gabah_pct'   => $gTotal > 0 ? round(($gLulus / $gTotal) * 100, 1) : 0,
                'beras_total' => $bTotal,
                'beras_lulus' => $bLulus,
                'beras_pct'   => $bTotal > 0 ? round(($bLulus / $bTotal) * 100, 1) : 0,
            ];
        } catch (\Exception $e) {
            return ['gabah_total' => 0, 'gabah_lulus' => 0, 'gabah_pct' => 0,
                    'beras_total' => 0, 'beras_lulus' => 0, 'beras_pct' => 0];
        }
    }

    // === MoM: perbandingan vs bulan lalu ===
    private function getMomStats(string $cg, string $cb): array
    {
        if (empty($this->periode)) return [];

        try {
            $prev = date('Y-m', strtotime($this->periode . '-01 -1 month'));

            $agg = function (string $model, string $kgCol, string $dateCol, string $cabCol, string $periode) {
                $q = $model::query();
                if (!empty($this->cabang)) $q->where($cabCol, $this->cabang);
                $q->where($dateCol, 'like', $periode . '%');
                return $q->select([
                    DB::raw('COUNT(*) as cnt'),
                    DB::raw("SUM(CAST(REPLACE($kgCol, ',', '.') AS DECIMAL(15,2))) as total_kg"),
                ])->first();
            };

            $cg_cur  = $agg(MasHpkkGabah::class, 'jumlah_timbangan', 'tanggal_pelaksanaan', $cg, $this->periode);
            $cg_prev = $agg(MasHpkkGabah::class, 'jumlah_timbangan', 'tanggal_pelaksanaan', $cg, $prev);
            $cb_cur  = $agg(MasHpkkBeras::class, 'kuantum_beras', 'tanggal_pemeriksaan', $cb, $this->periode);
            $cb_prev = $agg(MasHpkkBeras::class, 'kuantum_beras', 'tanggal_pemeriksaan', $cb, $prev);

            $chg = fn($cur, $prv) => $prv > 0 ? round((($cur - $prv) / $prv) * 100, 1) : null;

            return [
                'prev_periode'    => $prev,
                'gabah_count_chg' => $chg((int)($cg_cur->cnt ?? 0),      (int)($cg_prev->cnt ?? 0)),
                'gabah_kg_chg'    => $chg((float)($cg_cur->total_kg ?? 0),(float)($cg_prev->total_kg ?? 0)),
                'beras_count_chg' => $chg((int)($cb_cur->cnt ?? 0),      (int)($cb_prev->cnt ?? 0)),
                'beras_kg_chg'    => $chg((float)($cb_cur->total_kg ?? 0),(float)($cb_prev->total_kg ?? 0)),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    // === HASIL SAMPING: total by-products beras ===
    private function getHasilSampingStats(string $cb): array
    {
        try {
            $q = MasHpkkBeras::query();
            if (!empty($this->cabang)) $q->where($cb, $this->cabang);
            if (!empty($this->periode)) $q->where('tanggal_pemeriksaan', 'like', $this->periode . '%');

            $r = (clone $q)->select([
                DB::raw("SUM(CAST(REPLACE(hasil_samping_menir, ',', '.') AS DECIMAL(15,2))) as menir"),
                DB::raw("SUM(CAST(REPLACE(hasil_samping_butir_patah, ',', '.') AS DECIMAL(15,2))) as butir_patah"),
                DB::raw("SUM(CAST(REPLACE(hasil_samping_dedak_katul, ',', '.') AS DECIMAL(15,2))) as dedak_katul"),
                DB::raw("SUM(CAST(REPLACE(hasil_samping_butir_kuning_rusak, ',', '.') AS DECIMAL(15,2))) as kuning_rusak"),
            ])->first();

            $menir  = (float) ($r->menir       ?? 0);
            $patah  = (float) ($r->butir_patah  ?? 0);
            $dedak  = (float) ($r->dedak_katul  ?? 0);
            $kuning = (float) ($r->kuning_rusak ?? 0);

            return [
                'menir'        => $menir,
                'butir_patah'  => $patah,
                'dedak_katul'  => $dedak,
                'kuning_rusak' => $kuning,
                'total'        => $menir + $patah + $dedak + $kuning,
            ];
        } catch (\Exception $e) {
            return ['menir' => 0, 'butir_patah' => 0, 'dedak_katul' => 0, 'kuning_rusak' => 0, 'total' => 0];
        }
    }

    // === RANKING CABANG (Super Admin) ===
    private function getRankingCabang(): array
    {
        try {
            $gabah = MasHpkkGabah::withoutGlobalScopes()
                ->join('ref_cabang', 'mas_hpkk_gabah.code_cabang', '=', 'ref_cabang.code_cabang')
                ->select(
                    'mas_hpkk_gabah.code_cabang as code',
                    'ref_cabang.name_cabang',
                    DB::raw('COUNT(*) as jumlah_gabah'),
                    DB::raw("SUM(CAST(REPLACE(jumlah_timbangan, ',', '.') AS DECIMAL(15,2))) as total_kg_gabah"),
                )
                ->when($this->periode, fn($q) => $q->where('tanggal_pelaksanaan', 'like', $this->periode . '%'))
                ->groupBy('mas_hpkk_gabah.code_cabang', 'ref_cabang.name_cabang')
                ->get();

            $beras = MasHpkkBeras::withoutGlobalScopes()
                ->select(
                    'code_cabang as code',
                    DB::raw('COUNT(*) as jumlah_beras'),
                    DB::raw("SUM(CAST(REPLACE(kuantum_beras, ',', '.') AS DECIMAL(15,2))) as total_kg_beras"),
                )
                ->when($this->periode, fn($q) => $q->where('tanggal_pemeriksaan', 'like', $this->periode . '%'))
                ->groupBy('code_cabang')
                ->get()
                ->keyBy('code');

            return $gabah->map(function ($row) use ($beras) {
                $b         = $beras->get($row->code);
                $gabahKg   = (float) ($row->total_kg_gabah ?? 0);
                $berasKg   = $b ? (float) ($b->total_kg_beras ?? 0) : 0;
                $pendapatan = ($gabahKg * 36) + ($berasKg * 46);
                return [
                    'name'         => $row->name_cabang,
                    'jumlah_gabah' => (int) $row->jumlah_gabah,
                    'jumlah_beras' => $b ? (int) $b->jumlah_beras : 0,
                    'gabah_kg'     => $gabahKg,
                    'beras_kg'     => $berasKg,
                    'pendapatan'   => $pendapatan,
                ];
            })
            ->sortByDesc('pendapatan')
            ->values()
            ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    // === VARIABILITAS (Super Admin) ===
    private function calculateAdvancedStats(string $colGabah, string $colBeras)
    {
        $stddev = function ($model, $column, $dateCol, $locCol) {
            $q = $model::query();
            if (!empty($this->cabang)) $q->where($locCol, $this->cabang);
            if (!empty($this->periode)) $q->where($dateCol, 'like', $this->periode . '%');
            return (float) $q->select(DB::raw("STDDEV(CAST(REPLACE($column, ',', '.') AS DECIMAL(15,2))) as sd"))->value('sd');
        };

        $avgKA  = $this->gabahStats['ka']['avg'];
        $sdKA   = $stddev(MasHpkkGabah::class, 'kadar_air_rata_rata', 'tanggal_pelaksanaan', $colGabah);
        $avgRen = $this->berasStats['rendemen']['avg'];
        $sdRen  = $stddev(MasHpkkBeras::class, 'rendemen_pengolahan', 'tanggal_pemeriksaan', $colBeras);
        $avgPat = $this->berasStats['patah']['avg'];
        $sdPat  = $stddev(MasHpkkBeras::class, 'butir_patah', 'tanggal_pemeriksaan', $colBeras);

        $this->advancedStats = [
            'gabah_ka'       => ['sd' => $sdKA,  'cv' => $avgKA  > 0 ? ($sdKA  / $avgKA)  * 100 : 0],
            'beras_rendemen' => ['sd' => $sdRen, 'cv' => $avgRen > 0 ? ($sdRen / $avgRen) * 100 : 0],
            'beras_patah'    => ['sd' => $sdPat, 'cv' => $avgPat > 0 ? ($sdPat / $avgPat) * 100 : 0],
        ];
    }
}
