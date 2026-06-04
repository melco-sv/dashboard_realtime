<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapHglExport;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanHgl extends Component
{
    use WithPagination;

    // --- FILTER ---
    public $tgl_mulai;
    public $tgl_akhir;
    public $filter_cabang = '';
    public $filter_tempat = ''; // Filter Baru: Tempat Pemeriksaan

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
        $this->resetPage(); // Reset pagination ke halaman 1 saat filter berubah
        $this->hitungTotal();
    }

    // --- 1. BASE QUERY (Logic Pusat Filter) ---
    // Digunakan oleh: Render, Hitung Total, Excel, dan PDF agar data konsisten
    private function getBaseQuery()
    {
        $query = DB::table('mas_hpkk_beras as m')
            ->leftJoin('ref_cabang as r', 'm.code_cabang', '=', 'r.code_cabang')
            ->whereBetween('m.tanggal_pemeriksaan', [
                $this->tgl_mulai . ' 00:00:00',
                $this->tgl_akhir . ' 23:59:59'
            ]);

        // A. Filter Cabang (Security & Admin Choice)
        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $query->where('m.code_cabang', Auth::user()->code_cabang);
        } elseif (!empty($this->filter_cabang)) {
            $query->where('m.code_cabang', $this->filter_cabang);
        }

        // B. Filter Tempat Pemeriksaan (Lokasi)
        if (!empty($this->filter_tempat)) {
            $query->where('m.tempat_pemeriksaan', $this->filter_tempat);
        }

        return $query;
    }

    // --- 2. HITUNG TOTAL (Header Card) ---
    public function hitungTotal()
    {
        $query = $this->getBaseQuery();

        $this->total_record = $query->count();

        // Handling koma pada jumlah kuantum (cth: 10,50 menjadi 10.50)
        $this->total_penerimaan = $query->sum(DB::raw("CAST(REPLACE(kuantum_beras, ',', '.') AS DECIMAL(15,2))"));
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
            ->orderBy('m.tanggal_pemeriksaan', 'asc');

        $data_laporan = $query->simplePaginate(50);

        // B. Siapkan Data untuk Dropdown Filter

        // 1. List Cabang (Khusus Admin)
        $list_cabang = [];
        if (Auth::check() && Auth::user()->level !== 'Inspektor') {
            $list_cabang = DB::table('ref_cabang')->orderBy('name_cabang')->get();
        }

        // 2. List Tempat Pemeriksaan (Distinct berdasarkan tanggal & cabang)
        $queryTempat = DB::table('mas_hpkk_beras')
            ->whereBetween('tanggal_pemeriksaan', [$this->tgl_mulai, $this->tgl_akhir]);

        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $queryTempat->where('code_cabang', Auth::user()->code_cabang);
        } elseif (!empty($this->filter_cabang)) {
            $queryTempat->where('code_cabang', $this->filter_cabang);
        }

        $list_tempat = $queryTempat->select('tempat_pemeriksaan')
            ->distinct()
            ->orderBy('tempat_pemeriksaan')
            ->pluck('tempat_pemeriksaan');

        return view('livewire.laporan-hgl', [
            'data_laporan' => $data_laporan,
            'list_cabang'  => $list_cabang,
            'list_tempat'  => $list_tempat
        ]);
    }

    // --- 4. EXPORT EXCEL ---
    public function downloadExcel()
    {
        // Tentukan Group ID
        $groupId = (Auth::check() && Auth::user()->level == 'Inspektor')
            ? Auth::user()->code_cabang
            : $this->filter_cabang;

        // Pastikan Class RekapHglExport Anda menerima parameter filter di constructor
        return Excel::download(new RekapHglExport(
            $this->tgl_mulai,
            $this->tgl_akhir,
            $groupId,
            $this->filter_tempat // Kirim filter tempat
        ), 'Rekap_HGL.xlsx');
    }

    // --- 5. DOWNLOAD PDF ---
    public function downloadPdf()
    {
        // Gunakan getBaseQuery agar hasil PDF sama persis dengan tabel
        $data = $this->getBaseQuery()
            ->select('m.*', 'r.name_cabang', 'r.parent_company')
            ->orderBy('m.tanggal_pemeriksaan', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan_rekap_hgl', [
            'data'   => $data,
            'start'  => $this->tgl_mulai,
            'end'    => $this->tgl_akhir,
            'lokasi' => $this->filter_tempat
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Laporan_Rekap_HGL.pdf');
    }
}
