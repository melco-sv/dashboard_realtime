<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use Illuminate\Support\Facades\DB;

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
    public $hama_penyakit; // Dropdown
    public $metode_timbang; // Dropdown
    public $jumlah_timbangan;
    public $kode_sample;
    
    // Data Lab
    public $ulangan_1;
    public $ulangan_2;
    public $ulangan_3;
    public $kadar_air_rata_rata; // Auto Calc
    public $kadar_hampa;
    public $butir_hijau;
    
    // Footer Form
    public $tanggal_doc;
    public $lokasi;
    public $mengetahui;
    public $petugas;
    public $catatan;
    public $group;

    // === MOUNT (Jalan saat halaman dibuka) ===
    public function mount()
    {
        // Set tanggal default hari ini
        $this->tanggal_pelaksanaan = date('Y-m-d');
        $this->tanggal_doc = date('Y-m-d');
        
        // Generate Nomor Surat Otomatis
        $this->generateNomorSurat();
    }

    // === AUTO CALCULATE KADAR AIR ===
    public function updated($propertyName)
    {
        // Hitung rata-rata otomatis saat user mengetik nilai ulangan
        if (in_array($propertyName, ['ulangan_1', 'ulangan_2', 'ulangan_3'])) {
            $u1 = (float) $this->ulangan_1;
            $u2 = (float) $this->ulangan_2;
            $u3 = (float) $this->ulangan_3;

            if ($u1 || $u2 || $u3) {
                $avg = ($u1 + $u2 + $u3) / 3;
                $this->kadar_air_rata_rata = number_format($avg, 2, '.', ''); // 2 Desimal
            } else {
                $this->kadar_air_rata_rata = 0;
            }
        }
    }

    // === GENERATE NOMOR SURAT (5 DIGIT) ===
    public function generateNomorSurat()
    {
        // 1. Tentukan Tahun dari Tanggal Hari Ini
        $tahun = date('Y');
        
        // 2. Cari data terakhir di tahun ini berdasarkan 'tanggal_pelaksanaan'
        // (Kita tidak pakai created_at karena tabel Anda tidak punya kolom itu)
        $lastRecord = MasHpkkGabah::whereYear('tanggal_pelaksanaan', $tahun)
                        ->orderBy('id_hpkk_gabah', 'desc')
                        ->first();

        // 3. Tentukan Urutan Selanjutnya
        $nextUrutan = 1;
        if ($lastRecord) {
            // Format DB: 00001/GKP/... -> Kita ambil angka paling depan
            $parts = explode('/', $lastRecord->nomor_hpkk_gabah);
            
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $nextUrutan = intval($parts[0]) + 1;
            }
        }

        // 4. FORMAT 5 DIGIT (Padding '0' di kiri sampai panjang 5)
        // Contoh: 1 -> 00001
        $runningNo = str_pad($nextUrutan, 5, '0', STR_PAD_LEFT);

        // 5. Romawi Bulan
        $bulanRomawi = $this->getRomawi(date('n'));

        // 6. Susun String Akhir
        $this->nomor_hpkk_gabah = "$runningNo/GKP/4101/SCI/$bulanRomawi/$tahun";
    }

    // Helper Romawi
    private function getRomawi($bulan) {
        $map = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
        return $map[$bulan] ?? 'I';
    }

    // === SAVE DATA ===
    public function save()
    {
        // 1. Validasi Input
        $this->validate([
            'nomor_hpkk_gabah' => 'required',
            'mitra' => 'required',
            'jumlah_timbangan' => 'required|numeric',
            'ulangan_1' => 'required|numeric',
            'ulangan_2' => 'required|numeric',
            'ulangan_3' => 'required|numeric',
            'group' => 'required',
        ]);

        try {
            // 2. Mapping Metode Timbang ke Kolom DB
            $wbVal = null;
            $nonWbVal = null;

            if ($this->metode_timbang == 'Weightbridge') {
                $wbVal = 'Weightbridge'; 
            } elseif ($this->metode_timbang) {
                // Jika pilih Non-Weightbridge (Sawah/MPP), masuk ke kolom non_weighbridge
                $nonWbVal = $this->metode_timbang; 
            }

            // 3. Simpan ke Database
            MasHpkkGabah::create([
                'nomor_hpkk_gabah' => $this->nomor_hpkk_gabah,
                'no_order_pembelian' => $this->no_order_pembelian,
                'nomor_order' => $this->nomor_order,
                'mitra' => $this->mitra,
                'pengirim' => $this->pengirim,
                'tanggal_pelaksanaan' => $this->tanggal_pelaksanaan,
                'jenis_alat_angkut' => $this->jenis_alat_angkut,
                'nomor_registrasi_alat_angkut' => $this->nomor_registrasi_alat_angkut,
                'hama_penyakit' => $this->hama_penyakit,
                
                // Mapping kolom timbangan
                'weighbridge' => $wbVal,
                'non_weighbridge' => $nonWbVal,
                'jumlah_timbangan' => $this->jumlah_timbangan,

                'kode_sample' => $this->kode_sample,
                'ulangan_1' => $this->ulangan_1,
                'ulangan_2' => $this->ulangan_2,
                'ulangan_3' => $this->ulangan_3,
                'kadar_air_rata_rata' => $this->kadar_air_rata_rata,
                'kadar_hampa' => $this->kadar_hampa,
                'butir_hijau' => $this->butir_hijau,
                
                'tanggal_doc' => $this->tanggal_doc,
                'lokasi' => $this->lokasi,
                'mengetahui' => $this->mengetahui,
                'petugas' => $this->petugas,
                'catatan' => $this->catatan,
                'group' => $this->group,
            ]);

            // 4. Feedback & Reset
            session()->flash('message', 'Data HPKK Gabah berhasil dibuat!');
            
            // Generate nomor baru untuk input selanjutnya
            $this->generateNomorSurat(); 
            
            // Bersihkan field agar siap input lagi
            $this->reset([
                'mitra', 'pengirim', 'jenis_alat_angkut', 
                'nomor_registrasi_alat_angkut', 'jumlah_timbangan', 
                'ulangan_1', 'ulangan_2', 'ulangan_3', 
                'kadar_air_rata_rata', 'kadar_hampa', 'butir_hijau',
                'no_order_pembelian', 'nomor_order', 'kode_sample', 'catatan'
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->to('/'); // Kembali ke dashboard
    }

    public function render()
    {
        return view('livewire.input-gabah');
    }
}