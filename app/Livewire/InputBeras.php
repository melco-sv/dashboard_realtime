<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\MasHpkkBeras;
use App\Models\RefUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InputBeras extends Component
{
    use WithFileUploads;

    // === PROPERTI FORM ===
    public $nomor_hpkk_beras;
    public $id_mo;
    public $nomor_order;
    public $tempat_pemeriksaan;
    public $tanggal_pemeriksaan;
    public $kode_sample;
    public $dasar_pemeriksaan;
    public $kondisi_kemasan;
    public $hama;
    public $dedak_katul_sekam;
    public $bau;
    public $bahan_kimia;
    public $ulangan_1;
    public $ulangan_2;
    public $ulangan_3;
    public $rata_rata;
    public $derajat_sosoh;
    public $butir_patah;
    public $menir;
    public $kuantum_gabah_sesuai_mo;
    public $kuantum_beras;
    public $rendemen_pengolahan;
    public $hasil_samping_menir;
    public $hasil_samping_butir_patah;
    public $hasil_samping_dedak_katul;
    public $hasil_samping_butir_kuning_rusak;
    public $tanggal_doc;
    public $lokasi;
    public $mengetahui;
    public $petugas;
    public $catatan;
    public $code_cabang;
    public $status = 'Active';

    // === FOTO DOKUMENTASI (upload langsung di form) ===
    public $fotos = [];      // [key => UploadedFile]
    public $fotoNama = [];   // [key => string]
    public $fotoRows = [];   // urutan key baris foto
    public $fotoSeq = 0;     // counter key unik

    public function mount()
    {
        $this->tanggal_pemeriksaan = date('Y-m-d');
        $this->tanggal_doc = date('Y-m-d');

        if (Auth::check()) {
            $this->code_cabang = Auth::user()->code_cabang;
        }

        $this->generateNomorSurat(true);

        $this->addFotoRow();
    }

    // === FOTO: tambah / hapus baris ===
    public function addFotoRow()
    {
        $this->fotoRows[] = $this->fotoSeq;
        $this->fotoSeq++;
    }

    public function removeFotoRow($key)
    {
        $this->fotoRows = array_values(array_filter($this->fotoRows, fn ($k) => $k !== $key));
        unset($this->fotos[$key], $this->fotoNama[$key]);
    }

    // === REAL-TIME VALIDATION (SWEETALERT & AUTO RESET) ===
    public function updated($propertyName)
    {
        // Lewati properti foto (fotos.*, fotoNama.*) — bukan input numerik.
        if (str_starts_with($propertyName, 'foto')) {
            return;
        }

        $val = $this->parseNumber($this->{$propertyName});

        // 1. CEK ULANGAN 1, 2, 3 (Range 10.01 - 14)
        if (in_array($propertyName, ['ulangan_1', 'ulangan_2', 'ulangan_3'])) {
            if ($val > 0 && ($val < 10 || $val > 14)) {
                // Tampilkan SweetAlert Error
                $this->dispatch('swal:error', [
                    'title' => 'Input Diluar Batas!',
                    'text'  => "Nilai $propertyName harus diantara 10 s/d 14!",
                ]);

                // Auto Reset: Kosongkan inputan
                $this->{$propertyName} = null;
            }
            $this->hitungRataRata();
        }

        // 2. CEK DERAJAT SOSOH (Harus 85, 95, 100)
        if ($propertyName == 'derajat_sosoh') {
            if ($val > 0 && !in_array($val, [95, 100])) {
                $this->dispatch('swal:error', [
                    'title' => 'Data Invalid!',
                    'text'  => 'Derajat Sosoh hanya boleh bernilai 95, atau 100!',
                ]);

                // Auto Reset
                $this->derajat_sosoh = null;
            }
        }

        // 3. CEK BUTIR PATAH (Max 40)
        if ($propertyName == 'butir_patah') {
            if ($val > 25) {
                $this->dispatch('swal:error', [
                    'title' => 'Input Diluar Batas!',
                    'text'  => 'Butir Patah tidak boleh melebihi 25%!',
                ]);

                // Auto Reset
                $this->butir_patah = null;
            }
        }

        // 4. CEK MENIR (Max 5)
        if ($propertyName == 'menir') {
            if ($val > 2) {
                $this->dispatch('swal:error', [
                    'title' => 'Input Diluar Batas!',
                    'text'  => 'Menir tidak boleh melebihi 2%!',
                ]);

                // Auto Reset
                $this->menir = null;
            }
        }

        // 5. Hitung Rendemen
        if (in_array($propertyName, ['kuantum_beras', 'kuantum_gabah_sesuai_mo'])) {
            $this->hitungRendemen();
        }
    }

    // === LOGIKA HITUNGAN ===
    private function hitungRataRata()
    {
        $u1 = $this->parseNumber($this->ulangan_1);
        $u2 = $this->parseNumber($this->ulangan_2);
        $u3 = $this->parseNumber($this->ulangan_3);

        $count = 0;
        $sum = 0;
        if ($u1 > 0) {
            $sum += $u1;
            $count++;
        }
        if ($u2 > 0) {
            $sum += $u2;
            $count++;
        }
        if ($u3 > 0) {
            $sum += $u3;
            $count++;
        }

        $this->rata_rata = $count > 0 ? round($sum / $count, 2) : 0;
    }

    private function hitungRendemen()
    {
        $beras = $this->parseNumber($this->kuantum_beras);
        $gabah = $this->parseNumber($this->kuantum_gabah_sesuai_mo);

        if ($gabah > 0 && $beras > 0) {
            $rendemen = ($beras / $gabah) * 100;
            $this->rendemen_pengolahan = round($rendemen, 2);
        } else {
            $this->rendemen_pengolahan = 0;
        }
    }

    // Helper: Membersihkan input angka
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
        $groupCode = $this->code_cabang ?? '0000';

        $query = MasHpkkBeras::whereYear('tanggal_pemeriksaan', $tahun)
            ->where('code_cabang', $this->code_cabang);

        $count = $query->count();
        $nextNo = $count + 1;

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

    private function getRomawi($bulan)
    {
        $bulan = (int)$bulan;
        $map = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $map[$bulan] ?? '';
    }

    // === SIMPAN DATA (STORE) ===
    public function store()
    {
        try {
            $this->validate([
                'id_mo' => 'required',
                'nomor_order' => 'required',
                'tempat_pemeriksaan' => 'required',
                'tanggal_pemeriksaan' => 'required|date',
                'kode_sample' => 'required',
                'dasar_pemeriksaan' => 'required',
                'code_cabang' => 'required',
                'kondisi_kemasan' => 'required',
                'hama' => 'required',
                'dedak_katul_sekam' => 'required',
                'bau' => 'required',
                'bahan_kimia' => 'required',
                'ulangan_1' => 'required|numeric',
                'ulangan_2' => 'required|numeric',
                'ulangan_3' => 'required|numeric',
                'derajat_sosoh' => 'required|numeric',
                'butir_patah' => 'required|numeric',
                'menir' => 'required|numeric',
                'kuantum_gabah_sesuai_mo' => 'required|numeric',
                'kuantum_beras' => 'required|numeric',
                'hasil_samping_menir' => 'required|numeric',
                'hasil_samping_butir_patah' => 'required|numeric',
                'hasil_samping_dedak_katul' => 'required|numeric',
                'hasil_samping_butir_kuning_rusak' => 'required|numeric',
                'tanggal_doc' => 'required|date',
                'lokasi' => 'required',
                'mengetahui' => 'required',
                'petugas' => 'required',
                'catatan' => 'nullable',
                'fotos.*' => 'nullable|image|max:10240',
            ]);
        } catch (ValidationException $e) {
            // SweetAlert Error Form Belum Lengkap
            $this->dispatch('swal:error', [
                'title' => 'Gagal Disimpan!',
                'text'  => 'Mohon lengkapi semua form yang wajib diisi.',
            ]);
            throw $e;
        }

        // ============================================================
        // 2. VALIDASI LIMITER (SAFETY NET SAAT SAVE)
        // ============================================================

        $val_u1 = $this->parseNumber($this->ulangan_1);
        $val_u2 = $this->parseNumber($this->ulangan_2);
        $val_u3 = $this->parseNumber($this->ulangan_3);

        if (($val_u1 < 10 || $val_u1 > 14) ||
            ($val_u2 < 10 || $val_u2 > 14) ||
            ($val_u3 < 10 || $val_u3 > 14)
        ) {

            $this->dispatch('swal:error', ['title' => 'Data Invalid!', 'text' => 'Nilai Ulangan harus 10 - 14!']);
            return;
        }

        if (!in_array($this->parseNumber($this->derajat_sosoh), [95, 100])) {
            $this->dispatch('swal:error', ['title' => 'Data Invalid!', 'text' => 'Derajat Sosoh harus  95, atau 100!']);
            return;
        }

        if ($this->parseNumber($this->butir_patah) > 40) {
            $this->dispatch('swal:error', ['title' => 'Data Invalid!', 'text' => 'Butir Patah melebihi 40%!']);
            return;
        }

        if ($this->parseNumber($this->menir) > 2) {
            $this->dispatch('swal:error', ['title' => 'Data Invalid!', 'text' => 'Menir melebihi 2%!']);
            return;
        }

        // ============================================================
        // 3. PROSES SIMPAN DB
        // ============================================================

        DB::beginTransaction();

        try {
            $finalNomorSurat = $this->generateNomorSurat(false);

            $data = [
                'nomor_hpkk_beras' => $finalNomorSurat,
                'id_mo' => $this->id_mo,
                'nomor_order' => $this->nomor_order,
                'tempat_pemeriksaan' => $this->tempat_pemeriksaan,
                'tanggal_pemeriksaan' => $this->tanggal_pemeriksaan,
                'kode_sample' => $this->kode_sample,
                'dasar_pemeriksaan' => $this->dasar_pemeriksaan,
                'kondisi_kemasan' => $this->kondisi_kemasan,
                'hama' => $this->hama,
                'dedak_katul_sekam' => $this->dedak_katul_sekam,
                'bau' => $this->bau,
                'bahan_kimia' => $this->bahan_kimia,
                'ulangan_1' => $this->parseNumber($this->ulangan_1),
                'ulangan_2' => $this->parseNumber($this->ulangan_2),
                'ulangan_3' => $this->parseNumber($this->ulangan_3),
                'rata_rata' => $this->rata_rata ?? 0,
                'derajat_sosoh' => $this->parseNumber($this->derajat_sosoh),
                'butir_patah' => $this->parseNumber($this->butir_patah),
                'menir' => $this->parseNumber($this->menir),
                'kuantum_gabah_sesuai_mo' => $this->parseNumber($this->kuantum_gabah_sesuai_mo),
                'kuantum_beras' => $this->parseNumber($this->kuantum_beras),
                'rendemen_pengolahan' => $this->rendemen_pengolahan ?? 0,
                'hasil_samping_menir' => $this->parseNumber($this->hasil_samping_menir),
                'hasil_samping_butir_patah' => $this->parseNumber($this->hasil_samping_butir_patah),
                'hasil_samping_dedak_katul' => $this->parseNumber($this->hasil_samping_dedak_katul),
                'hasil_samping_butir_kuning_rusak' => $this->parseNumber($this->hasil_samping_butir_kuning_rusak),
                'tanggal_doc' => $this->tanggal_doc,
                'lokasi' => $this->lokasi,
                'mengetahui' => $this->mengetahui,
                'petugas' => $this->petugas,
                'catatan' => $this->catatan,
                'code_cabang' => $this->code_cabang,
                'status' => $this->status,
            ];

            $record = MasHpkkBeras::create($data);

            // Simpan foto dokumentasi yang diupload langsung di form
            foreach ($this->fotoRows as $key) {
                if (!empty($this->fotos[$key])) {
                    $path = $this->fotos[$key]->store('photos', 'public');
                    RefUpload::create([
                        'id_hpkk_beras' => $record->id_hpkk_beras,
                        'nama'          => (isset($this->fotoNama[$key]) && trim($this->fotoNama[$key]) !== '') ? $this->fotoNama[$key] : 'Dokumentasi',
                        'file'          => $path,
                        'code_cabang'   => $this->code_cabang,
                    ]);
                }
            }

            DB::commit();

            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'no_lhpk'          => $finalNomorSurat,
                    'no_mo'            => $this->id_mo,
                    'tempat'           => $this->tempat_pemeriksaan,
                    'petugas'          => $this->petugas,
                ])
                ->log('Input HGL');

            // SweetAlert Sukses
            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text'  => "Data Beras disimpan dengan No: $finalNomorSurat",
            ]);

            // Reset Form
            $this->reset([
                'id_mo',
                'nomor_order',
                'tempat_pemeriksaan',
                'kode_sample',
                'dasar_pemeriksaan',
                'kondisi_kemasan',
                'hama',
                'dedak_katul_sekam',
                'bau',
                'bahan_kimia',
                'ulangan_1',
                'ulangan_2',
                'ulangan_3',
                'rata_rata',
                'derajat_sosoh',
                'butir_patah',
                'menir',
                'kuantum_gabah_sesuai_mo',
                'kuantum_beras',
                'rendemen_pengolahan',
                'hasil_samping_menir',
                'hasil_samping_butir_patah',
                'hasil_samping_dedak_katul',
                'hasil_samping_butir_kuning_rusak',
                'catatan',
                'lokasi',
                'mengetahui',
                'petugas'
            ]);

            $this->reset(['fotos', 'fotoNama', 'fotoRows', 'fotoSeq']);
            $this->addFotoRow();

            $this->generateNomorSurat(true);
        } catch (\Exception $e) {
            DB::rollBack();
            // SweetAlert Error System
            $this->dispatch('swal:error', ['title' => 'Error System', 'text' => $e->getMessage()]);
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
