<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapTarifExport;
use App\Exports\RekapAnalisaExport;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanGkp extends Component
{
    use WithPagination;

    // --- FILTER ---
    public $tgl_mulai;
    public $tgl_akhir;
    public $filter_cabang = '';
    public $filter_tempat = ''; // Filter Baru: Pelaksanaan Pengolahan
    public $filter_mitra = '';  // Filter Baru: Mitra

    // --- STATISTIK ---
    public $total_record = 0;
    public $total_penerimaan = 0;

    public function mount()
    {
        $this->tgl_mulai = date('Y-m-01');
        $this->tgl_akhir = date('Y-m-d');

        // Security: Jika User adalah Inspektor, kunci filter cabang ke grupnya sendiri
        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $this->filter_cabang = Auth::user()->code_cabang;
        }

        $this->hitungTotal();
    }

    public function filter()
    {
        $this->resetPage(); // Kembali ke halaman 1 saat filter berubah
        $this->hitungTotal();
    }

    // --- 1. BASE QUERY (Logic Pusat Filter) ---
    // Digunakan oleh: Render, Hitung Total, Excel, dan PDF agar data konsisten
    private function getBaseQuery()
    {
        $query = DB::table('mas_hpkk_gabah as m')
            ->leftJoin('ref_cabang as r', 'm.code_cabang', '=', 'r.code_cabang')
            ->whereBetween('m.tanggal_pelaksanaan', [
                $this->tgl_mulai . ' 00:00:00',
                $this->tgl_akhir . ' 23:59:59'
            ]);

        // A. Filter Cabang (Security & Admin Choice)
        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $query->where('m.code_cabang', Auth::user()->code_cabang);
        } elseif (!empty($this->filter_cabang)) {
            $query->where('m.code_cabang', $this->filter_cabang);
        }

        // B. Filter Tempat Pelaksanaan (Lokasi)
        if (!empty($this->filter_tempat)) {
            $query->where('m.lokasi', $this->filter_tempat);
        }

        // C. Filter Mitra
        if (!empty($this->filter_mitra)) {
            $query->where('m.mitra', $this->filter_mitra);
        }

        return $query;
    }

    // --- 2. HITUNG TOTAL (Header Card) ---
    public function hitungTotal()
    {
        $query = $this->getBaseQuery();

        $this->total_record = $query->count();

        // Handling koma pada jumlah timbangan (cth: 10,50 menjadi 10.50)
        $this->total_penerimaan = $query->sum(DB::raw("CAST(REPLACE(jumlah_timbangan, ',', '.') AS DECIMAL(15,2))"));
    }

    // --- 3. RENDER VIEW & DATA ---
    public function render()
    {
        // A. Ambil Data Utama (Tabel)
        $query = $this->getBaseQuery()
            ->select(
                'm.*',
                'r.name_cabang',
                'r.parent_company'
            )
            ->orderBy('m.tanggal_pelaksanaan', 'asc');

        $data_laporan = $query->simplePaginate(50);

        // B. Siapkan Data untuk Dropdown Filter
        // Kita query ulang (distinct) agar dropdown hanya memunculkan data yang ADA pada tanggal tersebut

        // 1. List Cabang (Khusus Admin)
        $list_cabang = [];
        if (Auth::check() && Auth::user()->level !== 'Inspektor') {
            $list_cabang = DB::table('ref_cabang')->orderBy('name_cabang')->get();
        }

        // Base Query untuk filter dropdown (Hanya tanggal & cabang)
        $dropdownQuery = DB::table('mas_hpkk_gabah')
            ->whereBetween('tanggal_pelaksanaan', [$this->tgl_mulai, $this->tgl_akhir]);

        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $dropdownQuery->where('code_cabang', Auth::user()->code_cabang);
        } elseif (!empty($this->filter_cabang)) {
            $dropdownQuery->where('code_cabang', $this->filter_cabang);
        }

        // 2. List Tempat (Lokasi)
        $list_tempat = (clone $dropdownQuery)
            ->select('lokasi')
            ->distinct()
            ->orderBy('lokasi')
            ->pluck('lokasi');

        // 3. List Mitra (Bisa difilter lebih lanjut berdasarkan lokasi yg dipilih)
        $list_mitra = (clone $dropdownQuery)
            ->when(!empty($this->filter_tempat), function ($q) {
                $q->where('lokasi', $this->filter_tempat);
            })
            ->select('mitra')
            ->distinct()
            ->orderBy('mitra')
            ->pluck('mitra');

        return view('livewire.laporan-gkp', [
            'data_laporan' => $data_laporan,
            'list_cabang'  => $list_cabang,
            'list_tempat'  => $list_tempat,
            'list_mitra'   => $list_mitra
        ]);
    }

    // --- 4. EXPORT EXCEL ---
    public function downloadExcelTarif()
    {
        // Tentukan Group ID
        $groupId = (Auth::check() && Auth::user()->level == 'Inspektor')
            ? Auth::user()->code_cabang
            : $this->filter_cabang;

        // Pastikan Class Export Anda menerima parameter filter tambahan di __construct
        return Excel::download(new RekapTarifExport(
            $this->tgl_mulai,
            $this->tgl_akhir,
            $groupId,
            $this->filter_tempat, // Kirim filter tempat
            $this->filter_mitra   // Kirim filter mitra
        ), 'Rekap_Pemeriksaan_GKP.xlsx');
    }

    public function downloadExcelAnalisa()
    {
        $groupId = (Auth::check() && Auth::user()->level == 'Inspektor')
            ? Auth::user()->code_cabang
            : $this->filter_cabang;

        return Excel::download(new RekapAnalisaExport(
            $this->tgl_mulai,
            $this->tgl_akhir,
            $groupId,
            $this->filter_tempat,
            $this->filter_mitra
        ), 'Rekap_Analisa_GKP.xlsx');
    }

    // --- 5. DOWNLOAD PDF ---
    public function downloadPdf()
    {
        // Gunakan getBaseQuery agar hasil PDF sama persis dengan tabel di layar
        $data = $this->getBaseQuery()
            ->select('m.*', 'r.name_cabang', 'r.parent_company')
            ->orderBy('m.tanggal_pelaksanaan', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan_rekap_gkp', [
            'data'   => $data,
            'start'  => $this->tgl_mulai,
            'end'    => $this->tgl_akhir,
            'lokasi' => $this->filter_tempat,
            'mitra'  => $this->filter_mitra
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Laporan_Rekap_GKP.pdf');
    }
}
