<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
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
        $setting = Cache::rememberForever('tarif_bast', fn () =>
            DB::table('ref_settings')->where('key', 'tarif_bast')->first()
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

    private function generateNomorSurat(): string
    {
        $bulan = date('n', strtotime($this->tgl_akhir));
        $romanMonths = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $roman = $romanMonths[$bulan];
        $tahun = date('Y', strtotime($this->tgl_akhir));

        $cabang = '';
        if (Auth::check()) {
            $ref = DB::table('ref_cabang')->where('code_cabang', Auth::user()->group)->first();
            $cabang = $ref ? strtoupper(substr(str_replace(' ', '', $ref->name_cabang), 0, 3)) : 'XXX';
        }

        $count = $this->getBaseQuery()->count();

        return "{$count}/MDN-{$roman}/{$cabang}/{$tahun}";
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
