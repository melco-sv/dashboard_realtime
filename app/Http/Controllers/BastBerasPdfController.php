<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\PdfLogoHelper;

class BastBerasPdfController extends Controller
{
    use PdfLogoHelper;
    public function print(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $tglMulai       = $request->get('tgl_mulai', date('Y-m-01'));
        $tglAkhir       = $request->get('tgl_akhir', date('Y-m-d'));
        $namaKepalaUnit = $request->get('nama_kepala_unit', '');
        $namaPimpinan   = $request->get('nama_pimpinan', '');
        $tarif          = (float) str_replace(',', '.', $request->get('tarif', '46.40'));
        $nomorSurat     = $request->get('nomor_surat', '');

        $query = DB::table('mas_hpkk_beras as m')
            ->leftJoin('ref_cabang as r', 'm.code_cabang', '=', 'r.code_cabang')
            ->whereBetween('m.tanggal_pemeriksaan', [
                $tglMulai . ' 00:00:00',
                $tglAkhir . ' 23:59:59',
            ])
            ->select(
                'm.kuantum_beras', 'm.kuantum_gabah_sesuai_mo', 'm.tempat_pemeriksaan',
                'm.tanggal_pemeriksaan', 'm.id_mo', 'm.nomor_hpkk_beras',
                'm.derajat_sosoh', 'm.rata_rata', 'm.butir_patah', 'm.menir',
                'r.name_cabang', 'r.parent_company'
            )
            ->orderBy('m.tanggal_pemeriksaan', 'asc');

        // Inspektor hanya boleh akses data cabangnya sendiri
        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $query->where('m.code_cabang', Auth::user()->code_cabang);
        }

        $data = $query->get();

        // Info cabang
        $cabang = DB::table('ref_cabang')
            ->where('code_cabang', Auth::user()->code_cabang ?? '')
            ->first();

        $totalKg = $data->sum(function ($row) {
            return (float) str_replace(',', '.', $row->kuantum_beras ?? 0);
        });

        $kanwil = trim(preg_replace('/^\d+\s*-\s*KANTOR WILAYAH\s*/i', '', $cabang->parent_company ?? ''));

        $pdf = Pdf::setOptions([
            'isHtml5ParserEnabled' => false,
            'isRemoteEnabled'      => false,
            'defaultFont'          => 'sans-serif',
            'dpi'                  => 96,
        ])->loadView('pdf.bast_beras', [
            'data'             => $data,
            'tgl_mulai'        => $tglMulai,
            'tgl_akhir'        => $tglAkhir,
            'tarif'            => $tarif,
            'total_kg'         => $totalKg,
            'nomor_surat'      => $nomorSurat,
            'nama_kepala_unit' => $namaKepalaUnit,
            'nama_pimpinan'    => $namaPimpinan,
            'cabang'           => $cabang,
            'kanwil'           => $kanwil,
            'tgl_cetak'        => now()->isoFormat('D MMMM Y'),
            'logo_idsurvey'    => $this->logoBase64('idsurvey.png'),
            'logo_sucofindo'   => $this->logoBase64('logo-sucofindo.png'),
        ]);

        $periode = date('m-Y', strtotime($tglAkhir));
        return $pdf->stream("BAST-HGL-{$periode}.pdf");
    }

}
