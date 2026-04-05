<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;

class EditGabah extends Component
{
    public $id_data; // ID Data yang diedit

    // Property Form (Sama persis dengan InputGabah)
    public $nomor_hpkk_gabah;
    public $no_order_pembelian;
    public $nomor_order;
    public $mitra;
    public $pengirim;
    public $tanggal_pelaksanaan;
    public $jenis_alat_angkut;
    public $nomor_registrasi_alat_angkut;
    public $hama_penyakit;
    public $metode_timbang;
    public $jumlah_timbangan;
    public $kode_sample;

    public $ulangan_1;
    public $ulangan_2;
    public $ulangan_3;
    public $kadar_air_rata_rata;
    public $kadar_hampa;
    public $butir_hijau;

    public $tanggal_doc;
    public $lokasi;
    public $mengetahui;
    public $petugas;
    public $catatan;
    public $group;

    // === MOUNT (Ambil Data Lama) ===
    public function mount($id)
    {
        $this->id_data = $id;
        $data = MasHpkkGabah::findOrFail($id);

        // Isi form dengan data dari database
        $this->nomor_hpkk_gabah = $data->nomor_hpkk_gabah;
        $this->no_order_pembelian = $data->no_order_pembelian;
        $this->nomor_order = $data->nomor_order;
        $this->mitra = $data->mitra;
        $this->pengirim = $data->pengirim;
        $this->tanggal_pelaksanaan = $data->tanggal_pelaksanaan;
        $this->jenis_alat_angkut = $data->jenis_alat_angkut;
        $this->nomor_registrasi_alat_angkut = $data->nomor_registrasi_alat_angkut;
        $this->hama_penyakit = $data->hama_penyakit;

        // Logika Reverse Metode Timbang
        if ($data->weighbridge == 'Weightbridge') {
            $this->metode_timbang = 'Weightbridge';
        } else {
            $this->metode_timbang = $data->non_weighbridge;
        }

        $this->jumlah_timbangan = $data->jumlah_timbangan;
        $this->kode_sample = $data->kode_sample;
        $this->ulangan_1 = $data->ulangan_1;
        $this->ulangan_2 = $data->ulangan_2;
        $this->ulangan_3 = $data->ulangan_3;
        $this->kadar_air_rata_rata = $data->kadar_air_rata_rata;
        $this->kadar_hampa = $data->kadar_hampa;
        $this->butir_hijau = $data->butir_hijau;

        $this->tanggal_doc = $data->tanggal_doc;
        $this->lokasi = $data->lokasi;
        $this->mengetahui = $data->mengetahui;
        $this->petugas = $data->petugas;
        $this->catatan = $data->catatan;
        $this->group = $data->group;
    }

    // Auto Calc Tetap Ada
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['ulangan_1', 'ulangan_2', 'ulangan_3'])) {
            $u1 = (float) $this->ulangan_1;
            $u2 = (float) $this->ulangan_2;
            $u3 = (float) $this->ulangan_3;
            if ($u1 || $u2 || $u3) {
                $avg = ($u1 + $u2 + $u3) / 3;
                $this->kadar_air_rata_rata = number_format($avg, 2, '.', '');
            } else {
                $this->kadar_air_rata_rata = 0;
            }
        }
    }

    // === UPDATE DATA ===
    public function update()
    {
        $this->validate([
            'mitra' => 'required',
            'jumlah_timbangan' => 'required|numeric',
        ]);

        try {
            $data = MasHpkkGabah::findOrFail($this->id_data);

            $wbVal = null;
            $nonWbVal = null;
            if ($this->metode_timbang == 'Weightbridge') {
                $wbVal = 'Weightbridge';
            } else {
                $nonWbVal = $this->metode_timbang;
            }

            $data->update([
                // Nomor HPKK biasanya TIDAK diubah saat edit agar konsisten, 
                // tapi jika mau diubah, uncomment baris bawah:
                // 'nomor_hpkk_gabah' => $this->nomor_hpkk_gabah,

                'no_order_pembelian' => $this->no_order_pembelian,
                'nomor_order' => $this->nomor_order,
                'mitra' => $this->mitra,
                'pengirim' => $this->pengirim,
                'tanggal_pelaksanaan' => $this->tanggal_pelaksanaan,
                'jenis_alat_angkut' => $this->jenis_alat_angkut,
                'nomor_registrasi_alat_angkut' => $this->nomor_registrasi_alat_angkut,
                'hama_penyakit' => $this->hama_penyakit,

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

            session()->flash('message', 'Data berhasil diperbarui!');
            return redirect()->route('list.gabah');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('list.gabah');
    }

    public function render()
    {
        return view('livewire.edit-gabah');
    }
}
