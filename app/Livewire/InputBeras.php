<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkBeras;
use Illuminate\Support\Facades\DB;

class InputBeras extends Component
{
    // === IDENTITAS DOKUMEN ===
    public $nomor_hpkk_beras; // Auto
    public $id_mo; // Nomor MO
    public $nomor_order;
    public $tempat_pemeriksaan;
    public $tanggal_pemeriksaan;
    public $kode_sample;
    public $dasar_pemeriksaan;
    
    // === KUALITAS FISIK (Dropdown) ===
    public $kondisi_kemasan;
    public $hama; // Hama Penyakit
    public $dedak_katul_sekam;
    public $bau; // Bau Apek/Busuk
    public $bahan_kimia; // Kimia

    // === LAB: KADAR AIR ===
    public $ulangan_1;
    public $ulangan_2;
    public $ulangan_3;
    public $rata_rata; // Auto Calc (Kadar Air)

    // === LAB: FISIK BERAS (%) ===
    public $derajat_sosoh;
    public $butir_patah;
    public $menir;

    // === KUANTUM & RENDEMEN ===
    public $kuantum_gabah_sesuai_mo; // KG
    public $kuantum_beras; // KG
    public $rendemen_pengolahan; // Auto Calc

    // === HASIL SAMPING (KG) ===
    public $hasil_samping_menir;
    public $hasil_samping_butir_patah;
    public $hasil_samping_dedak_katul;
    public $hasil_samping_butir_kuning_rusak;

    // === FOOTER ===
    public $tanggal_doc;
    public $lokasi;
    public $mengetahui; // Nama Mengetahui
    public $petugas; // Nama Petugas
    public $catatan;
    public $group;
    public $status; // Auto "HPK"

    public function mount()
    {
        $this->tanggal_pemeriksaan = date('Y-m-d');
        $this->tanggal_doc = date('Y-m-d');
        $this->status = 'HPK'; // Default Value
        
        $this->generateNomorSurat();
    }

    // === AUTO CALCULATIONS ===
    public function updated($propertyName)
    {
        // 1. Hitung Rata-rata Kadar Air
        if (in_array($propertyName, ['ulangan_1', 'ulangan_2', 'ulangan_3'])) {
            $u1 = (float) $this->ulangan_1;
            $u2 = (float) $this->ulangan_2;
            $u3 = (float) $this->ulangan_3;

            if ($u1 || $u2 || $u3) {
                $avg = ($u1 + $u2 + $u3) / 3;
                $this->rata_rata = number_format($avg, 2, '.', '');
            } else {
                $this->rata_rata = 0;
            }
        }

        // 2. Hitung Rendemen Pengolahan
        // Rumus: (Kuantum Beras / Kuantum Gabah) * 100 (Biasanya persen)
        if (in_array($propertyName, ['kuantum_beras', 'kuantum_gabah_sesuai_mo'])) {
            $beras = (float) $this->kuantum_beras;
            $gabah = (float) $this->kuantum_gabah_sesuai_mo;

            if ($gabah > 0 && $beras > 0) {
                // Asumsi rendemen adalah persentase, jadi dikali 100.
                // Jika user minta murni pembagian, hapus * 100 nya.
                $rendemen = ($beras / $gabah) * 100; 
                $this->rendemen_pengolahan = number_format($rendemen, 2, '.', '');
            } else {
                $this->rendemen_pengolahan = 0;
            }
        }
    }

    // === GENERATE RUNNING NUMBER ===
    public function generateNomorSurat()
    {
        // Format: (5 Digit)/HGL/4101/SCI/(Romawi)/2025
        $tahun = date('Y');
        
        // Cari nomor terakhir di tabel BERAS berdasarkan tahun tanggal pemeriksaan
        $lastRecord = MasHpkkBeras::whereYear('tanggal_pemeriksaan', $tahun)
                        ->orderBy('id_hpkk_beras', 'desc')
                        ->first();

        $nextUrutan = 1;
        if ($lastRecord) {
            $parts = explode('/', $lastRecord->nomor_hpkk_beras);
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $nextUrutan = intval($parts[0]) + 1;
            }
        }

        // 5 Digit Padding
        $runningNo = str_pad($nextUrutan, 5, '0', STR_PAD_LEFT);
        $bulanRomawi = $this->getRomawi(date('n'));

        $this->nomor_hpkk_beras = "$runningNo/HGL/4101/SCI/$bulanRomawi/$tahun";
    }

    private function getRomawi($bulan) {
        $map = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
        return $map[$bulan] ?? 'I';
    }

    public function save()
    {
        $this->validate([
            'nomor_hpkk_beras' => 'required',
            'kuantum_gabah_sesuai_mo' => 'required|numeric',
            'kuantum_beras' => 'required|numeric',
            'ulangan_1' => 'required|numeric',
            'ulangan_2' => 'required|numeric',
            'ulangan_3' => 'required|numeric',
            'group' => 'required',
            'id_mo' => 'required',
        ]);

        try {
            MasHpkkBeras::create([
                'nomor_hpkk_beras' => $this->nomor_hpkk_beras,
                'id_mo' => $this->id_mo,
                'nomor_order' => $this->nomor_order,
                'tempat_pemeriksaan' => $this->tempat_pemeriksaan,
                'tanggal_pemeriksaan' => $this->tanggal_pemeriksaan,
                'kode_sample' => $this->kode_sample,
                'dasar_pemeriksaan' => $this->dasar_pemeriksaan,
                
                // Dropdowns
                'kondisi_kemasan' => $this->kondisi_kemasan,
                'hama' => $this->hama,
                'dedak_katul_sekam' => $this->dedak_katul_sekam,
                'bau' => $this->bau,
                'bahan_kimia' => $this->bahan_kimia,

                // Lab KA
                'ulangan_1' => $this->ulangan_1,
                'ulangan_2' => $this->ulangan_2,
                'ulangan_3' => $this->ulangan_3,
                'rata_rata' => $this->rata_rata,

                // Lab Fisik
                'derajat_sosoh' => $this->derajat_sosoh,
                'butir_patah' => $this->butir_patah,
                'menir' => $this->menir,

                // Kuantum & Rendemen
                'kuantum_gabah_sesuai_mo' => $this->kuantum_gabah_sesuai_mo,
                'kuantum_beras' => $this->kuantum_beras,
                'rendemen_pengolahan' => $this->rendemen_pengolahan,

                // Hasil Samping
                'hasil_samping_menir' => $this->hasil_samping_menir,
                'hasil_samping_butir_patah' => $this->hasil_samping_butir_patah,
                'hasil_samping_dedak_katul' => $this->hasil_samping_dedak_katul,
                'hasil_samping_butir_kuning_rusak' => $this->hasil_samping_butir_kuning_rusak,

                // Footer
                'tanggal_doc' => $this->tanggal_doc,
                'lokasi' => $this->lokasi,
                'mengetahui' => $this->mengetahui,
                'petugas' => $this->petugas,
                'catatan' => $this->catatan,
                'group' => $this->group,
                'status' => $this->status,
            ]);

            session()->flash('message', 'Data HPKK Beras berhasil dibuat!');
            
            // Reset form & generate nomor baru
            $this->generateNomorSurat();
            $this->reset([
                'id_mo', 'nomor_order', 'kode_sample', 'ulangan_1', 'ulangan_2', 'ulangan_3',
                'rata_rata', 'kuantum_beras', 'kuantum_gabah_sesuai_mo', 'rendemen_pengolahan',
                'derajat_sosoh', 'butir_patah', 'menir', 'catatan'
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->to('/');
    }

    public function render()
    {
        return view('livewire.input-beras');
    }
}