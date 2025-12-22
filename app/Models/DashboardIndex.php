<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaksi; // Panggil Model yang dibuat di Langkah 1

class DashboardIndex extends Component
{
    public $totalData;
    public $dataTerbaru;

    public function render()
    {
        // LOGIC: Ambil data dari database CI3
        
        // 1. Hitung total baris
        $this->totalData = Transaksi::count();

        // 2. Ambil 5 data terakhir (sesuaikan 'id' dengan kolom urutan di tabel Anda)
        $this->dataTerbaru = Transaksi::orderBy('id', 'desc')->take(5)->get();

        return view('livewire.dashboard-index');
    }
}