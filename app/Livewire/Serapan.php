<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use App\Models\MasHpkkBeras;

class Serapan extends Component
{
    public $gabahStats = [];
    public $berasStats = [];

    public function render()
    {
        // === 1. DATA GABAH (Baris Atas) ===
        // Sesuai urutan gambar: Kadar Air, Hampa, Butir Hijau
        $this->gabahStats = [
            'ka'    => $this->getStats(MasHpkkGabah::class, 'kadar_air_rata_rata'),
            'hampa' => $this->getStats(MasHpkkGabah::class, 'hampa_kotoran'), 
            'hijau' => $this->getStats(MasHpkkGabah::class, 'butir_hijau_mengapur'), 
        ];

        // === 2. DATA BERAS (Baris Bawah) ===
        // Sesuai urutan gambar: KA, Sosoh, Patah, Menir, Rendemen
        $this->berasStats = [
            'ka'       => $this->getStats(MasHpkkBeras::class, 'rata_rata'), // Kadar Air Beras
            'sosoh'    => $this->getStats(MasHpkkBeras::class, 'derajat_sosoh'),
            'patah'    => $this->getStats(MasHpkkBeras::class, 'butir_patah'),
            'menir'    => $this->getStats(MasHpkkBeras::class, 'menir'),
            'rendemen' => $this->getStats(MasHpkkBeras::class, 'rendemen_pengolahan'),
        ];

        return view('livewire.serapan');
    }

    // Fungsi Helper (Sudah diperbaiki dengan float casting)
    private function getStats($model, $column)
    {
        try {
            $toFloat = function($val) {
                if ($val === null || $val === '') return 0.00;
                $val = str_replace(',', '.', (string)$val); // Ubah koma jadi titik
                return (float) $val;
            };

            return [
                'min' => $toFloat($model::min($column)),
                'max' => $toFloat($model::max($column)),
                'avg' => $toFloat($model::avg($column)),
            ];
        } catch (\Exception $e) {
            return ['min' => 0.00, 'max' => 0.00, 'avg' => 0.00];
        }
    }
}