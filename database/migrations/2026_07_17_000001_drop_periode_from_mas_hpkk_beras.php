<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom 'periode' tidak dipakai aplikasi (nilai turunan dari tanggal
 * pemeriksaan) dan sudah dihapus manual di salah satu device.
 * Migration ini menyeragamkan skema di semua device: hapus bila masih ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('mas_hpkk_beras', 'periode')) {
            Schema::table('mas_hpkk_beras', function (Blueprint $table) {
                $table->dropColumn('periode');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('mas_hpkk_beras', 'periode')) {
            Schema::table('mas_hpkk_beras', function (Blueprint $table) {
                $table->integer('periode')->nullable();
            });
        }
    }
};
