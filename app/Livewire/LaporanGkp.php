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

    public $tgl_mulai;
    public $tgl_akhir;
    
    // Properti Filter Tambahan (Opsional: Jika Admin ingin filter cabang tertentu)
    public $filter_cabang = ''; 

    public $total_record = 0;
    public $total_penerimaan = 0;

    public function mount()
    {
        $this->tgl_mulai = date('Y-m-01');
        $this->tgl_akhir = date('Y-m-d');
        
        // Jika User adalah Inspektor, set default filter cabang ke grupnya sendiri
        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $this->filter_cabang = Auth::user()->group;
        }

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
        $query = DB::table('mas_hpkk_gabah')
            ->whereBetween('tanggal_pelaksanaan', [
                $this->tgl_mulai . ' 00:00:00', 
                $this->tgl_akhir . ' 23:59:59'
            ]);

        // LOGIKA FILTER SECURITY (Inspektor hanya lihat datanya sendiri)
        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $query->where('group', Auth::user()->group);
        } 
        // LOGIKA FILTER ADMIN (Jika Admin memilih cabang tertentu dari dropdown)
        elseif (!empty($this->filter_cabang)) {
            $query->where('group', $this->filter_cabang);
        }

        $this->total_record = $query->count();
        
        // Handling koma pada jumlah timbangan
        $this->total_penerimaan = $query->sum(DB::raw("CAST(REPLACE(jumlah_timbangan, ',', '.') AS DECIMAL(15,2))"));
    }

    // --- 2. LOGIKA RENDER TABEL (TERFILTER) ---
    public function render()
    {
        $query = DB::table('mas_hpkk_gabah as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->select(
                'm.*', 
                'r.name_cabang', 
                'r.parent_company' 
            )
            ->whereBetween('m.tanggal_pelaksanaan', [
                $this->tgl_mulai . ' 00:00:00', 
                $this->tgl_akhir . ' 23:59:59'
            ]);

        // Filter Security (Inspektor)
        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $query->where('m.group', Auth::user()->group);
        } 
        // Filter Pilihan Admin
        elseif (!empty($this->filter_cabang)) {
            $query->where('m.group', $this->filter_cabang);
        }

        $data_laporan = $query->orderBy('m.tanggal_pelaksanaan', 'asc')
            ->Simplepaginate(50);
            
        // Ambil daftar cabang untuk Dropdown Filter (Khusus Admin)
        $list_cabang = [];
        if (Auth::check() && Auth::user()->level !== 'Inspektor') {
            $list_cabang = DB::table('ref_cabang')->orderBy('name_cabang')->get();
        }

        return view('livewire.laporan-gkp', [
            'data_laporan' => $data_laporan,
            'list_cabang' => $list_cabang // Kirim ke view untuk dropdown
        ]);
    }

    // --- 3. EXPORT EXCEL (PERBAIKAN SECURITY) ---
    public function downloadExcelTarif()
    {
        // Tentukan Group ID yang akan difilter
        // Jika Inspektor: Pakai Auth Group
        // Jika Admin: Pakai filter_cabang (bisa null jika pilih semua)
        $groupId = (Auth::check() && Auth::user()->level == 'Inspektor') 
                    ? Auth::user()->group 
                    : $this->filter_cabang;

        return Excel::download(new RekapTarifExport($this->tgl_mulai, $this->tgl_akhir, $groupId), 'Rekap_Pemeriksaan_GKP.xlsx');
    }

    public function downloadExcelAnalisa()
    {
        $groupId = (Auth::check() && Auth::user()->level == 'Inspektor') 
                    ? Auth::user()->group 
                    : $this->filter_cabang;

        return Excel::download(new RekapAnalisaExport($this->tgl_mulai, $this->tgl_akhir, $groupId), 'Rekap_Analisa_GKP.xlsx');
    }

    // --- 4. DOWNLOAD PDF (TERFILTER) ---
    public function downloadPdf()
    {
        $query = DB::table('mas_hpkk_gabah as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->select('m.*', 'r.name_cabang', 'r.parent_company') 
            ->whereBetween('m.tanggal_pelaksanaan', [
                $this->tgl_mulai . ' 00:00:00', 
                $this->tgl_akhir . ' 23:59:59'
            ]);

        // Filter Security & Admin Choice
        if (Auth::check() && Auth::user()->level == 'Inspektor') {
            $query->where('m.group', Auth::user()->group);
        } elseif (!empty($this->filter_cabang)) {
            $query->where('m.group', $this->filter_cabang);
        }

        $data = $query->orderBy('m.tanggal_pelaksanaan', 'asc')->get();
        
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