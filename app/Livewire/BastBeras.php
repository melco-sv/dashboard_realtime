<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RefBastStatus;
use App\Jobs\GenerateBastPdf;
use App\Traits\GeneratesBastNomorSurat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BastBeras extends Component
{
    use WithPagination, GeneratesBastNomorSurat;

    private const BAST_JENIS = 'HGL';

    public $tgl_mulai;
    public $tgl_akhir;

    public $nama_kepala_unit = '';
    public $nama_pimpinan_cabang = '';
    public $tarif = '46.40';
    public $nomor_surat = '';

    // Stats
    public $total_record = 0;
    public $total_kg = 0;

    // PDF queue
    public $pdfToken = null;

    public function mount()
    {
        $this->tgl_mulai = date('Y-m-01');
        $this->tgl_akhir = date('Y-m-d');

        // Load tarif dari cache (invalidated saat Super Admin ubah tarif)
        $setting = Cache::rememberForever('tarif_bast_beras', fn () =>
            DB::table('ref_settings')->where('key', 'tarif_bast_beras')->first()
        );
        if ($setting) {
            $this->tarif = $setting->value;
        }

        // Pre-fill nama pejabat dari Pengaturan Pejabat BAST (tersimpan per cabang)
        $group = Auth::user()->code_cabang ?? '';
        $kepala = DB::table('ref_settings')->where('key', "bast_kepala_unit_{$group}")->value('value');
        if ($kepala) $this->nama_kepala_unit = $kepala;
        $pimpinan = DB::table('ref_settings')->where('key', "bast_pimpinan_cabang_{$group}")->value('value');
        if ($pimpinan) $this->nama_pimpinan_cabang = $pimpinan;

        $this->nomor_surat = $this->generateNomorSurat();
    }

    public function filter()
    {
        $this->resetPage();
        $this->nomor_surat = $this->generateNomorSurat();
    }

    private function getBaseQuery()
    {
        $query = DB::table('mas_hpkk_beras as m')
            ->leftJoin('ref_cabang as r', 'm.code_cabang', '=', 'r.code_cabang')
            ->whereBetween('m.tanggal_pemeriksaan', [
                $this->tgl_mulai . ' 00:00:00',
                $this->tgl_akhir . ' 23:59:59',
            ]);

        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $query->where('m.code_cabang', Auth::user()->code_cabang);
        }

        return $query;
    }

    public function hitungTotal()
    {
        $query = $this->getBaseQuery();
        $this->total_record = $query->count();
        $this->total_kg = (float) $query->sum(
            DB::raw("CAST(REPLACE(kuantum_beras, ',', '.') AS DECIMAL(15,2))")
        );
    }

    public function cetakPdf()
    {
        // Nama pejabat diambil dari Pengaturan Pejabat BAST (per cabang).
        // Cegah cetak BAST tanpa nama penanda tangan — arahkan user ke halaman pengaturan.
        if (trim((string) $this->nama_kepala_unit) === '' || trim((string) $this->nama_pimpinan_cabang) === '') {
            $this->addError('pdf', 'Nama pejabat belum diatur untuk cabang Anda. Silakan isi dulu di menu BAST → Pengaturan Pejabat.');
            return;
        }

        $group = Auth::user()->code_cabang;

        DB::transaction(function () use ($group) {
            $existing = RefBastStatus::where('code_cabang', $group)
                ->where('jenis', 'HGL')
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
                    ->where('jenis', 'HGL')
                    ->whereYear('tgl_akhir', $tahun)
                    ->whereMonth('tgl_akhir', $bulan)
                    ->whereNotNull('nomor_surat')
                    ->lockForUpdate()
                    ->count();

                $this->nomor_surat = $this->buildNomorSurat($seq + 1);

                RefBastStatus::create([
                    'code_cabang' => $group,
                    'jenis'       => 'HGL',
                    'tgl_mulai'   => $this->tgl_mulai,
                    'tgl_akhir'   => $this->tgl_akhir,
                    'nomor_surat' => $this->nomor_surat,
                ]);
            }
        });

        $token = Str::uuid()->toString();
        $this->pdfToken = $token;

        GenerateBastPdf::dispatch('beras', $token, [
            'tgl_mulai'        => $this->tgl_mulai,
            'tgl_akhir'        => $this->tgl_akhir,
            'nomor_surat'      => $this->nomor_surat,
            'nama_kepala_unit' => $this->nama_kepala_unit,
            'nama_pimpinan'    => $this->nama_pimpinan_cabang,
            'tarif'            => $this->tarif,
            'user_group'       => Auth::user()->code_cabang ?? null,
            'user_level'       => Auth::user()->level ?? null,
        ]);
    }

    public function checkPdfReady(): void
    {
        if (!$this->pdfToken) return;

        if (Cache::has("bast_pdf_{$this->pdfToken}_failed")) {
            $this->addError('pdf', 'Gagal membuat PDF. Silakan coba lagi.');
            $this->pdfToken = null;
            return;
        }

        if (file_exists(storage_path("app/bast-exports/{$this->pdfToken}.pdf"))) {
            $this->dispatch('open-pdf', url: route('bast.download', $this->pdfToken));
            $this->pdfToken = null;
        }
    }

    public function render()
    {
        $this->hitungTotal();

        $data = $this->getBaseQuery()
            ->select('m.*', 'r.name_cabang', 'r.parent_company')
            ->orderBy('m.tanggal_pemeriksaan', 'asc')
            ->simplePaginate(15);

        return view('livewire.bast-beras', [
            'dataList' => $data,
        ]);
    }
}
