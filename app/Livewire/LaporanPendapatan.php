<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PendapatanCabangExport;

class LaporanPendapatan extends Component
{
    public string $tgl_mulai = '';
    public string $tgl_akhir = '';

    public function mount(): void
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
    }

    private function tarif(): array
    {
        $g = (float) (DB::table('ref_settings')->where('key', 'tarif_bast_gabah')->value('value') ?? 36);
        $b = (float) (DB::table('ref_settings')->where('key', 'tarif_bast_beras')->value('value') ?? 46);
        return [$g, $b];
    }

    /**
     * Pendapatan per cabang = (tonase gabah × tarif gabah) + (tonase beras × tarif beras),
     * diurutkan dari pendapatan tertinggi. Pakai DB::table + CONVERT utf8mb4 (mengikuti
     * pola DashboardGabah) karena ada perbedaan collation pada kolom code_cabang.
     */
    public function getRows()
    {
        [$tarifGabah, $tarifBeras] = $this->tarif();

        $gabahQ = DB::table('mas_hpkk_gabah as g')
            ->leftJoin('ref_cabang as rc', DB::raw('CONVERT(g.`code_cabang` USING utf8mb4)'), '=', DB::raw('CONVERT(rc.code_cabang USING utf8mb4)'))
            ->select(
                'g.code_cabang as code',
                'rc.name_cabang',
                'rc.parent_company',
                DB::raw("SUM(CAST(REPLACE(g.jumlah_timbangan, ',', '.') AS DECIMAL(15,2))) as kg"),
                DB::raw("COUNT(*) as jml")
            );
        if ($this->tgl_mulai && $this->tgl_akhir) {
            $gabahQ->whereBetween('g.tanggal_pelaksanaan', [$this->tgl_mulai . ' 00:00:00', $this->tgl_akhir . ' 23:59:59']);
        }
        $gabah = $gabahQ->groupBy('g.code_cabang', 'rc.name_cabang', 'rc.parent_company')->get()->keyBy('code');

        $berasQ = DB::table('mas_hpkk_beras as b')
            ->leftJoin('ref_cabang as rc', DB::raw('CONVERT(b.`code_cabang` USING utf8mb4)'), '=', DB::raw('CONVERT(rc.code_cabang USING utf8mb4)'))
            ->select(
                'b.code_cabang as code',
                'rc.name_cabang',
                'rc.parent_company',
                DB::raw("SUM(CAST(REPLACE(b.kuantum_beras, ',', '.') AS DECIMAL(15,2))) as kg"),
                DB::raw("COUNT(*) as jml")
            );
        if ($this->tgl_mulai && $this->tgl_akhir) {
            $berasQ->whereBetween('b.tanggal_pemeriksaan', [$this->tgl_mulai . ' 00:00:00', $this->tgl_akhir . ' 23:59:59']);
        }
        $beras = $berasQ->groupBy('b.code_cabang', 'rc.name_cabang', 'rc.parent_company')->get()->keyBy('code');

        $codes = $gabah->keys()->merge($beras->keys())->unique()
            ->filter(fn ($c) => $c !== null && $c !== '');

        $rows = $codes->map(function ($code) use ($gabah, $beras, $tarifGabah, $tarifBeras) {
            $g = $gabah->get($code);
            $b = $beras->get($code);

            $gabahKg = (float) ($g->kg ?? 0);
            $berasKg = (float) ($b->kg ?? 0);
            $pg = $gabahKg * $tarifGabah;
            $pb = $berasKg * $tarifBeras;

            return (object) [
                'wilayah'          => $g->parent_company ?? $b->parent_company ?? '-',
                'cabang'           => $g->name_cabang ?? $b->name_cabang ?? ('Cabang ' . $code),
                'gabah_kg'         => $gabahKg,
                'beras_kg'         => $berasKg,
                'total_kg'         => $gabahKg + $berasKg,
                'pendapatan_gabah' => $pg,
                'pendapatan_beras' => $pb,
                'total_pendapatan' => $pg + $pb,
                'dok_gkp'          => (int) ($g->jml ?? 0),
                'dok_hgl'          => (int) ($b->jml ?? 0),
            ];
        })->sortByDesc('total_pendapatan')->values();

        $grand = $rows->sum('total_pendapatan');

        return $rows->map(function ($r) use ($grand) {
            $r->kontribusi = $grand > 0 ? round($r->total_pendapatan / $grand * 100, 2) : 0;
            return $r;
        });
    }

    private function periodLabel(): string
    {
        if ($this->tgl_mulai && $this->tgl_akhir) {
            return 'Periode: ' . \Carbon\Carbon::parse($this->tgl_mulai)->isoFormat('D MMM Y')
                . ' s.d. ' . \Carbon\Carbon::parse($this->tgl_akhir)->isoFormat('D MMM Y');
        }
        return 'Seluruh Periode';
    }

    public function download()
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        [$tarifGabah, $tarifBeras] = $this->tarif();

        return Excel::download(
            new PendapatanCabangExport($this->getRows(), $tarifGabah, $tarifBeras, $this->periodLabel()),
            'Laporan_Pendapatan_Cabang.xlsx'
        );
    }

    public function render()
    {
        $rows = $this->getRows();

        return view('livewire.laporan-pendapatan', [
            'rows'         => $rows,
            'grandTotal'   => $rows->sum('total_pendapatan'),
            'grandGabahKg' => $rows->sum('gabah_kg'),
            'grandBerasKg' => $rows->sum('beras_kg'),
            'top'          => $rows->first(),
        ]);
    }
}
