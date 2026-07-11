<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BastPejabatSetting extends Component
{
    public string $nama_kepala_unit = '';
    public string $nama_pimpinan_cabang = '';

    public function mount(): void
    {
        $group = Auth::user()->code_cabang;

        $kepala = DB::table('ref_settings')->where('key', "bast_kepala_unit_{$group}")->first();
        if ($kepala) $this->nama_kepala_unit = $kepala->value;

        $pimpinan = DB::table('ref_settings')->where('key', "bast_pimpinan_cabang_{$group}")->first();
        if ($pimpinan) $this->nama_pimpinan_cabang = $pimpinan->value;
    }

    public function save(): void
    {
        $group = Auth::user()->code_cabang;

        if (empty($group)) {
            $this->addError('nama_kepala_unit', 'Akun Anda tidak terhubung ke cabang manapun. Hubungi Super Admin.');
            return;
        }

        $this->validate([
            'nama_kepala_unit'     => 'nullable|string|max:255',
            'nama_pimpinan_cabang' => 'nullable|string|max:255',
        ], [
            'nama_kepala_unit.max'     => 'Nama Kepala Unit terlalu panjang (maksimal 255 karakter).',
            'nama_pimpinan_cabang.max' => 'Nama Pimpinan Cabang terlalu panjang (maksimal 255 karakter).',
        ]);

        $now = now();

        // created_at hanya diisi saat INSERT (record baru); pada UPDATE tidak
        // ditimpa lagi agar tanggal pembuatan asli tetap tersimpan.
        DB::table('ref_settings')->updateOrInsert(
            ['key' => "bast_kepala_unit_{$group}"],
            fn (bool $exists) => [
                'value'       => trim($this->nama_kepala_unit),
                'description' => "Nama Kepala Unit Pelayanan SUCOFINDO - Cabang {$group}",
                'updated_at'  => $now,
            ] + ($exists ? [] : ['created_at' => $now])
        );

        DB::table('ref_settings')->updateOrInsert(
            ['key' => "bast_pimpinan_cabang_{$group}"],
            fn (bool $exists) => [
                'value'       => trim($this->nama_pimpinan_cabang),
                'description' => "Nama Pimpinan Cabang Bulog - Cabang {$group}",
                'updated_at'  => $now,
            ] + ($exists ? [] : ['created_at' => $now])
        );

        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'code_cabang'          => $group,
                'nama_kepala_unit'     => $this->nama_kepala_unit,
                'nama_pimpinan_cabang' => $this->nama_pimpinan_cabang,
            ])
            ->log('Ubah Pejabat BAST');

        session()->flash('message', 'Data pejabat BAST berhasil disimpan.');
    }

    public function render()
    {
        $group = Auth::user()->code_cabang;

        $cabang = DB::table('ref_cabang')->where('code_cabang', $group)->first();

        $lastUpdatedRow = DB::table('ref_settings')
            ->where('key', "bast_kepala_unit_{$group}")
            ->first();

        return view('livewire.bast-pejabat-setting', [
            'namaCabang'  => $cabang->name_cabang ?? '-',
            'codeCabang'  => $group,
            'lastUpdated' => $lastUpdatedRow?->updated_at,
        ]);
    }
}
