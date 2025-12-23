<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use App\Models\MasHpkkBeras;
use Illuminate\Support\Facades\DB;

class Serapan extends Component
{
    // === VARIABLE FILTER ===
    public $cabang = '';
    public $periode = '';
    
    // Data Dropdown
    public $listCabang = [];

    // Data Statistik
    public $gabahStats = [];
    public $berasStats = [];

    public function mount()
    {
        // Default Periode (Bulan Ini)
        $this->periode = date('Y-m'); 

        // Ambil Daftar Cabang dari ref_cabang (Code & Name)
        try {
            $this->listCabang = DB::table('ref_cabang')
                ->whereNotNull('name_cabang') 
                ->orderBy('name_cabang', 'asc')
                ->pluck('name_cabang', 'code_cabang') // Value=Code, Label=Name
                ->toArray();
        } catch (\Exception $e) {
            $this->listCabang = [];
        }
    }

    public function render()
    {
        // === KONFIGURASI FILTER ===
        // Berdasarkan FOTO TABLE: Kedua tabel menggunakan kolom 'group' untuk kode cabang/lokasi
        $colCabangGabah = 'group'; 
        $colCabangBeras = 'group';

        // === 1. HITUNG DATA GABAH (Sesuai Foto mas_hpkk_gabah) ===
        $this->gabahStats = [
            // Kadar Air
            'ka'    => $this->getStats(MasHpkkGabah::class, 'kadar_air_rata_rata', 'tanggal_pelaksanaan', $colCabangGabah),
            // Kadar Hampa (Sesuai kolom di foto: kadar_hampa)
            'hampa' => $this->getStats(MasHpkkGabah::class, 'kadar_hampa', 'tanggal_pelaksanaan', $colCabangGabah), 
            // Butir Hijau (Sesuai kolom di foto: butir_hijau)
            'hijau' => $this->getStats(MasHpkkGabah::class, 'butir_hijau', 'tanggal_pelaksanaan', $colCabangGabah), 
        ];

        // === 2. HITUNG DATA BERAS (Sesuai Foto mas_hpkk_beras) ===
        $this->berasStats = [
            // Kadar Air diambil dari 'rata_rata' (Sesuai Request)
            'ka'       => $this->getStats(MasHpkkBeras::class, 'rata_rata', 'tanggal_pemeriksaan', $colCabangBeras),
            // Derajat Sosoh
            'sosoh'    => $this->getStats(MasHpkkBeras::class, 'derajat_sosoh', 'tanggal_pemeriksaan', $colCabangBeras),
            // Butir Patah
            'patah'    => $this->getStats(MasHpkkBeras::class, 'butir_patah', 'tanggal_pemeriksaan', $colCabangBeras),
            // Menir
            'menir'    => $this->getStats(MasHpkkBeras::class, 'menir', 'tanggal_pemeriksaan', $colCabangBeras),
            // Rendemen
            'rendemen' => $this->getStats(MasHpkkBeras::class, 'rendemen_pengolahan', 'tanggal_pemeriksaan', $colCabangBeras),
        ];

        return view('livewire.serapan');
    }

    /**
     * Helper Statistik (Min, Max, Avg)
     */
    private function getStats($model, $column, $dateColumn, $cabangColumn)
    {
        try {
            $query = $model::query();

            // A. Filter Cabang (Berdasarkan kolom 'group')
            if (!empty($this->cabang)) {
                $query->where($cabangColumn, $this->cabang);
            }

            // B. Filter Periode
            if (!empty($this->periode)) {
                $query->where($dateColumn, 'like', $this->periode . '%');
            }

            // Fungsi ubah string (koma) ke float (titik)
            $toFloat = function($val) {
                if ($val === null || $val === '') return 0.00;
                $val = str_replace(',', '.', (string)$val);
                return (float) $val;
            };

            return [
                'min' => $toFloat($query->min($column)),
                'max' => $toFloat($query->max($column)),
                'avg' => $toFloat($query->avg($column)),
            ];
        } catch (\Exception $e) {
            return ['min' => 0.00, 'max' => 0.00, 'avg' => 0.00];
        }
    }
}