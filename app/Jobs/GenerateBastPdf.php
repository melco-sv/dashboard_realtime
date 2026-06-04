<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Mpdf\Mpdf;
use App\Traits\PdfLogoHelper;

class GenerateBastPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, PdfLogoHelper;

    public int $timeout = 600;
    public int $tries   = 1;

    public function __construct(
        public readonly string $jenis,
        public readonly string $token,
        public readonly array  $params,
    ) {}

    public function handle(): void
    {
        ini_set('memory_limit', '256M');

        $dir = storage_path('app/bast-exports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Clean files older than 2 hours
        foreach (glob($dir . '/*.pdf') ?: [] as $f) {
            if (filemtime($f) < now()->subHours(2)->timestamp) {
                @unlink($f);
            }
        }

        $tglMulai = $this->params['tgl_mulai'];
        $tglAkhir = $this->params['tgl_akhir'];
        $tarif    = (float) str_replace(',', '.', $this->params['tarif'] ?? '46.40');

        if ($this->jenis === 'gabah') {
            $this->generateGabah($tglMulai, $tglAkhir, $tarif);
        } else {
            $this->generateBeras($tglMulai, $tglAkhir, $tarif);
        }
    }

    private function newMpdf(): Mpdf
    {
        $tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mpdf_bast';
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        return new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_top'    => 25,
            'margin_bottom' => 30,
            'margin_left'   => 30,
            'margin_right'  => 30,
            'tempDir'       => $tmpDir,
        ]);
    }

    private function cabangInfo(): ?object
    {
        $group = $this->params['user_group'] ?? null;
        if (!$group) return null;
        return DB::table('ref_cabang')->where('code_cabang', $group)->first();
    }

    private function kanwil(?object $cabang): string
    {
        return trim(preg_replace('/^\d+\s*-\s*KANTOR WILAYAH\s*/i', '', $cabang->parent_company ?? ''));
    }

    private function generateGabah(string $tglMulai, string $tglAkhir, float $tarif): void
    {
        $userGroup = $this->params['user_group'] ?? null;
        $userLevel = $this->params['user_level'] ?? null;

        $query = DB::table('mas_hpkk_gabah as m')
            ->leftJoin('ref_cabang as r', 'm.code_cabang', '=', 'r.code_cabang')
            ->whereBetween('m.tanggal_pelaksanaan', [$tglMulai . ' 00:00:00', $tglAkhir . ' 23:59:59'])
            ->select(
                'm.jumlah_timbangan', 'm.pengirim', 'm.tanggal_pelaksanaan',
                'm.no_order_pembelian', 'm.nomor_hpkk_gabah', 'm.lokasi',
                'm.kadar_air_rata_rata', 'm.kadar_hampa', 'm.butir_hijau',
                'r.name_cabang', 'r.parent_company'
            )
            ->orderBy('m.tanggal_pelaksanaan', 'asc');

        if ($userLevel === 'Inspektor' && $userGroup) {
            $query->where('m.code_cabang', $userGroup);
        }

        $data    = $query->get();
        $cabang  = $this->cabangInfo();
        $totalKg = $data->sum(fn($r) => (float) str_replace(',', '.', $r->jumlah_timbangan ?? 0));

        $html = view('pdf.bast_gabah', [
            'data'             => $data,
            'tgl_mulai'        => $tglMulai,
            'tgl_akhir'        => $tglAkhir,
            'tarif'            => $tarif,
            'total_kg'         => $totalKg,
            'nomor_surat'      => $this->params['nomor_surat']      ?? '',
            'nama_kepala_unit' => $this->params['nama_kepala_unit'] ?? '',
            'nama_pimpinan'    => $this->params['nama_pimpinan']    ?? '',
            'cabang'           => $cabang,
            'kanwil'           => $this->kanwil($cabang),
            'tgl_cetak'        => now()->isoFormat('D MMMM Y'),
            'logo_idsurvey'    => $this->logoBase64('idsurvey.png'),
            'logo_sucofindo'   => $this->logoBase64('logo-sucofindo.png'),
        ])->render();

        $mpdf = $this->newMpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output(storage_path("app/bast-exports/{$this->token}.pdf"), \Mpdf\Output\Destination::FILE);
    }

    private function generateBeras(string $tglMulai, string $tglAkhir, float $tarif): void
    {
        $userGroup = $this->params['user_group'] ?? null;
        $userLevel = $this->params['user_level'] ?? null;

        $query = DB::table('mas_hpkk_beras as m')
            ->leftJoin('ref_cabang as r', 'm.code_cabang', '=', 'r.code_cabang')
            ->whereBetween('m.tanggal_pemeriksaan', [$tglMulai . ' 00:00:00', $tglAkhir . ' 23:59:59'])
            ->select(
                'm.kuantum_beras', 'm.kuantum_gabah_sesuai_mo', 'm.tempat_pemeriksaan',
                'm.tanggal_pemeriksaan', 'm.id_mo', 'm.nomor_hpkk_beras',
                'm.derajat_sosoh', 'm.rata_rata', 'm.butir_patah', 'm.menir',
                'r.name_cabang', 'r.parent_company'
            )
            ->orderBy('m.tanggal_pemeriksaan', 'asc');

        if ($userLevel === 'Inspektor' && $userGroup) {
            $query->where('m.code_cabang', $userGroup);
        }

        $data    = $query->get();
        $cabang  = $this->cabangInfo();
        $totalKg = $data->sum(fn($r) => (float) str_replace(',', '.', $r->kuantum_beras ?? 0));

        $html = view('pdf.bast_beras', [
            'data'             => $data,
            'tgl_mulai'        => $tglMulai,
            'tgl_akhir'        => $tglAkhir,
            'tarif'            => $tarif,
            'total_kg'         => $totalKg,
            'nomor_surat'      => $this->params['nomor_surat']      ?? '',
            'nama_kepala_unit' => $this->params['nama_kepala_unit'] ?? '',
            'nama_pimpinan'    => $this->params['nama_pimpinan']    ?? '',
            'cabang'           => $cabang,
            'kanwil'           => $this->kanwil($cabang),
            'tgl_cetak'        => now()->isoFormat('D MMMM Y'),
            'logo_idsurvey'    => $this->logoBase64('idsurvey.png'),
            'logo_sucofindo'   => $this->logoBase64('logo-sucofindo.png'),
        ])->render();

        $mpdf = $this->newMpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output(storage_path("app/bast-exports/{$this->token}.pdf"), \Mpdf\Output\Destination::FILE);
    }

    public function failed(\Throwable $e): void
    {
        Cache::put("bast_pdf_{$this->token}_failed", $e->getMessage(), 3600);
    }
}
