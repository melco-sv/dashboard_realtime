<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use Illuminate\Support\Facades\Auth;

class InputGabah extends Component
{
    // === PROPERTI FORM ===
    public $nomor_hpkk_gabah;
    public $no_order_pembelian;
    public $nomor_order;
    public $mitra;
    public $pengirim;
    public $tanggal_pelaksanaan;
    public $jenis_alat_angkut;
    public $nomor_registrasi_alat_angkut;
    public $hama_penyakit; 
    
    public $metode_timbang; // Dropdown Form
    
    public $jumlah_timbangan;
    public $kode_sample;
    
    // Data Lab
    public $ulangan_1;
    public $ulangan_2;
    public $ulangan_3;
    public $kadar_air_rata_rata; 
    public $kadar_hampa;
    public $butir_hijau;
    
    // Footer Form
    public $tanggal_doc;
    public $lokasi;
    public $mengetahui;
    public $petugas;
    public $catatan;
    public $group; 

    // === MOUNT ===
    public function mount()
    {
        $this->tanggal_pelaksanaan = date('Y-m-d');
        $this->tanggal_doc = date('Y-m-d');
        
        // Isi Group Otomatis
        if (Auth::check()) {
            $this->group = Auth::user()->group;
        }

        $this->generateNomorSurat();
    }

    // === GENERATE NOMOR SURAT ===
    public function generateNomorSurat()
    {
        $bulan = date('m');
        $tahun = date('Y');
        $groupCode = $this->group ?? '0000';
        
        // Menggunakan tanggal_pelaksanaan untuk menghitung urutan
        $count = MasHpkkGabah::whereYear('tanggal_pelaksanaan', $tahun)
                    ->where('group', $this->group)
                    ->count();
        
        $nextNo = $count + 1;
        
        $noUrut = sprintf("%05d", $nextNo);
        $romawi = $this->getRomawi($bulan);
        
        $this->nomor_hpkk_gabah = "$noUrut/GPK/$groupCode/SCI/$romawi/$tahun";
    }

    private function getRomawi($bulan)
    {
        $bulan = (int)$bulan;
        $map = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $map[$bulan] ?? '';
    }

    // === HITUNG RATA-RATA OTOMATIS ===
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['ulangan_1', 'ulangan_2', 'ulangan_3'])) {
            $val1 = (float) $this->ulangan_1;
            $val2 = (float) $this->ulangan_2;
            $val3 = (float) $this->ulangan_3;
            
            if ($val1 > 0 || $val2 > 0 || $val3 > 0) {
                $count = 0; $sum = 0;
                if($val1>0) { $sum+=$val1; $count++; }
                if($val2>0) { $sum+=$val2; $count++; }
                if($val3>0) { $sum+=$val3; $count++; }
                
                $this->kadar_air_rata_rata = $count > 0 ? round($sum / $count, 2) : 0;
            }
        }
    }

    // === SIMPAN DATA (STORE) ===
    public function store()
    {
        // 1. Validasi
        $this->validate([
            'mitra' => 'required',
            'group' => 'required',
        ]);

        try {
            // 2. MAPPING METODE TIMBANG (Logic Baru)
            // Memisahkan input dropdown menjadi 2 kolom database
            $val_weighbridge = null;
            $val_non_weighbridge = null;

            if ($this->metode_timbang === 'Weightbridge') {
                $val_weighbridge = 'Weightbridge';
            } elseif (!empty($this->metode_timbang)) {
                // Jika pilih "Non Weightbridge - Sawah" atau "MPP"
                $val_non_weighbridge = $this->metode_timbang;
            }

            // 3. Persiapan Data Array
            $data = [
                'nomor_hpkk_gabah' => $this->nomor_hpkk_gabah,
                'no_order_pembelian' => $this->no_order_pembelian,
                'nomor_order' => $this->nomor_order,
                'mitra' => $this->mitra,
                'pengirim' => $this->pengirim,
                'tanggal_pelaksanaan' => $this->tanggal_pelaksanaan,
                'jenis_alat_angkut' => $this->jenis_alat_angkut,
                'nomor_registrasi_alat_angkut' => $this->nomor_registrasi_alat_angkut,
                'hama_penyakit' => $this->hama_penyakit,
                
                // Masukkan hasil mapping tadi ke kolom yang sesuai
                'weighbridge' => $val_weighbridge,
                'non_weighbridge' => $val_non_weighbridge,

                'kode_sample' => $this->kode_sample,
                
                // Konversi Angka (0 jika kosong, agar tidak error database)
                'jumlah_timbangan' => $this->jumlah_timbangan === '' ? 0 : $this->jumlah_timbangan,
                'ulangan_1' => $this->ulangan_1 === '' ? 0 : $this->ulangan_1,
                'ulangan_2' => $this->ulangan_2 === '' ? 0 : $this->ulangan_2,
                'ulangan_3' => $this->ulangan_3 === '' ? 0 : $this->ulangan_3,
                'kadar_air_rata_rata' => $this->kadar_air_rata_rata === '' ? 0 : $this->kadar_air_rata_rata,
                'kadar_hampa' => $this->kadar_hampa === '' ? 0 : $this->kadar_hampa,
                'butir_hijau' => $this->butir_hijau === '' ? 0 : $this->butir_hijau,
                
                'tanggal_doc' => $this->tanggal_doc,
                'lokasi' => $this->lokasi,
                'mengetahui' => $this->mengetahui,
                'petugas' => $this->petugas,
                'catatan' => $this->catatan,
                'group' => $this->group,
            ];

            // 4. Simpan ke Database
            MasHpkkGabah::create($data);

            // 5. Beri Notifikasi Sukses
            session()->flash('message', 'Data HPKK Gabah berhasil dibuat!');

            // 6. Reset Form
            $this->reset([
                'mitra', 'pengirim', 'jenis_alat_angkut', 
                'nomor_registrasi_alat_angkut', 'jumlah_timbangan', 
                'ulangan_1', 'ulangan_2', 'ulangan_3', 
                'kadar_air_rata_rata', 'kadar_hampa', 'butir_hijau',
                'no_order_pembelian', 'nomor_order', 'kode_sample', 'catatan',
                'metode_timbang'
            ]);
            
            // 7. Generate nomor baru untuk input selanjutnya
            $this->generateNomorSurat();

        } catch (\Exception $e) {
            // Tampilkan error jika masih ada masalah
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('list.gabah');
    }

    public function render()
    {
        return view('livewire.input-gabah');
    }
}