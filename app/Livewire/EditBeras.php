<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkBeras;
use Livewire\Attributes\Validate;

class EditBeras extends Component
{
    public $id_hpkk_beras;

    // --- GROUP 1: Identitas Dokumen ---
    public $nomor_hpkk_beras;
    public $id_mo;
    public $nomor_order;
    public $kode_sample;
    public $tanggal_pemeriksaan;
    public $tempat_pemeriksaan;
    public $lokasi;
    public $periode;

    // --- GROUP 2: Pemeriksaan Fisik & Dasar ---
    public $dasar_pemeriksaan;
    public $kondisi_kemasan;
    public $hama;
    public $dedak_katul_sekam; // Analisa fisik visual
    public $bau;
    public $bahan_kimia;

    // --- GROUP 3: Hasil Lab (Angka/Float) ---
    public $ulangan_1;
    public $ulangan_2;
    public $ulangan_3;
    public $rata_rata; // Rata-rata kadar air
    public $derajat_sosoh;
    public $butir_patah;
    public $menir;

    // --- GROUP 4: Kuantum & Hasil Samping ---
    public $kuantum_gabah_sesuai_mo;
    public $kuantum_beras;
    public $rendemen_pengolahan;

    public $hasil_samping_menir;
    public $hasil_samping_butir_patah;
    public $hasil_samping_dedak_katul;
    public $hasil_samping_butir_kuning_rusak;

    // --- GROUP 5: Lainnya ---
    public $catatan;
    public $petugas;
    public $mengetahui;
    public $group;
    public $status;
    public $tanggal_doc; // Timestamp biasanya otomatis, tapi bisa diedit jika perlu

    public function mount($id)
    {
        $data = MasHpkkBeras::findOrFail($id);

        // Mapping semua kolom database ke variable public
        $this->fill($data->toArray());

        // Khusus ID harus diset manual jika fill() tidak menangkap primary key yang protected
        $this->id_hpkk_beras = $data->id_hpkk_beras;
    }

    public function update()
    {
        $this->validate();

        try {
            $data = MasHpkkBeras::findOrFail($this->id_hpkk_beras);

            $data->update([
                'id_hpkk_beras' => $this->id_hpkk_beras,
                'nomor_hpkk_beras' => $this->nomor_hpkk_beras,
                'id_mo' => $this->id_mo,
                'nomor_order' => $this->nomor_order,
                'kode_sample' => $this->kode_sample,
                'tanggal_pemeriksaan' => $this->tanggal_pemeriksaan,
                'tempat_pemeriksaan' => $this->tempat_pemeriksaan,
                'lokasi' => $this->lokasi,
                'periode' => $this->periode,

                'dasar_pemeriksaan' => $this->dasar_pemeriksaan,
                'kondisi_kemasan' => $this->kondisi_kemasan,
                'hama' => $this->hama,
                'dedak_katul_sekam' => $this->dedak_katul_sekam,
                'bau' => $this->bau,
                'bahan_kimia' => $this->bahan_kimia,

                'ulangan_1' => $this->ulangan_1,
                'ulangan_2' => $this->ulangan_2,
                'ulangan_3' => $this->ulangan_3,
                'rata_rata' => $this->rata_rata,
                'derajat_sosoh' => $this->derajat_sosoh,
                'butir_patah' => $this->butir_patah,
                'menir' => $this->menir,

                'kuantum_gabah_sesuai_mo' => $this->kuantum_gabah_sesuai_mo,
                'kuantum_beras' => $this->kuantum_beras,
                'rendemen_pengolahan' => $this->rendemen_pengolahan,

                'hasil_samping_menir' => $this->hasil_samping_menir,
                'hasil_samping_butir_patah' => $this->hasil_samping_butir_patah,
                'hasil_samping_dedak_katul' => $this->hasil_samping_dedak_katul,
                'hasil_samping_butir_kuning_rusak' => $this->hasil_samping_butir_kuning_rusak,

                'catatan' => $this->catatan,
                'petugas' => $this->petugas,
                'mengetahui' => $this->mengetahui,
                'group' => $this->group,
                'status' => $this->status,
            ]);

            session()->flash('message', 'Data HPKK Beras berhasil diperbarui.');

            // Redirect kembali ke list
            return $this->redirect(route('list.beras'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.edit-beras');
    }
}
