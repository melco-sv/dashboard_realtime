<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RefBastStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BastGabah extends Component
{
    use WithPagination;

    public $tgl_mulai;
    public $tgl_akhir;

    // Modal
    public $showModal = false;
    public $nama_kepala_unit = '';
    public $nama_pimpinan_cabang = '';
    public $tarif = '46.40';
    public $nomor_surat = '';

    // Stats
    public $total_record = 0;
    public $total_kg = 0;

    public function mount()
    {
        $this->tgl_mulai = date('Y-m-01');
        $this->tgl_akhir = date('Y-m-d');

        // Load tarif dari cache (invalidated saat Super Admin ubah tarif)
        $setting = Cache::rememberForever('tarif_bast_gabah', fn () =>
            DB::table('ref_settings')->where('key', 'tarif_bast_gabah')->first()
        );
        if ($setting) {
            $this->tarif = $setting->value;
        }

        $this->nomor_surat = $this->generateNomorSurat();
    }

    public function filter()
    {
        $this->resetPage();
        $this->nomor_surat = $this->generateNomorSurat();
    }

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

        $group = Auth::check() ? (Auth::user()->group ?? '') : '';
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
        $group = Auth::check() ? (Auth::user()->group ?? '') : '';

        // Jika periode ini sudah pernah dicetak, gunakan nomor yang sama
        $existing = DB::table('ref_bast_status')
            ->where('code_cabang', $group)
            ->where('jenis', 'GKP')
            ->where('tgl_mulai', $this->tgl_mulai)
            ->where('tgl_akhir', $this->tgl_akhir)
            ->whereNotNull('nomor_surat')
            ->value('nomor_surat');

        if ($existing) {
            return $existing;
        }

        // Hitung dokumen BAST GKP yang sudah diterbitkan cabang ini bulan ini
        $seq = DB::table('ref_bast_status')
            ->where('code_cabang', $group)
            ->where('jenis', 'GKP')
            ->whereYear('tgl_akhir', $tahun)
            ->whereMonth('tgl_akhir', $bulan)
            ->whereNotNull('nomor_surat')
            ->count();

        return $this->buildNomorSurat($seq + 1);
    }

    private function getBaseQuery()
    {
        $query = DB::table('mas_hpkk_gabah as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->whereBetween('m.tanggal_pelaksanaan', [
                $this->tgl_mulai . ' 00:00:00',
                $this->tgl_akhir . ' 23:59:59',
            ]);

        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $query->where('m.group', Auth::user()->group);
        }

        return $query;
    }

    public function hitungTotal()
    {
        $query = $this->getBaseQuery();
        $this->total_record = $query->count();
        $this->total_kg = (float) $query->sum(
            DB::raw("CAST(REPLACE(jumlah_timbangan, ',', '.') AS DECIMAL(15,2))")
        );
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function cetakPdf()
    {
        $group = Auth::user()->group;

        DB::transaction(function () use ($group) {
            $existing = RefBastStatus::where('code_cabang', $group)
                ->where('jenis', 'GKP')
                ->where('tgl_mulai', $this->tgl_mulai)
                ->where('tgl_akhir', $this->tgl_akhir)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                // Jika sudah ada record, gunakan nomor yang tersimpan (re-print stabil)
                if ($existing->nomor_surat) {
                    $this->nomor_surat = $existing->nomor_surat;
                }
                $existing->nomor_surat = $this->nomor_surat;
                $existing->save();
            } else {
                // Hitung seq di dalam transaksi untuk meminimalisir race condition
                $bulan = date('n', strtotime($this->tgl_akhir));
                $tahun = date('Y', strtotime($this->tgl_akhir));
                $seq = RefBastStatus::where('code_cabang', $group)
                    ->where('jenis', 'GKP')
                    ->whereYear('tgl_akhir', $tahun)
                    ->whereMonth('tgl_akhir', $bulan)
                    ->whereNotNull('nomor_surat')
                    ->lockForUpdate()
                    ->count();

                $this->nomor_surat = $this->buildNomorSurat($seq + 1);

                RefBastStatus::create([
                    'code_cabang' => $group,
                    'jenis'       => 'GKP',
                    'tgl_mulai'   => $this->tgl_mulai,
                    'tgl_akhir'   => $this->tgl_akhir,
                    'nomor_surat' => $this->nomor_surat,
                ]);
            }
        });

        $url = route('bast.gabah.pdf', [
            'tgl_mulai'        => $this->tgl_mulai,
            'tgl_akhir'        => $this->tgl_akhir,
            'nama_kepala_unit' => $this->nama_kepala_unit,
            'nama_pimpinan'    => $this->nama_pimpinan_cabang,
            'tarif'            => $this->tarif,
            'nomor_surat'      => $this->nomor_surat,
        ]);

        $this->dispatch('open-pdf', url: $url);
    }

    public function render()
    {
        $this->hitungTotal();

        $data = $this->getBaseQuery()
            ->select('m.*', 'r.name_cabang', 'r.parent_company')
            ->orderBy('m.tanggal_pelaksanaan', 'asc')
            ->simplePaginate(15);

        return view('livewire.bast-gabah', [
            'dataList' => $data,
        ]);
    }
}
