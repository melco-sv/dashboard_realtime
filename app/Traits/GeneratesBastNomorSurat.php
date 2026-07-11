<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Logika penomoran surat BAST yang dipakai bersama oleh BastGabah (GKP)
 * dan BastBeras (HGL). Kelas pemakai wajib mendefinisikan konstanta
 * BAST_JENIS ('GKP' / 'HGL') serta properti $tgl_mulai dan $tgl_akhir.
 */
trait GeneratesBastNomorSurat
{
    private static array $cabangAbbr = [
        '1701' => 'MDN', '1705' => 'ASH', '1706' => 'PDS', '1707' => 'PST',
        '1704' => 'LHK', '1708' => 'ACH', '1709' => 'LGS', '1710' => 'MLB',
        '1711' => 'SGL', '1712' => 'BGP', '1713' => 'KTC', '1714' => 'TKG',
        '2201' => 'BKT', '2202' => 'PDG', '2203' => 'SLK',
        '2301' => 'DUM', '2303' => 'BKL',
        '2401' => 'JMB', '2402' => 'KTL', '2403' => 'SGP',
        '2001' => 'PLB', '2005' => 'BKA', '2006' => 'OKU', '2007' => 'LLG', '2008' => 'LHT',
        '2501' => 'BGL', '2502' => 'RJL',
        '2101' => 'LPG', '2102' => 'LPS', '2103' => 'LPU', '2104' => 'MTR', '2105' => 'TBB',
        '3601' => 'JKT', '3703' => 'LBK', '3704' => 'SRG',
        '3902' => 'KRW', '3903' => 'BGR', '4001' => 'CRB', '4002' => 'IDR', '4003' => 'TGL',
        '4101' => 'BDG', '4103' => 'CMS', '4104' => 'CJR', '4105' => 'SBG',
        '4201' => 'SMR', '4202' => 'PTI', '4203' => 'SKT', '4204' => 'YGY', '4205' => 'MGL', '4301' => 'CLC',
        '7101' => 'SBY', '7104' => 'BYW', '7105' => 'BJN', '7106' => 'BDS', '7107' => 'JBR',
        '7108' => 'KDR', '7109' => 'MDI', '7110' => 'MLG', '7111' => 'MJK',
        '7112' => 'PRG', '7113' => 'PBL', '7114' => 'TLA',
        '7501' => 'BLI',
        '7503' => 'BMA', '7504' => 'LBT', '7505' => 'NTB', '7506' => 'SMW',
        '6001' => 'SKW', '6002' => 'MMP', '6003' => 'PTK',
        '5706' => 'KWT', '5708' => 'KTG', '5709' => 'KPS',
        '5701' => 'BJM', '5707' => 'HST', '6201' => 'KBR',
        '5601' => 'PSR', '5801' => 'SMD', '5803' => 'BPP',
        '6301' => 'TRK', '6302' => 'BRU', '6303' => 'BLG',
        '7302' => 'KND', '7308' => 'KLK', '7309' => 'UNH',
        '7301' => 'MKS', '7311' => 'PLP', '7312' => 'PRP', '7313' => 'PNR', '7314' => 'SDR',
        '7315' => 'SPG', '7316' => 'WJO', '7317' => 'BON', '7318' => 'BLK', '7319' => 'MMJ', '7320' => 'PLM',
    ];

    private function buildNomorSurat(int $seq): string
    {
        $bulan = date('n', strtotime($this->tgl_akhir));
        $romanMonths = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $roman = $romanMonths[$bulan];
        $tahun = date('Y', strtotime($this->tgl_akhir));

        $group = Auth::check() ? (Auth::user()->code_cabang ?? '') : '';
        if (isset(self::$cabangAbbr[$group])) {
            $abbr = self::$cabangAbbr[$group];
        } else {
            $ref  = DB::table('ref_cabang')->where('code_cabang', $group)->first();
            $abbr = $ref ? strtoupper(substr(str_replace(' ', '', $ref->name_cabang), 0, 3)) : 'XXX';
        }

        return "{$seq}/{$abbr}-{$roman}/{$tahun}";
    }

    private function generateNomorSurat(): string
    {
        $bulan = date('n', strtotime($this->tgl_akhir));
        $tahun = date('Y', strtotime($this->tgl_akhir));
        $group = Auth::check() ? (Auth::user()->code_cabang ?? '') : '';

        // Jika periode ini sudah pernah dicetak, gunakan nomor yang sama
        $existing = DB::table('ref_bast_status')
            ->where('code_cabang', $group)
            ->where('jenis', static::BAST_JENIS)
            ->where('tgl_mulai', $this->tgl_mulai)
            ->where('tgl_akhir', $this->tgl_akhir)
            ->whereNotNull('nomor_surat')
            ->value('nomor_surat');

        if ($existing) {
            return $existing;
        }

        // Hitung dokumen BAST yang sudah diterbitkan cabang ini bulan ini
        $seq = DB::table('ref_bast_status')
            ->where('code_cabang', $group)
            ->where('jenis', static::BAST_JENIS)
            ->whereYear('tgl_akhir', $tahun)
            ->whereMonth('tgl_akhir', $bulan)
            ->whereNotNull('nomor_surat')
            ->count();

        return $this->buildNomorSurat($seq + 1);
    }
}
