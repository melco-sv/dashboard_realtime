<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TarifSetting extends Component
{
    public string $tarif_bast_gabah = '46.40';
    public string $tarif_bast_beras = '46.40';

    public function mount(): void
    {
        $gabah = DB::table('ref_settings')->where('key', 'tarif_bast_gabah')->first();
        if ($gabah) $this->tarif_bast_gabah = $gabah->value;

        $beras = DB::table('ref_settings')->where('key', 'tarif_bast_beras')->first();
        if ($beras) $this->tarif_bast_beras = $beras->value;
    }

    public function save(): void
    {
        $this->validate([
            'tarif_bast_gabah' => 'required|numeric|min:0',
            'tarif_bast_beras' => 'required|numeric|min:0',
        ], [
            'tarif_bast_gabah.required' => 'Tarif Gabah tidak boleh kosong.',
            'tarif_bast_gabah.numeric'  => 'Tarif Gabah harus berupa angka.',
            'tarif_bast_gabah.min'      => 'Tarif Gabah tidak boleh negatif.',
            'tarif_bast_beras.required' => 'Tarif Beras tidak boleh kosong.',
            'tarif_bast_beras.numeric'  => 'Tarif Beras harus berupa angka.',
            'tarif_bast_beras.min'      => 'Tarif Beras tidak boleh negatif.',
        ]);

        $now = now();

        DB::table('ref_settings')->updateOrInsert(
            ['key' => 'tarif_bast_gabah'],
            ['value' => number_format((float) $this->tarif_bast_gabah, 2, '.', ''), 'description' => 'Tarif BAST GKP/Gabah (Rp/Kg)', 'updated_at' => $now, 'created_at' => $now]
        );

        DB::table('ref_settings')->updateOrInsert(
            ['key' => 'tarif_bast_beras'],
            ['value' => number_format((float) $this->tarif_bast_beras, 2, '.', ''), 'description' => 'Tarif BAST HGL/Beras (Rp/Kg)', 'updated_at' => $now, 'created_at' => $now]
        );

        Cache::forget('tarif_bast_gabah');
        Cache::forget('tarif_bast_beras');

        activity()
            ->causedBy(\Illuminate\Support\Facades\Auth::user())
            ->withProperties(['tarif_gabah' => $this->tarif_bast_gabah, 'tarif_beras' => $this->tarif_bast_beras])
            ->log('Ubah Tarif BAST');

        session()->flash('message', 'Tarif berhasil disimpan — Gabah: Rp ' . number_format((float) $this->tarif_bast_gabah, 2, ',', '.') . '/Kg | Beras: Rp ' . number_format((float) $this->tarif_bast_beras, 2, ',', '.') . '/Kg');
    }

    public function render()
    {
        $gabahRow = DB::table('ref_settings')->where('key', 'tarif_bast_gabah')->first();

        return view('livewire.tarif-setting', [
            'lastUpdated' => $gabahRow?->updated_at,
        ]);
    }
}
