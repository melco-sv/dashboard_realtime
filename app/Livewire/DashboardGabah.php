<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use App\Models\MasHpkkBeras;
use Illuminate\Support\Facades\DB;

class DashboardGabah extends Component
{
    public $totalGabahKg;
    public $totalBerasKg;
    public $totalGabahKgDisplay;
    public $totalBerasKgDisplay;
    public $totalGabahAnalisa;
    public $totalBerasAnalisa;

    // Data chart disimpan sebagai properti agar bisa di-embed langsung ke HTML
    public $gabahLabels = [];
    public $gabahValues = [];
    public $berasLabels = [];
    public $berasValues = [];

    public function mount()
    {
        $this->loadData();
    }

    public function render()
    {
        // Hanya re-load saat polling (mount() sudah handle load pertama)
        if ($this->gabahLabels === []) {
            $this->loadData();
        }

        $this->dispatch('update-charts', [
            'gabah_labels' => $this->gabahLabels,
            'gabah_values' => $this->gabahValues,
            'beras_labels' => $this->berasLabels,
            'beras_values' => $this->berasValues,
        ]);

        return view('livewire.dashboard-gabah');
    }

    private function loadData()
    {
        // Gabah
        $gabahChart = MasHpkkGabah::select(
            DB::raw("DATE_FORMAT(tanggal_pelaksanaan, '%Y-%m') as bulan"),
            DB::raw("SUM(CAST(REPLACE(jumlah_timbangan, ',', '.') AS DECIMAL(15,2))) as total")
        )
            ->whereNotNull('tanggal_pelaksanaan')
            ->groupBy(DB::raw("DATE_FORMAT(tanggal_pelaksanaan, '%Y-%m')"))
            ->orderBy('bulan', 'asc')
            ->get();

        $this->totalGabahKg       = $gabahChart->sum('total');
        $this->totalGabahAnalisa  = MasHpkkGabah::count();
        $this->totalGabahKgDisplay = number_format($this->totalGabahKg, 2, ',', '.');
        $this->gabahLabels        = $gabahChart->pluck('bulan')->values()->all();
        $this->gabahValues        = $gabahChart->pluck('total')->map(fn($v) => (float)$v)->values()->all();

        // Beras
        $berasChart = MasHpkkBeras::select(
            DB::raw("DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m') as bulan"),
            DB::raw("SUM(CAST(REPLACE(kuantum_beras, ',', '.') AS DECIMAL(15,2))) as total")
        )
            ->whereNotNull('tanggal_pemeriksaan')
            ->groupBy(DB::raw("DATE_FORMAT(tanggal_pemeriksaan, '%Y-%m')"))
            ->orderBy('bulan', 'asc')
            ->get();

        $this->totalBerasKg       = $berasChart->sum('total');
        $this->totalBerasAnalisa  = MasHpkkBeras::count();
        $this->totalBerasKgDisplay = number_format($this->totalBerasKg, 2, ',', '.');
        $this->berasLabels        = $berasChart->pluck('bulan')->values()->all();
        $this->berasValues        = $berasChart->pluck('total')->map(fn($v) => (float)$v)->values()->all();
    }
}
