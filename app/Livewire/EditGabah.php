<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use Illuminate\Support\Facades\Auth;

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
        $this->tanggal_pelaksanaan = $data->tanggal_pelaksanaan
            ? \Carbon\Carbon::parse($data->tanggal_pelaksanaan)->format('Y-m-d')
            : null;
        $this->jenis_alat_angkut = $data->jenis_alat_angkut;
        $this->nomor_registrasi_alat_angkut = $data->nomor_registrasi_alat_angkut;
        $this->hama_penyakit = $data->hama_penyakit;
        $this->metode_timbang = $data->metode_timbang ?? '';

        $this->jumlah_timbangan = $data->jumlah_timbangan;
        $this->kode_sample = $data->kode_sample;
        $this->ulangan_1 = $data->ulangan_1;
        $this->ulangan_2 = $data->ulangan_2;
        $this->ulangan_3 = $data->ulangan_3;
        $this->kadar_air_rata_rata = $data->kadar_air_rata_rata;
        $this->kadar_hampa = $data->kadar_hampa;
        $this->butir_hijau = $data->butir_hijau;

        $this->tanggal_doc = $data->tanggal_doc
            ? \Carbon\Carbon::parse($data->tanggal_doc)->format('Y-m-d')
            : null;
        $this->lokasi = $data->lokasi;
        $this->mengetahui = $data->mengetahui;
        $this->petugas = $data->petugas;
        $this->catatan = $data->catatan;
        $this->group = $data->group;
    }

    public function updated($propertyName): void
    {
        $val = (float) str_replace(',', '.', $this->{$propertyName} ?? 0);

        // Ulangan 1, 2, 3 — harus antara 10 s/d 38 (standar GKP)
        if (in_array($propertyName, ['ulangan_1', 'ulangan_2', 'ulangan_3'])) {
            if ($val > 0 && ($val < 10 || $val > 38)) {
                $this->dispatch('swal:error', [
                    'title' => 'Input Diluar Batas!',
                    'text'  => "Nilai $propertyName harus berada di rentang 10 s/d 38!",
                ]);
                $this->{$propertyName} = null;
            }
            $this->hitungRataRata();
        }

        // Kadar Hampa — maks 40%
        if ($propertyName === 'kadar_hampa' && $val > 40) {
            $this->dispatch('swal:error', [
                'title' => 'Input Diluar Batas!',
                'text'  => 'Kadar Hampa tidak boleh melebihi 40%!',
            ]);
            $this->kadar_hampa = null;
        }

        // Butir Hijau — maks 30%
        if ($propertyName === 'butir_hijau' && $val > 30) {
            $this->dispatch('swal:error', [
                'title' => 'Input Diluar Batas!',
                'text'  => 'Butir Hijau tidak boleh melebihi 30%!',
            ]);
            $this->butir_hijau = null;
        }
    }

    private function hitungRataRata(): void
    {
        $u1 = (float) str_replace(',', '.', $this->ulangan_1 ?? 0);
        $u2 = (float) str_replace(',', '.', $this->ulangan_2 ?? 0);
        $u3 = (float) str_replace(',', '.', $this->ulangan_3 ?? 0);

        $count = ($u1 > 0 ? 1 : 0) + ($u2 > 0 ? 1 : 0) + ($u3 > 0 ? 1 : 0);
        $this->kadar_air_rata_rata = $count > 0
            ? number_format(($u1 + $u2 + $u3) / $count, 2, '.', '')
            : 0;
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

            // Fill dulu tanpa save agar getDirty() bisa mendeteksi perubahan
            $data->fill([
                'no_order_pembelian'           => $this->no_order_pembelian,
                'nomor_order'                  => $this->nomor_order,
                'mitra'                        => $this->mitra,
                'pengirim'                     => $this->pengirim,
                'tanggal_pelaksanaan'          => $this->tanggal_pelaksanaan,
                'jenis_alat_angkut'            => $this->jenis_alat_angkut,
                'nomor_registrasi_alat_angkut' => $this->nomor_registrasi_alat_angkut,
                'hama_penyakit'                => $this->hama_penyakit,
                'jumlah_timbangan'             => $this->jumlah_timbangan,
                'kode_sample'                  => $this->kode_sample,
                'ulangan_1'                    => $this->ulangan_1,
                'ulangan_2'                    => $this->ulangan_2,
                'ulangan_3'                    => $this->ulangan_3,
                'kadar_air_rata_rata'          => $this->kadar_air_rata_rata,
                'kadar_hampa'                  => $this->kadar_hampa,
                'butir_hijau'                  => $this->butir_hijau,
                'tanggal_doc'                  => $this->tanggal_doc,
                'lokasi'                       => $this->lokasi,
                'mengetahui'                   => $this->mengetahui,
                'petugas'                      => $this->petugas,
                'catatan'                      => $this->catatan,
                'group'                        => $this->group,
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
                    'no_hpk'    => $this->nomor_hpkk_gabah,
                    'petugas'   => $this->petugas,
                    'perubahan' => $perubahan,
                ])
                ->log('Update GKP');

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
