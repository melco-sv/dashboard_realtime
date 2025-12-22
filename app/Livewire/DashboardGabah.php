<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use App\Models\MasHpkkBeras;
use Illuminate\Support\Facades\DB;

class DashboardGabah extends Component
{
    public $totalGabahKg;
    public $totalGabahAnalisa;
    public $totalBerasKg;
    public $totalBerasAnalisa;

    public function render()
    {
        // 1. DATA GABAH (Total & Grafik)
        $this->totalGabahKg = MasHpkkGabah::sum('jumlah_timbangan');
        $this->totalGabahAnalisa = MasHpkkGabah::count();

        $dataGabah = MasHpkkGabah::select(
                DB::raw("DATE_FORMAT(tanggal_pelaksanaan, '%Y-%m') as bulan"), 
                DB::raw('SUM(jumlah_timbangan) as total')
            )
            ->whereNotNull('tanggal_pelaksanaan')
            ->where('tanggal_pelaksanaan', '!=', '0000-00-00')
            ->groupBy(DB::raw("DATE_FORMAT(tanggal_pelaksanaan, '%Y-%m')"))
            ->orderBy('bulan', 'asc')
            ->get();

        // 2. DATA BERAS (Total & Grafik)
        $this->totalBerasKg = MasHpkkBeras::sum('kuantum_beras');
        $this->totalBerasAnalisa = MasHpkkBeras::count();

        $dataBeras = MasHpkkBeras::select(
                DB::raw("DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m') as bulan"), 
                DB::raw('SUM(kuantum_beras) as total')
            )
            ->whereNotNull('tanggal_pemeriksaan')
            ->where('tanggal_pemeriksaan', '!=', '0000-00-00')
            ->groupBy(DB::raw("DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m')"))
            ->orderBy('bulan', 'asc')
            ->get();

        // 3. Dispatch Data ke JavaScript (Format Array Murni)
        $this->dispatch('update-charts', [
            'gabah_labels' => $dataGabah->pluck('bulan')->values()->all(),
            'gabah_values' => $dataGabah->pluck('total')->map(fn($v) => (float)$v)->values()->all(),
            'beras_labels' => $dataBeras->pluck('bulan')->values()->all(),
            'beras_values' => $dataBeras->pluck('total')->map(fn($v) => (float)$v)->values()->all(),
        ]);

        return view('livewire.dashboard-gabah');
    }
}