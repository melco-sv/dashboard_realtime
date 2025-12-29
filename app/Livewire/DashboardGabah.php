<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use App\Models\MasHpkkBeras;
use Illuminate\Support\Facades\DB;

class DashboardGabah extends Component
{
    // Properti Data Mentah (untuk Chart)
    public $totalGabahKg;
    public $totalBerasKg;
    
    // Properti Tampilan (Sudah diformat Rp/Kg)
    public $totalGabahKgDisplay;
    public $totalBerasKgDisplay;
    
    public $totalGabahAnalisa;
    public $totalBerasAnalisa;

    public function render()
    {
        // === 1. DATA GABAH ===
        // Hitung Total Berat (Handle koma desimal agar akurat)
        $this->totalGabahKg = MasHpkkGabah::sum(DB::raw("CAST(REPLACE(jumlah_timbangan, ',', '.') AS DECIMAL(15,2))"));
        $this->totalGabahAnalisa = MasHpkkGabah::count();

        // Format Tampilan (Contoh: 12.500,00)
        $this->totalGabahKgDisplay = number_format($this->totalGabahKg, 2, ',', '.');

        // Data Grafik Gabah (Group by Bulan)
        $dataGabah = MasHpkkGabah::select(
                DB::raw("DATE_FORMAT(tanggal_pelaksanaan, '%Y-%m') as bulan"), 
                DB::raw("SUM(CAST(REPLACE(jumlah_timbangan, ',', '.') AS DECIMAL(15,2))) as total")
            )
            ->whereNotNull('tanggal_pelaksanaan')
            ->groupBy(DB::raw("DATE_FORMAT(tanggal_pelaksanaan, '%Y-%m')"))
            ->orderBy('bulan', 'asc')
            ->get();


        // === 2. DATA BERAS ===
        // Hitung Total Berat
        $this->totalBerasKg = MasHpkkBeras::sum(DB::raw("CAST(REPLACE(kuantum_beras, ',', '.') AS DECIMAL(15,2))"));
        $this->totalBerasAnalisa = MasHpkkBeras::count();

        // Format Tampilan
        $this->totalBerasKgDisplay = number_format($this->totalBerasKg, 2, ',', '.');

        // Data Grafik Beras (Group by Bulan)
        $dataBeras = MasHpkkBeras::select(
                DB::raw("DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m') as bulan"), 
                DB::raw("SUM(CAST(REPLACE(kuantum_beras, ',', '.') AS DECIMAL(15,2))) as total")
            )
            ->whereNotNull('tanggal_pemeriksaan')
            ->groupBy(DB::raw("DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m')"))
            ->orderBy('bulan', 'asc')
            ->get();


        // === 3. DISPATCH KE CHART JS ===
        // Mengirim data bersih ke JavaScript
        $this->dispatch('update-charts', [
            'gabah_labels' => $dataGabah->pluck('bulan')->values()->all(),
            'gabah_values' => $dataGabah->pluck('total')->map(fn($v) => (float)$v)->values()->all(),
            'beras_labels' => $dataBeras->pluck('bulan')->values()->all(),
            'beras_values' => $dataBeras->pluck('total')->map(fn($v) => (float)$v)->values()->all(),
        ]);

        return view('livewire.dashboard-gabah');
    }
}