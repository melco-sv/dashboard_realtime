<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Index tambahan murni untuk performa (additive, tidak mengubah data/skema kolom).
 *
 * Latar belakang: mas_hpkk_gabah (±95 ribu baris) dan mas_hpkk_beras (±41 ribu baris)
 * difilter di hampir semua halaman berdasarkan kolom tanggal (BAST, Laporan, Dashboard,
 * Verifikasi) dan kombinasi cabang+tanggal (Inspektor), namun sebelumnya hanya punya
 * index PRIMARY + FK code_cabang. Kolom status_data/status dipakai penghitungan
 * notifikasi (ApprovalNotif) yang di-poll tiap 30 detik.
 *
 * ref_bast_status sengaja TIDAK diberi index baru: tabelnya kecil dan sudah punya
 * unique index (code_cabang, jenis, tgl_mulai, tgl_akhir).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mas_hpkk_gabah', function (Blueprint $table) {
            if (!Schema::hasIndex('mas_hpkk_gabah', 'idx_hpkk_gabah_tanggal')) {
                $table->index('tanggal_pelaksanaan', 'idx_hpkk_gabah_tanggal');
            }
            if (!Schema::hasIndex('mas_hpkk_gabah', 'idx_hpkk_gabah_cabang_tanggal')) {
                $table->index(['code_cabang', 'tanggal_pelaksanaan'], 'idx_hpkk_gabah_cabang_tanggal');
            }
            if (!Schema::hasIndex('mas_hpkk_gabah', 'idx_hpkk_gabah_status_data')) {
                $table->index('status_data', 'idx_hpkk_gabah_status_data');
            }
        });

        Schema::table('mas_hpkk_beras', function (Blueprint $table) {
            if (!Schema::hasIndex('mas_hpkk_beras', 'idx_hpkk_beras_tanggal')) {
                $table->index('tanggal_pemeriksaan', 'idx_hpkk_beras_tanggal');
            }
            if (!Schema::hasIndex('mas_hpkk_beras', 'idx_hpkk_beras_cabang_tanggal')) {
                $table->index(['code_cabang', 'tanggal_pemeriksaan'], 'idx_hpkk_beras_cabang_tanggal');
            }
            if (!Schema::hasIndex('mas_hpkk_beras', 'idx_hpkk_beras_status')) {
                $table->index('status', 'idx_hpkk_beras_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mas_hpkk_gabah', function (Blueprint $table) {
            if (Schema::hasIndex('mas_hpkk_gabah', 'idx_hpkk_gabah_tanggal')) {
                $table->dropIndex('idx_hpkk_gabah_tanggal');
            }
            if (Schema::hasIndex('mas_hpkk_gabah', 'idx_hpkk_gabah_cabang_tanggal')) {
                $table->dropIndex('idx_hpkk_gabah_cabang_tanggal');
            }
            if (Schema::hasIndex('mas_hpkk_gabah', 'idx_hpkk_gabah_status_data')) {
                $table->dropIndex('idx_hpkk_gabah_status_data');
            }
        });

        Schema::table('mas_hpkk_beras', function (Blueprint $table) {
            if (Schema::hasIndex('mas_hpkk_beras', 'idx_hpkk_beras_tanggal')) {
                $table->dropIndex('idx_hpkk_beras_tanggal');
            }
            if (Schema::hasIndex('mas_hpkk_beras', 'idx_hpkk_beras_cabang_tanggal')) {
                $table->dropIndex('idx_hpkk_beras_cabang_tanggal');
            }
            if (Schema::hasIndex('mas_hpkk_beras', 'idx_hpkk_beras_status')) {
                $table->dropIndex('idx_hpkk_beras_status');
            }
        });
    }
};
