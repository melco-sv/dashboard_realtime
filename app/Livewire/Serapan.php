<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use App\Models\MasHpkkBeras;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
    
    // Data Khusus Superadmin (Analisis Lanjutan)
    public $advancedStats = null; 

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

        // === LOGIKA FILTER OTOMATIS BERDASARKAN LEVEL DB ===
        if ($user) {
            // Sesuai data mas_user.sql: 'Verification'
            if ($user->level == 'Verification') {
                $query->where('parent_company', $user->nama);
            }
            // Sesuai data mas_user.sql: 'Inspektor'
            elseif ($user->level == 'Inspektor') {
                if (!empty($user->group)) {
                    $query->where('code_cabang', $user->group);
                }
            }
        }

        try {
            $this->listCabang = $query->pluck('name_cabang', 'code_cabang')->toArray();
        } catch (\Exception $e) {
            $this->listCabang = [];
        }
    }

    public function render()
    {
        // Filter Columns
        $colCabangGabah = 'group'; 
        $colCabangBeras = 'group';

        // === 1. HITUNG DATA DASAR GABAH ===
        $this->gabahStats = [
            'ka'    => $this->getStats(MasHpkkGabah::class, 'kadar_air_rata_rata', 'tanggal_pelaksanaan', $colCabangGabah),
            'hampa' => $this->getStats(MasHpkkGabah::class, 'kadar_hampa', 'tanggal_pelaksanaan', $colCabangGabah), 
            'hijau' => $this->getStats(MasHpkkGabah::class, 'butir_hijau', 'tanggal_pelaksanaan', $colCabangGabah), 
        ];

        // === 2. HITUNG DATA DASAR BERAS ===
        $this->berasStats = [
            'ka'       => $this->getStats(MasHpkkBeras::class, 'rata_rata', 'tanggal_pemeriksaan', $colCabangBeras),
            'sosoh'    => $this->getStats(MasHpkkBeras::class, 'derajat_sosoh', 'tanggal_pemeriksaan', $colCabangBeras),
            'patah'    => $this->getStats(MasHpkkBeras::class, 'butir_patah', 'tanggal_pemeriksaan', $colCabangBeras),
            'menir'    => $this->getStats(MasHpkkBeras::class, 'menir', 'tanggal_pemeriksaan', $colCabangBeras),
            'rendemen' => $this->getStats(MasHpkkBeras::class, 'rendemen_pengolahan', 'tanggal_pemeriksaan', $colCabangBeras),
        ];

        // === 3. HITUNG STATISTIKA LANJUTAN (HANYA SUPER ADMIN) ===
        // PERBAIKAN: Menggunakan 'Super Admin' (pakai spasi) sesuai database
        // Menggunakan strtolower agar lebih aman (super admin / Super Admin)
        if (Auth::check() && strtolower(trim(Auth::user()->level)) === 'super admin') {
            $this->calculateAdvancedStats($colCabangGabah, $colCabangBeras);
        }

        return view('livewire.serapan');
    }

    /**
     * Helper Basic Stats (Min, Max, Avg)
     */
    private function getStats($model, $column, $dateColumn, $cabangColumn)
    {
        try {
            $query = $model::query();

            if (!empty($this->cabang)) {
                $query->where($cabangColumn, $this->cabang);
            }
            if (!empty($this->periode)) {
                $query->where($dateColumn, 'like', $this->periode . '%');
            }

            // Helper format float
            $toFloat = function($val) {
                if ($val === null || $val === '') return 0.00;
                $val = str_replace(',', '.', (string)$val);
                return (float) $val;
            };

            $min = $query->min($column);
            $max = $query->max($column);
            
            // Raw calculation for AVG to handle comma in DB
            $avg = $query->select(DB::raw("AVG(CAST(REPLACE($column, ',', '.') AS DECIMAL(10,2))) as avg_val"))->value('avg_val');

            return [
                'min' => $toFloat($min),
                'max' => $toFloat($max),
                'avg' => (float)$avg,
            ];
        } catch (\Exception $e) {
            return ['min' => 0, 'max' => 0, 'avg' => 0];
        }
    }

    /**
     * Hitung Standar Deviasi & Koefisien Variasi
     */
    private function calculateAdvancedStats($colGabah, $colBeras)
    {
        $getStdDev = function($model, $column, $dateCol, $locCol) {
            $q = $model::query();
            if (!empty($this->cabang)) $q->where($locCol, $this->cabang);
            if (!empty($this->periode)) $q->where($dateCol, 'like', $this->periode . '%');

            // Hitung Standard Deviasi (Population) langsung di SQL
            return $q->select(DB::raw("STDDEV(CAST(REPLACE($column, ',', '.') AS DECIMAL(15,2))) as sd"))
                     ->value('sd');
        };

        // --- Analisis Gabah (Kadar Air) ---
        $avgGabahKA = $this->gabahStats['ka']['avg'];
        $sdGabahKA  = $getStdDev(MasHpkkGabah::class, 'kadar_air_rata_rata', 'tanggal_pelaksanaan', $colGabah);
        $cvGabahKA  = ($avgGabahKA > 0) ? ($sdGabahKA / $avgGabahKA) * 100 : 0;

        // --- Analisis Beras (Rendemen) ---
        $avgBerasRen = $this->berasStats['rendemen']['avg'];
        $sdBerasRen  = $getStdDev(MasHpkkBeras::class, 'rendemen_pengolahan', 'tanggal_pemeriksaan', $colBeras);
        $cvBerasRen  = ($avgBerasRen > 0) ? ($sdBerasRen / $avgBerasRen) * 100 : 0;

        // --- Analisis Beras (Butir Patah) ---
        $avgBerasPatah = $this->berasStats['patah']['avg'];
        $sdBerasPatah  = $getStdDev(MasHpkkBeras::class, 'butir_patah', 'tanggal_pemeriksaan', $colBeras);
        $cvBerasPatah  = ($avgBerasPatah > 0) ? ($sdBerasPatah / $avgBerasPatah) * 100 : 0;

        $this->advancedStats = [
            'gabah_ka' => ['sd' => $sdGabahKA, 'cv' => $cvGabahKA],
            'beras_rendemen' => ['sd' => $sdBerasRen, 'cv' => $cvBerasRen],
            'beras_patah' => ['sd' => $sdBerasPatah, 'cv' => $cvBerasPatah],
        ];
    }
}