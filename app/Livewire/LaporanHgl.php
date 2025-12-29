<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // 1. Tambahkan Import Auth
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapHglExport;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanHgl extends Component
{
    use WithPagination;

    public $tgl_mulai;
    public $tgl_akhir;
    public $total_record = 0;
    public $total_penerimaan = 0;

    public function mount()
    {
        $this->tgl_mulai = date('Y-m-01');
        $this->tgl_akhir = date('Y-m-d');
        $this->hitungTotal();
    }

    public function filter()
    {
        $this->resetPage(); 
        $this->hitungTotal();
    }

    // --- 1. LOGIKA HITUNG TOTAL (TERFILTER) ---
    public function hitungTotal()
    {
        $query = DB::table('mas_hpkk_beras') 
            ->whereBetween('tanggal_pemeriksaan', [
                $this->tgl_mulai . ' 00:00:00', 
                $this->tgl_akhir . ' 23:59:59'
            ]);

        // LOGIKA FILTER: Khusus Inspektor
        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $query->where('group', Auth::user()->group);
        }

        $this->total_record = $query->count();
        
        // Menghitung total penerimaan (handling koma desimal)
        $this->total_penerimaan = $query->sum(
            DB::raw("CAST(REPLACE(kuantum_beras, ',', '.') AS DECIMAL(15,2))")
        );
    }

    // --- 2. LOGIKA RENDER TABEL (TERFILTER) ---
    public function render()
    {
        // Query Dasar
        $query = DB::table('mas_hpkk_beras as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->select(
                'm.*',
                'r.name_cabang',
                'r.parent_company' 
            )
            ->whereBetween('m.tanggal_pemeriksaan', [
                $this->tgl_mulai . ' 00:00:00', 
                $this->tgl_akhir . ' 23:59:59'
            ]);

        // LOGIKA FILTER: Khusus Inspektor
        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $query->where('m.group', Auth::user()->group);
        }

        // Eksekusi Query dengan Pagination
        $data_laporan = $query->orderBy('m.tanggal_pemeriksaan', 'asc')
            ->Simplepaginate(50); 

        return view('livewire.laporan-hgl', [
            'data_laporan' => $data_laporan
        ]);
    }

    public function downloadExcel()
    {
        // Note: Pastikan di dalam file App/Exports/RekapHglExport.php 
        // Anda juga menambahkan logika filter Auth::user()->group agar Excel-nya sesuai.
        return Excel::download(new RekapHglExport($this->tgl_mulai, $this->tgl_akhir), 'Rekap_HGL.xlsx');
    }

    // --- 3. LOGIKA DOWNLOAD PDF (TERFILTER) ---
    public function downloadPdf()
    {
        $query = DB::table('mas_hpkk_beras as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->select('m.*', 'r.name_cabang', 'r.parent_company') 
            ->whereBetween('m.tanggal_pemeriksaan', [
                $this->tgl_mulai . ' 00:00:00', 
                $this->tgl_akhir . ' 23:59:59'
            ]);

        // LOGIKA FILTER: Khusus Inspektor
        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $query->where('m.group', Auth::user()->group);
        }

        $data = $query->orderBy('m.tanggal_pemeriksaan', 'asc')->get();
        
        $pdf = Pdf::loadView('pdf.laporan_rekap_hgl', [
            'data' => $data,
            'start' => $this->tgl_mulai,
            'end' => $this->tgl_akhir
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Laporan_Rekap_HGL.pdf');
    }
}