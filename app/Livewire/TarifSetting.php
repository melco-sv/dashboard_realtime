<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TarifSetting extends Component
{
    public string $tarif_bast = '46.40';

    public function mount(): void
    {
        $row = DB::table('ref_settings')->where('key', 'tarif_bast')->first();
        if ($row) {
            $this->tarif_bast = $row->value;
        }
    }

    public function save(): void
    {
        $this->validate([
            'tarif_bast' => 'required|numeric|min:0',
        ], [
            'tarif_bast.required' => 'Tarif tidak boleh kosong.',
            'tarif_bast.numeric'  => 'Tarif harus berupa angka.',
            'tarif_bast.min'      => 'Tarif tidak boleh negatif.',
        ]);

        DB::table('ref_settings')->updateOrInsert(
            ['key' => 'tarif_bast'],
            [
                'value'       => number_format((float) $this->tarif_bast, 2, '.', ''),
                'description' => 'Tarif pemeriksaan BAST (Rp/Kg)',
                'updated_at'  => now(),
                'created_at'  => now(),
            ]
        );

        // Hapus cache agar BastBeras/BastGabah langsung pakai tarif baru
        Cache::forget('tarif_bast');

        activity()
            ->causedBy(\Illuminate\Support\Facades\Auth::user())
            ->withProperties(['tarif_baru' => $this->tarif_bast])
            ->log('Ubah Tarif BAST');

        session()->flash('message', 'Tarif berhasil disimpan: Rp ' . number_format((float) $this->tarif_bast, 2, ',', '.') . '/Kg');
    }

    public function render()
    {
        $setting = DB::table('ref_settings')->where('key', 'tarif_bast')->first();

        return view('livewire.tarif-setting', [
            'lastUpdated' => $setting?->updated_at,
        ]);
    }
}
