<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination; // 1. Import Pagination
use Illuminate\Support\Facades\DB; 
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapTarifExport;
use App\Exports\RekapAnalisaExport;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanGkp extends Component
{
    use WithPagination; // 2. Gunakan Trait Pagination

    public $tgl_mulai;
    public $tgl_akhir;
    // public $data_laporan = []; // <--- HAPUS INI (Biar tidak berat)
    
    public $total_record = 0;
    public $total_penerimaan = 0;

    public function mount()
    {
        $this->tgl_mulai = date('Y-m-01');
        $this->tgl_akhir = date('Y-m-d');
        $this->hitungTotal(); // Hitung total saat pertama buka
    }

    // Fungsi ini dipanggil saat tombol "Tampilkan" diklik
    public function filter()
    {
        $this->resetPage(); // Reset ke halaman 1 setiap kali filter berubah
        $this->hitungTotal();
    }

    // Pisahkan logika menghitung total agar ringan
    public function hitungTotal()
    {
        $query = DB::table('mas_hpkk_gabah')
            ->whereBetween('tanggal_pelaksanaan', [
                $this->tgl_mulai . ' 00:00:00', 
                $this->tgl_akhir . ' 23:59:59'
            ]);

        $this->total_record = $query->count();
        
        // Hitung total penerimaan dengan aman
        // Kita ambil raw data timbangan lalu proses di PHP (chunking) agar hemat memori jika data jutaan
        // Atau gunakan query sum biasa jika yakin datanya sudah bersih. 
        // Karena data Anda string berkoma, kita pakai cara aman query raw:
        // Mencoba replace koma di SQL level (MySQL)
        $this->total_penerimaan = $query->sum(DB::raw("CAST(REPLACE(jumlah_timbangan, ',', '.') AS DECIMAL(15,2))"));
    }

    public function render()
    {
        // Query Utama untuk Tabel (Pakai Pagination)
        $data_laporan = DB::table('mas_hpkk_gabah as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->select(
                'm.*', 
                'r.name_cabang', 
                'r.parent_company' 
            )
            ->whereBetween('m.tanggal_pelaksanaan', [
                $this->tgl_mulai . ' 00:00:00', 
                $this->tgl_akhir . ' 23:59:59'
            ])
            ->orderBy('m.tanggal_pelaksanaan', 'asc')
            ->paginate(50); // <--- TAMPILKAN 50 DATA PER HALAMAN

        return view('livewire.laporan-gkp', [
            'data_laporan' => $data_laporan
        ]);
    }

    public function downloadExcelTarif()
    {
        return Excel::download(new RekapTarifExport($this->tgl_mulai, $this->tgl_akhir), 'Rekap_Pemeriksaan.xlsx');
    }

    public function downloadExcelAnalisa()
    {
        return Excel::download(new RekapAnalisaExport($this->tgl_mulai, $this->tgl_akhir), 'Rekap_Analisa.xlsx');
    }

    public function downloadPdf()
    {
        $data = DB::table('mas_hpkk_gabah as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->select('m.*', 'r.name_cabang', 'r.parent_company') 
            ->whereBetween('m.tanggal_pelaksanaan', [
                $this->tgl_mulai . ' 00:00:00', 
                $this->tgl_akhir . ' 23:59:59'
            ])
            ->orderBy('m.tanggal_pelaksanaan', 'asc')
            ->get();
        
        $pdf = Pdf::loadView('pdf.laporan_rekap_gkp', [
            'data' => $data,
            'start' => $this->tgl_mulai,
            'end' => $this->tgl_akhir
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Laporan_Rekap_GKP.pdf');
    }
}