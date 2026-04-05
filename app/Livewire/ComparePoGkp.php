<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ComparePoGkp extends Component
{
    public $searchPo = '';
    public $resultData = null;

    // Variable Ringkasan
    public $totalKuantum = 0;
    public $avgKa1 = 0;
    public $avgKa2 = 0;
    public $avgKa3 = 0;
    public $avgHampa = 0;
    public $avgButirHijau = 0;

    // Helper untuk hitungan matematika (Membersihkan string DB jadi float)
    private function simpleFloat($val)
    {
        if (!$val) return 0;
        // Hapus titik ribuan, ganti koma jadi titik
        $clean = str_replace('.', '', $val);
        $clean = str_replace(',', '.', $clean);
        return (float) $clean;
    }

    // --- FUNGSI FORMAT SUMMARY (YANG DIPERBAIKI) ---
    public function formatSummary($val)
    {
        // 1. Format awal dengan 2 desimal standar (misal: 30.8 -> 30,80 | 1594 -> 1.594,00)
        $formatted = number_format((float)$val, 2, ',', '.');

        // 2. Hapus NOL di belakang koma (30,80 -> 30,8)
        $formatted = rtrim($formatted, '0');

        // 3. Hapus KOMA jika angka jadi bulat (1.594, -> 1.594)
        $formatted = rtrim($formatted, ',');

        return $formatted;
    }

    public function cari()
    {
        $this->validate([
            'searchPo' => 'required|string|min:3'
        ]);

        // Ambil Data Mentah dari Database
        $data = DB::table('mas_hpkk_gabah')
            ->leftJoin('ref_cabang', 'mas_hpkk_gabah.group', '=', 'ref_cabang.code_cabang')
            ->select('mas_hpkk_gabah.*', 'ref_cabang.parent_company')
            ->where('mas_hpkk_gabah.no_order_pembelian', 'like', '%' . $this->searchPo . '%')
            ->get();

        if ($data->count() > 0) {
            $this->resultData = $data;

            // Hitung Ringkasan (Matematika)
            $this->totalKuantum = $data->sum(fn($row) => $this->simpleFloat($row->jumlah_timbangan));

            $this->avgKa1 = $data->avg(fn($row) => $this->simpleFloat($row->ulangan_1));
            $this->avgKa2 = $data->avg(fn($row) => $this->simpleFloat($row->ulangan_2));
            $this->avgKa3 = $data->avg(fn($row) => $this->simpleFloat($row->ulangan_3));
            $this->avgHampa = $data->avg(fn($row) => $this->simpleFloat($row->kadar_hampa));
            $this->avgButirHijau = $data->avg(fn($row) => $this->simpleFloat($row->butir_hijau));
        } else {
            $this->resultData = null;
            $this->reset(['totalKuantum', 'avgKa1', 'avgKa2', 'avgKa3', 'avgHampa', 'avgButirHijau']);
            session()->flash('error', 'Data PO tidak ditemukan.');
        }
    }

    public function render()
    {
        return view('livewire.compare-po-gkp');
    }
}
