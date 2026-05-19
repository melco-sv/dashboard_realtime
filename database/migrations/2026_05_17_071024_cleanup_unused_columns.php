<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // mas_hpkk_gabah — 13 kolom tidak dipakai di kode manapun
        Schema::table('mas_hpkk_gabah', function (Blueprint $table) {
            $table->dropColumn([
                'tempat_pelaksanaan',
                'weighbridge',
                'non_weighbridge',
                'no_lhpk_gabah',
                'tgl_selesai',
                'kesimpulan',
                'jenis_pemeriksaan',
                'nomor_referensi_lhpk_komersial',
                'nama_kepala_gudang',
                'rencana_kuantum',
                'hasil_kuantum',
                'id_timbang',
                'id_tempat_timbang',
            ]);
        });

        // ref_cabang — 2 kolom tidak dipakai (company_name & cabang_sci dipertahankan)
        Schema::table('ref_cabang', function (Blueprint $table) {
            $table->dropColumn([
                'kode_bulog',
                'id_kota_cabang',
            ]);
        });

        // mas_user — 2 kolom tidak dipakai (photo dipertahankan untuk fitur yang akan diperbaiki)
        Schema::table('mas_user', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'nama_client',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('mas_hpkk_gabah', function (Blueprint $table) {
            $table->string('tempat_pelaksanaan')->nullable();
            $table->string('weighbridge')->nullable();
            $table->string('non_weighbridge')->nullable();
            $table->string('no_lhpk_gabah')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->text('kesimpulan')->nullable();
            $table->string('jenis_pemeriksaan')->nullable();
            $table->string('nomor_referensi_lhpk_komersial')->nullable();
            $table->string('nama_kepala_gudang')->nullable();
            $table->string('rencana_kuantum')->nullable();
            $table->string('hasil_kuantum')->nullable();
            $table->string('id_timbang')->nullable();
            $table->string('id_tempat_timbang')->nullable();
        });

        Schema::table('ref_cabang', function (Blueprint $table) {
            $table->string('kode_bulog')->nullable();
            $table->string('id_kota_cabang')->nullable();
        });

        Schema::table('mas_user', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('nama_client')->nullable();
        });
    }
};
