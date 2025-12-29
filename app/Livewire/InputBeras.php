<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkBeras;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InputBeras extends Component
{
    // === PROPERTI FORM ===
    public $nomor_hpkk_beras; 
    public $id_mo; 
    public $nomor_order;
    public $tempat_pemeriksaan;
    public $tanggal_pemeriksaan;
    public $kode_sample;
    public $dasar_pemeriksaan;
    
    // Kualitas Fisik
    public $kondisi_kemasan;
    public $hama; 
    public $dedak_katul_sekam;
    public $bau; 
    public $bahan_kimia; 

    // Lab Kadar Air
    public $ulangan_1;
    public $ulangan_2;
    public $ulangan_3;
    public $rata_rata; 

    // Fisik Beras
    public $derajat_sosoh;
    public $butir_patah;
    public $menir;

    // Kuantum
    public $kuantum_gabah_sesuai_mo; 
    public $kuantum_beras; 
    public $rendemen_pengolahan; 

    // Hasil Samping
    public $hasil_samping_menir;
    public $hasil_samping_butir_patah;
    public $hasil_samping_dedak_katul;
    public $hasil_samping_butir_kuning_rusak;

    // Footer
    public $tanggal_doc;
    public $lokasi;
    public $mengetahui;
    public $petugas;
    public $catatan;
    public $group; 
    public $status = 'Active';

    // === MOUNT ===
    public function mount()
    {
        $this->tanggal_pemeriksaan = date('Y-m-d');
        $this->tanggal_doc = date('Y-m-d');
        
        // 1. ISI GROUP OTOMATIS
        if (Auth::check()) {
            $this->group = Auth::user()->group;
        }

        // 2. GENERATE NOMOR SURAT (Preview Saja)
        $this->generateNomorSurat(true);
    }

    // === AUTO CALCULATIONS ===
    public function updated($propertyName)
    {
        // 1. Hitung Rata-rata Kadar Air
        if (in_array($propertyName, ['ulangan_1', 'ulangan_2', 'ulangan_3'])) {
            $u1 = (float) $this->ulangan_1;
            $u2 = (float) $this->ulangan_2;
            $u3 = (float) $this->ulangan_3;

            if ($u1 > 0 || $u2 > 0 || $u3 > 0) {
                $count = 0; $sum = 0;
                if($u1 > 0) { $sum += $u1; $count++; }
                if($u2 > 0) { $sum += $u2; $count++; }
                if($u3 > 0) { $sum += $u3; $count++; }

                $this->rata_rata = $count > 0 ? round($sum / $count, 2) : 0;
            } else {
                $this->rata_rata = 0;
            }
        }

        // 2. Hitung Rendemen Pengolahan
        if (in_array($propertyName, ['kuantum_beras', 'kuantum_gabah_sesuai_mo'])) {
            $beras = (float) $this->kuantum_beras;
            $gabah = (float) $this->kuantum_gabah_sesuai_mo;

            if ($gabah > 0 && $beras > 0) {
                $rendemen = ($beras / $gabah) * 100; 
                $this->rendemen_pengolahan = round($rendemen, 2);
            } else {
                $this->rendemen_pengolahan = 0;
            }
        }
    }

    // === GENERATE NOMOR SURAT ===
    public function generateNomorSurat($preview = false)
    {
        $bulan = date('m');
        $tahun = date('Y');
        $groupCode = $this->group ?? '0000';
        
        // Hitung count berdasarkan tahun dan group
        $query = MasHpkkBeras::whereYear('tanggal_pemeriksaan', $tahun)
                        ->where('group', $this->group);

        $count = $query->count();
        $nextNo = $count + 1;
        
        // Format: 00001/HGL/4101/SCI/XII/2025
        $noUrut = sprintf("%05d", $nextNo);
        $romawi = $this->getRomawi($bulan);
        
        $generated = "$noUrut/HGL/$groupCode/SCI/$romawi/$tahun";

        if ($preview) {
            $this->nomor_hpkk_beras = $generated;
            return $generated;
        }

        $this->nomor_hpkk_beras = $generated;
        return $generated;
    }

    private function getRomawi($bulan) {
        $bulan = (int)$bulan;
        $map = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $map[$bulan] ?? '';
    }

    // === SIMPAN DATA (STORE) ===
    public function store()
    {
        // 1. Validasi Input Wajib
        $this->validate([
            'id_mo' => 'required',
            'group' => 'required',
            'tanggal_pemeriksaan' => 'required|date',
            // Validasi numeric agar tidak error di database
            'ulangan_1' => 'nullable|numeric',
            'kuantum_beras' => 'nullable|numeric',
            'kuantum_gabah_sesuai_mo' => 'nullable|numeric',
        ]);

        // Gunakan Transaksi Database
        DB::beginTransaction();

        try {
            // 2. GENERATE NOMOR FINAL (Agar tidak duplikat)
            $finalNomorSurat = $this->generateNomorSurat(false);

            // 3. PERSIAPAN DATA (SANITASI INPUT)
            $data = [
                'nomor_hpkk_beras' => $finalNomorSurat,
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

                // Gunakan Null Coalescing (?? 0) agar aman
                'ulangan_1' => $this->ulangan_1 ?? 0,
                'ulangan_2' => $this->ulangan_2 ?? 0,
                'ulangan_3' => $this->ulangan_3 ?? 0,
                'rata_rata' => $this->rata_rata ?? 0,

                'derajat_sosoh' => $this->derajat_sosoh ?? 0,
                'butir_patah' => $this->butir_patah ?? 0,
                'menir' => $this->menir ?? 0,

                'kuantum_gabah_sesuai_mo' => $this->kuantum_gabah_sesuai_mo ?? 0,
                'kuantum_beras' => $this->kuantum_beras ?? 0,
                'rendemen_pengolahan' => $this->rendemen_pengolahan ?? 0,

                'hasil_samping_menir' => $this->hasil_samping_menir ?? 0,
                'hasil_samping_butir_patah' => $this->hasil_samping_butir_patah ?? 0,
                'hasil_samping_dedak_katul' => $this->hasil_samping_dedak_katul ?? 0,
                'hasil_samping_butir_kuning_rusak' => $this->hasil_samping_butir_kuning_rusak ?? 0,

                // Footer
                'tanggal_doc' => $this->tanggal_doc,
                'lokasi' => $this->lokasi,
                'mengetahui' => $this->mengetahui,
                'petugas' => $this->petugas,
                'catatan' => $this->catatan,
                'group' => $this->group,
                'status' => $this->status,
            ];

            // 4. Simpan ke Database
            MasHpkkBeras::create($data);

            DB::commit(); // Simpan Permanen

            // 5. Beri Pesan Sukses
            session()->flash('message', "Data HPKK Beras berhasil disimpan! No: $finalNomorSurat");
            
            // 6. RESET FORM
            $this->reset([
                'id_mo', 'nomor_order', 'tempat_pemeriksaan', 'kode_sample', 'dasar_pemeriksaan',
                'kondisi_kemasan', 'hama', 'dedak_katul_sekam', 'bau', 'bahan_kimia',
                'ulangan_1', 'ulangan_2', 'ulangan_3', 'rata_rata',
                'derajat_sosoh', 'butir_patah', 'menir',
                'kuantum_gabah_sesuai_mo', 'kuantum_beras', 'rendemen_pengolahan',
                'hasil_samping_menir', 'hasil_samping_butir_patah', 'hasil_samping_dedak_katul', 'hasil_samping_butir_kuning_rusak',
                'catatan', 'lokasi', 'mengetahui', 'petugas'
            ]);
            
            // 7. Generate nomor baru untuk input selanjutnya
            $this->generateNomorSurat(true);

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan jika error
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('list.beras');
    }

    public function render()
    {
        return view('livewire.input-beras');
    }
}