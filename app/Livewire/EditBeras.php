<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkBeras;
use Illuminate\Support\Facades\Auth;

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

        $this->fill($data->toArray());
        $this->id_hpkk_beras = $data->id_hpkk_beras;

        // Pastikan tanggal dalam format Y-m-d untuk input[type=date]
        $this->tanggal_pemeriksaan = $data->tanggal_pemeriksaan
            ? \Carbon\Carbon::parse($data->tanggal_pemeriksaan)->format('Y-m-d')
            : null;
        $this->tanggal_doc = $data->tanggal_doc
            ? \Carbon\Carbon::parse($data->tanggal_doc)->format('Y-m-d')
            : null;
    }

    public function updated($propertyName): void
    {
        $val = (float) str_replace(',', '.', $this->{$propertyName} ?? 0);

        // Ulangan 1, 2, 3 — harus antara 10 s/d 14
        if (in_array($propertyName, ['ulangan_1', 'ulangan_2', 'ulangan_3'])) {
            if ($val > 0 && ($val < 10 || $val > 14)) {
                $this->dispatch('swal:error', [
                    'title' => 'Input Diluar Batas!',
                    'text'  => "Nilai $propertyName harus antara 10 s/d 14!",
                ]);
                $this->{$propertyName} = null;
            }
            $this->hitungRataRata();
        }

        // Derajat Sosoh — hanya boleh 95 atau 100
        if ($propertyName === 'derajat_sosoh' && $val > 0 && !in_array($val, [95, 100])) {
            $this->dispatch('swal:error', [
                'title' => 'Data Invalid!',
                'text'  => 'Derajat Sosoh hanya boleh bernilai 95 atau 100!',
            ]);
            $this->derajat_sosoh = null;
        }

        // Butir Patah — max 25
        if ($propertyName === 'butir_patah' && $val > 25) {
            $this->dispatch('swal:error', [
                'title' => 'Input Diluar Batas!',
                'text'  => 'Butir Patah tidak boleh melebihi 25%!',
            ]);
            $this->butir_patah = null;
        }

        // Menir — max 2
        if ($propertyName === 'menir' && $val > 2) {
            $this->dispatch('swal:error', [
                'title' => 'Input Diluar Batas!',
                'text'  => 'Menir tidak boleh melebihi 2%!',
            ]);
            $this->menir = null;
        }
    }

    private function hitungRataRata(): void
    {
        $u1 = (float) str_replace(',', '.', $this->ulangan_1 ?? 0);
        $u2 = (float) str_replace(',', '.', $this->ulangan_2 ?? 0);
        $u3 = (float) str_replace(',', '.', $this->ulangan_3 ?? 0);

        $count = ($u1 > 0 ? 1 : 0) + ($u2 > 0 ? 1 : 0) + ($u3 > 0 ? 1 : 0);
        $this->rata_rata = $count > 0
            ? round(($u1 + $u2 + $u3) / $count, 2)
            : 0;
    }

    public function update()
    {
        $this->validate([
            'tanggal_pemeriksaan' => 'required|date',
            'id_mo'               => 'required',
            'tempat_pemeriksaan'  => 'required',
        ]);

        try {
            $data = MasHpkkBeras::findOrFail($this->id_hpkk_beras);

            // Fill dulu tanpa save agar getDirty() bisa mendeteksi perubahan
            $data->fill([
                'nomor_hpkk_beras'                => $this->nomor_hpkk_beras,
                'id_mo'                           => $this->id_mo,
                'nomor_order'                     => $this->nomor_order,
                'kode_sample'                     => $this->kode_sample,
                'tanggal_pemeriksaan'             => $this->tanggal_pemeriksaan,
                'tempat_pemeriksaan'              => $this->tempat_pemeriksaan,
                'lokasi'                          => $this->lokasi,
                'periode'                         => $this->periode,
                'dasar_pemeriksaan'               => $this->dasar_pemeriksaan,
                'kondisi_kemasan'                 => $this->kondisi_kemasan,
                'hama'                            => $this->hama,
                'dedak_katul_sekam'               => $this->dedak_katul_sekam,
                'bau'                             => $this->bau,
                'bahan_kimia'                     => $this->bahan_kimia,
                'ulangan_1'                       => $this->ulangan_1,
                'ulangan_2'                       => $this->ulangan_2,
                'ulangan_3'                       => $this->ulangan_3,
                'rata_rata'                       => $this->rata_rata,
                'derajat_sosoh'                   => $this->derajat_sosoh,
                'butir_patah'                     => $this->butir_patah,
                'menir'                           => $this->menir,
                'kuantum_gabah_sesuai_mo'         => $this->kuantum_gabah_sesuai_mo,
                'kuantum_beras'                   => $this->kuantum_beras,
                'rendemen_pengolahan'             => $this->rendemen_pengolahan,
                'hasil_samping_menir'             => $this->hasil_samping_menir,
                'hasil_samping_butir_patah'       => $this->hasil_samping_butir_patah,
                'hasil_samping_dedak_katul'       => $this->hasil_samping_dedak_katul,
                'hasil_samping_butir_kuning_rusak' => $this->hasil_samping_butir_kuning_rusak,
                'catatan'                         => $this->catatan,
                'petugas'                         => $this->petugas,
                'mengetahui'                      => $this->mengetahui,
                'group'                           => $this->group,
                'status'                          => $this->status,
            ]);

            // Rekam field apa yang berubah beserta nilai lama vs baru
            $perubahan = [];
            foreach ($data->getDirty() as $field => $nilaiBarú) {
                $perubahan[$field] = [
                    'dari'    => $data->getOriginal($field),
                    'menjadi' => $nilaiBarú,
                ];
            }

            $data->save();

            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'no_lhpk'   => $this->nomor_hpkk_beras,
                    'no_mo'     => $this->id_mo,
                    'petugas'   => $this->petugas,
                    'perubahan' => $perubahan,
                ])
                ->log('Update HGL');

            session()->flash('message', 'Data HPKK Beras berhasil diperbarui.');

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
