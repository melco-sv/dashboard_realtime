<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasHpkkGabah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
    public $metode_timbang;
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

        if (Auth::check()) {
            $this->group = Auth::user()->group;
        }

        $this->generateNomorSurat(true);
    }

    // === REAL-TIME VALIDATION & AUTO RESET (SWEETALERT) ===
    public function updated($propertyName)
    {
        $val = $this->parseNumber($this->{$propertyName});

        // 1. CEK ULANGAN 1, 2, 3 (Range 10 - 38)
        if (in_array($propertyName, ['ulangan_1', 'ulangan_2', 'ulangan_3'])) {
            if ($val > 0 && ($val < 10 || $val > 38)) {
                // Tampilkan SweetAlert Error
                $this->dispatch('swal:error', [
                    'title' => 'Input Diluar Batas!',
                    'text'  => "Nilai $propertyName harus berada di rentang 10 s/d 38!",
                ]);

                // Auto Reset: Kosongkan inputan
                $this->{$propertyName} = null;
            }
            $this->hitungRataRata();
        }

        // 2. CEK KADAR HAMPA (Maksimal 40)
        if ($propertyName == 'kadar_hampa') {
            if ($val > 40) {
                $this->dispatch('swal:error', [
                    'title' => 'Input Diluar Batas!',
                    'text'  => 'Kadar Hampa tidak boleh melebihi 40%!',
                ]);

                // Auto Reset
                $this->kadar_hampa = null;
            }
        }

        // 3. CEK BUTIR HIJAU (Maksimal 30)
        if ($propertyName == 'butir_hijau') {
            if ($val > 30) {
                $this->dispatch('swal:error', [
                    'title' => 'Input Diluar Batas!',
                    'text'  => 'Butir Hijau tidak boleh melebihi 30%!',
                ]);

                // Auto Reset
                $this->butir_hijau = null;
            }
        }
    }

    // === LOGIKA HITUNGAN ===
    private function hitungRataRata()
    {
        $val1 = $this->parseNumber($this->ulangan_1);
        $val2 = $this->parseNumber($this->ulangan_2);
        $val3 = $this->parseNumber($this->ulangan_3);

        $count = 0;
        $sum = 0;
        if ($val1 > 0) {
            $sum += $val1;
            $count++;
        }
        if ($val2 > 0) {
            $sum += $val2;
            $count++;
        }
        if ($val3 > 0) {
            $sum += $val3;
            $count++;
        }

        $this->kadar_air_rata_rata = $count > 0 ? round($sum / $count, 2) : 0;
    }

    // === HELPER: BERSIHKAN ANGKA ===
    private function parseNumber($value)
    {
        if (empty($value)) return 0;
        $cleanValue = str_replace(',', '.', $value);
        return floatval($cleanValue);
    }

    // === GENERATE NOMOR SURAT ===
    public function generateNomorSurat($preview = false)
    {
        $bulan = date('m');
        $tahun = date('Y');
        $groupCode = $this->group ?? '0000';

        $query = MasHpkkGabah::whereYear('tanggal_pelaksanaan', $tahun)
            ->where('group', $this->group);

        $count = $query->count();
        $nextNo = $count + 1;

        $noUrut = sprintf("%05d", $nextNo);
        $romawi = $this->getRomawi($bulan);

        $generated = "$noUrut/GPK/$groupCode/SCI/$romawi/$tahun";

        if ($preview) {
            $this->nomor_hpkk_gabah = $generated;
            return $generated;
        }

        $this->nomor_hpkk_gabah = $generated;
        return $generated;
    }

    private function getRomawi($bulan)
    {
        $bulan = (int)$bulan;
        $map = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $map[$bulan] ?? '';
    }

    // === SIMPAN DATA (STORE) ===
    public function store()
    {
        // 1. VALIDASI FORMAT (Laravel)
        try {
            $this->validate([
                'no_order_pembelian'           => 'required',
                'nomor_order'                  => 'required',
                'mitra'                        => 'required',
                'pengirim'                     => 'required',
                'tanggal_pelaksanaan'          => 'required|date',
                'jenis_alat_angkut'            => 'required',
                'nomor_registrasi_alat_angkut' => 'required',
                'hama_penyakit'                => 'required',
                'group'                        => 'required',
                'jumlah_timbangan'             => 'required|numeric',
                'metode_timbang'               => 'required',
                'kode_sample'                  => 'required',
                'ulangan_1'                    => 'required|numeric',
                'ulangan_2'                    => 'required|numeric',
                'ulangan_3'                    => 'required|numeric',
                'kadar_air_rata_rata'          => 'required|numeric',
                'kadar_hampa'                  => 'required|numeric',
                'butir_hijau'                  => 'required|numeric',
                'tanggal_doc'                  => 'required|date',
                'lokasi'                       => 'required',
                'mengetahui'                   => 'required',
                'petugas'                      => 'required',
                'catatan'                      => 'nullable',
            ]);
        } catch (ValidationException $e) {
            // SweetAlert Error jika form belum lengkap
            $this->dispatch('swal:error', [
                'title' => 'Gagal Disimpan!',
                'text'  => 'Mohon lengkapi semua form yang wajib diisi.',
            ]);
            throw $e;
        }

        // 2. VALIDASI LIMITER (SAFETY NET SAAT SAVE)
        $val_u1 = $this->parseNumber($this->ulangan_1);
        $val_u2 = $this->parseNumber($this->ulangan_2);
        $val_u3 = $this->parseNumber($this->ulangan_3);

        if (($val_u1 < 10 || $val_u1 > 38) ||
            ($val_u2 < 10 || $val_u2 > 38) ||
            ($val_u3 < 10 || $val_u3 > 38)
        ) {

            $this->dispatch('swal:error', ['title' => 'Data Invalid!', 'text' => 'Nilai Ulangan harus 10-38!']);
            return;
        }

        if ($this->parseNumber($this->kadar_hampa) > 40) {
            $this->dispatch('swal:error', ['title' => 'Data Invalid!', 'text' => 'Kadar Hampa > 40%!']);
            return;
        }

        if ($this->parseNumber($this->butir_hijau) > 30) {
            $this->dispatch('swal:error', ['title' => 'Data Invalid!', 'text' => 'Butir Hijau > 30%!']);
            return;
        }

        // 3. PROSES SIMPAN DB
        DB::beginTransaction();

        try {
            $finalNomorSurat = $this->generateNomorSurat(false);

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
                'kode_sample' => $this->kode_sample,
                'jumlah_timbangan' => $this->parseNumber($this->jumlah_timbangan),
                'ulangan_1' => $this->parseNumber($this->ulangan_1),
                'ulangan_2' => $this->parseNumber($this->ulangan_2),
                'ulangan_3' => $this->parseNumber($this->ulangan_3),
                'kadar_air_rata_rata' => $this->kadar_air_rata_rata ?? 0,
                'kadar_hampa' => $this->parseNumber($this->kadar_hampa),
                'butir_hijau' => $this->parseNumber($this->butir_hijau),
                'tanggal_doc' => $this->tanggal_doc,
                'lokasi' => $this->lokasi,
                'mengetahui' => $this->mengetahui,
                'petugas' => $this->petugas,
                'catatan' => $this->catatan,
                'group' => $this->group,
            ]);

            DB::commit();

            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'no_hpk'  => $finalNomorSurat,
                    'mitra'   => $this->mitra,
                    'petugas' => $this->petugas,
                    'lokasi'  => $this->lokasi,
                ])
                ->log('Input GKP');

            // SweetAlert Sukses
            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text'  => "Data Gabah disimpan dengan No: $finalNomorSurat",
            ]);

            $this->reset([
                'mitra',
                'pengirim',
                'jenis_alat_angkut',
                'nomor_registrasi_alat_angkut',
                'jumlah_timbangan',
                'ulangan_1',
                'ulangan_2',
                'ulangan_3',
                'kadar_air_rata_rata',
                'kadar_hampa',
                'butir_hijau',
                'no_order_pembelian',
                'nomor_order',
                'kode_sample',
                'catatan',
                'metode_timbang',
                'lokasi',
                'mengetahui',
                'petugas',
                'hama_penyakit'
            ]);

            $this->generateNomorSurat(true);
        } catch (\Exception $e) {
            DB::rollBack();
            // SweetAlert Error System
            $this->dispatch('swal:error', ['title' => 'Error System', 'text' => $e->getMessage()]);
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
