<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use Illuminate\Support\Facades\Auth;
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

    // === MOUNT (Dipanggil saat halaman dibuka) ===
    public function mount()
    {
        $this->tanggal_pelaksanaan = date('Y-m-d');
        $this->tanggal_doc = date('Y-m-d');
        
        // Isi Group Otomatis
        if (Auth::check()) {
            $this->group = Auth::user()->group;
        }

        // Generate nomor bayangan untuk tampilan awal (Preview)
        $this->generateNomorSurat(true);
    }

    // === GENERATE NOMOR SURAT ===
    public function generateNomorSurat($preview = false)
    {
        $bulan = date('m');
        $tahun = date('Y');
        $groupCode = $this->group ?? '0000';
        
        // Hitung jumlah data berdasarkan tahun dan group
        $query = MasHpkkGabah::whereYear('tanggal_pelaksanaan', $tahun)
                    ->where('group', $this->group);
        
        $count = $query->count();
        
        // Tambah 1 untuk nomor urut berikutnya
        $nextNo = $count + 1;
        
        $noUrut = sprintf("%05d", $nextNo);
        $romawi = $this->getRomawi($bulan);
        
        $generated = "$noUrut/GPK/$groupCode/SCI/$romawi/$tahun";

        // Jika hanya preview (saat mount), return string tanpa set property
        if ($preview) {
            $this->nomor_hpkk_gabah = $generated;
            return $generated;
        }

        // Jika saat save, return value untuk disimpan ke DB
        $this->nomor_hpkk_gabah = $generated;
        return $generated;
    }

    private function getRomawi($bulan)
    {
        $bulan = (int)$bulan;
        $map = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $map[$bulan] ?? '';
    }

    // === HITUNG RATA-RATA OTOMATIS SAAT KETIK ===
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['ulangan_1', 'ulangan_2', 'ulangan_3'])) {
            $val1 = (float) $this->ulangan_1;
            $val2 = (float) $this->ulangan_2;
            $val3 = (float) $this->ulangan_3;
            
            if ($val1 > 0 || $val2 > 0 || $val3 > 0) {
                $count = 0; $sum = 0;
                if($val1 > 0) { $sum+=$val1; $count++; }
                if($val2 > 0) { $sum+=$val2; $count++; }
                if($val3 > 0) { $sum+=$val3; $count++; }
                
                $this->kadar_air_rata_rata = $count > 0 ? round($sum / $count, 2) : 0;
            } else {
                $this->kadar_air_rata_rata = 0;
            }
        }
    }

    // === SIMPAN DATA (STORE) ===
    public function store()
    {
        // 1. Validasi Input
        $this->validate([
            'mitra' => 'required',
            'group' => 'required',
            'jumlah_timbangan' => 'required|numeric|min:1',
            'metode_timbang' => 'required',
            'kadar_air_rata_rata' => 'required|numeric|min:0',
        ], [
            'mitra.required' => 'Nama Mitra wajib diisi.',
            'jumlah_timbangan.required' => 'Jumlah timbangan tidak boleh kosong.',
            'metode_timbang.required' => 'Silakan pilih metode timbang.',
        ]);

        // Mulai Transaksi Database
        DB::beginTransaction();

        try {
            // 2. Generate Nomor Surat FINAL (Penting agar tidak duplikat)
            $finalNomorSurat = $this->generateNomorSurat(false);

            // 3. Mapping Metode Timbang (Dropdown -> Kolom DB)
            $val_weighbridge = ($this->metode_timbang === 'Weightbridge') ? 'Weightbridge' : null;
            $val_non_weighbridge = ($this->metode_timbang !== 'Weightbridge') ? $this->metode_timbang : null;

            // 4. Simpan ke Database
            MasHpkkGabah::create([
                'nomor_hpkk_gabah' => $finalNomorSurat,
                'no_order_pembelian' => $this->no_order_pembelian,
                'nomor_order' => $this->nomor_order,
                'mitra' => $this->mitra,
                'pengirim' => $this->pengirim,
                'tanggal_pelaksanaan' => $this->tanggal_pelaksanaan,
                'jenis_alat_angkut' => $this->jenis_alat_angkut,
                'nomor_registrasi_alat_angkut' => $this->nomor_registrasi_alat_angkut,
                'hama_penyakit' => $this->hama_penyakit,
                
                // Hasil Mapping
                'weighbridge' => $val_weighbridge,
                'non_weighbridge' => $val_non_weighbridge,
                
                'kode_sample' => $this->kode_sample,
                
                // Gunakan Null Coalescing (?? 0) agar tidak error jika kosong
                'jumlah_timbangan' => $this->jumlah_timbangan ?? 0,
                'ulangan_1' => $this->ulangan_1 ?? 0,
                'ulangan_2' => $this->ulangan_2 ?? 0,
                'ulangan_3' => $this->ulangan_3 ?? 0,
                'kadar_air_rata_rata' => $this->kadar_air_rata_rata ?? 0,
                'kadar_hampa' => $this->kadar_hampa ?? 0,
                'butir_hijau' => $this->butir_hijau ?? 0,
                
                'tanggal_doc' => $this->tanggal_doc,
                'lokasi' => $this->lokasi,
                'mengetahui' => $this->mengetahui,
                'petugas' => $this->petugas,
                'catatan' => $this->catatan,
                'group' => $this->group,
            ]);

            // Commit Transaksi (Simpan Permanen)
            DB::commit();

            // 5. Notifikasi Sukses
            session()->flash('message', "Data berhasil disimpan! No: $finalNomorSurat");

            // 6. Reset Form
            $this->reset([
                'mitra', 'pengirim', 'jenis_alat_angkut', 
                'nomor_registrasi_alat_angkut', 'jumlah_timbangan', 
                'ulangan_1', 'ulangan_2', 'ulangan_3', 
                'kadar_air_rata_rata', 'kadar_hampa', 'butir_hijau',
                'no_order_pembelian', 'nomor_order', 'kode_sample', 'catatan',
                'metode_timbang','lokasi','mengetahui','petugas'
            ]);
            
            // Generate nomor bayangan baru untuk input selanjutnya
            $this->generateNomorSurat(true);

        } catch (\Exception $e) {
            // Rollback Transaksi (Batalkan jika ada error)
            DB::rollBack();
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