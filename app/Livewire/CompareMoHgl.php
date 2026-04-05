<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CompareMoHgl extends Component
{
    public $searchMo = '';
    public $resultData = null;

    // Variable Ringkasan
    public $totalKuantum = 0;
    public $avgKa1 = 0;
    public $avgKa2 = 0;
    public $avgKa3 = 0;
    public $avgButirPatah = 0;
    public $avgMenir = 0;
    public $avgSosoh = 0;

    // Helper: String DB -> Float Komputer (Untuk Hitungan)
    private function simpleFloat($val)
    {
        if (!$val) return 0;
        $clean = str_replace('.', '', $val); // Buang titik ribuan
        $clean = str_replace(',', '.', $clean); // Koma jadi titik
        return (float) $clean;
    }

    // Helper: Float Komputer -> String Indo (Untuk Tampilan Ringkasan)
    public function formatSummary($val)
    {
        // Format 2 desimal
        $formatted = number_format((float)$val, 2, ',', '.');
        // Bersihkan nol dan koma berlebih di belakang
        $formatted = rtrim($formatted, '0');
        $formatted = rtrim($formatted, ',');
        return $formatted;
    }

    public function cari()
    {
        $this->validate([
            'searchMo' => 'required|string|min:3'
        ]);

        // Gunakan DB::table untuk bypass casting Model (ambil data MENTAH)
        $data = DB::table('mas_hpkk_beras')
            ->leftJoin('ref_cabang', 'mas_hpkk_beras.group', '=', 'ref_cabang.code_cabang')
            ->select('mas_hpkk_beras.*', 'ref_cabang.parent_company')
            ->where('mas_hpkk_beras.id_mo', 'like', '%' . $this->searchMo . '%')
            ->get();

        if ($data->count() > 0) {
            $this->resultData = $data;

            // Hitung Ringkasan
            $this->totalKuantum = $data->sum(fn($row) => $this->simpleFloat($row->kuantum_beras));

            $this->avgKa1 = $data->avg(fn($row) => $this->simpleFloat($row->ulangan_1));
            $this->avgKa2 = $data->avg(fn($row) => $this->simpleFloat($row->ulangan_2));
            $this->avgKa3 = $data->avg(fn($row) => $this->simpleFloat($row->ulangan_3));
            $this->avgButirPatah = $data->avg(fn($row) => $this->simpleFloat($row->butir_patah));
            $this->avgMenir = $data->avg(fn($row) => $this->simpleFloat($row->menir));
            $this->avgSosoh = $data->avg(fn($row) => $this->simpleFloat($row->derajat_sosoh));
        } else {
            $this->resultData = null;
            $this->reset(['totalKuantum', 'avgKa1', 'avgKa2', 'avgKa3', 'avgButirPatah', 'avgMenir', 'avgSosoh']);
            session()->flash('error', 'Data MO tidak ditemukan.');
        }
    }

    public function render()
    {
        return view('livewire.compare-mo-hgl');
    }
}
