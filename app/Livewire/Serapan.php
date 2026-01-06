<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use App\Models\MasHpkkBeras;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // TAMBAHAN: Import Auth

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
        // 1. Default Periode (Bulan Ini)
        $this->periode = date('Y-m'); 

        // 2. Ambil User yang sedang Login
        $user = Auth::user();

        // 3. Query Dasar ke ref_cabang
        $query = DB::table('ref_cabang')
            ->whereNotNull('name_cabang')
            ->orderBy('name_cabang', 'asc');

        // === LOGIKA FILTER OTOMATIS BERDASARKAN LEVEL USER ===
        if ($user) {
            
            // A. JIKA USER ADALAH KANTOR WILAYAH (Level: Verification)
            // Di database, nama user Kanwil (contoh: "02001 - KANTOR WILAYAH SUMUT")
            // sama persis dengan isi kolom 'parent_company' di tabel ref_cabang.
            if ($user->level == 'Verification') {
                $query->where('parent_company', $user->nama);
            }
            
            // B. JIKA USER ADALAH KANTOR CABANG (Level: Inspektor)
            // Inspektor hanya boleh melihat datanya sendiri.
            // Di database user, kolom 'group' menyimpan kode cabang (contoh: "1701")
            elseif ($user->level == 'Inspektor') {
                if (!empty($user->group)) {
                    $query->where('code_cabang', $user->group);
                }
            }
            
            // C. JIKA SUPER ADMIN / CLIENT
            // Tidak ada filter tambahan, bisa melihat semua list cabang.
        }

        // 4. Eksekusi Query
        try {
            $this->listCabang = $query->pluck('name_cabang', 'code_cabang')->toArray();
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
            // Kadar Hampa
            'hampa' => $this->getStats(MasHpkkGabah::class, 'kadar_hampa', 'tanggal_pelaksanaan', $colCabangGabah), 
            // Butir Hijau
            'hijau' => $this->getStats(MasHpkkGabah::class, 'butir_hijau', 'tanggal_pelaksanaan', $colCabangGabah), 
        ];

        // === 2. HITUNG DATA BERAS (Sesuai Foto mas_hpkk_beras) ===
        $this->berasStats = [
            // Kadar Air diambil dari 'rata_rata'
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