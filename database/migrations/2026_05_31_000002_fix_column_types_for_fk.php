<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ref_upload: BIGINT → INT agar cocok dengan PK inspeksi (INT)
        Schema::table('ref_upload', function (Blueprint $table) {
            $table->unsignedInteger('id_hpkk_gabah')->nullable()->change();
            $table->unsignedInteger('id_hpkk_beras')->nullable()->change();
        });

        // ref_bast_status: code_cabang VARCHAR → INT agar cocok dengan ref_cabang.code_cabang (INT)
        // Konversi data yang ada terlebih dahulu
        DB::statement('ALTER TABLE ref_bast_status MODIFY code_cabang INT UNSIGNED NOT NULL');

        // mas_hpkk_beras: code_cabang VARCHAR(50) → INT, periode VARCHAR(20) → INT
        DB::statement('ALTER TABLE mas_hpkk_beras MODIFY code_cabang INT UNSIGNED NULL');
        DB::statement('ALTER TABLE mas_hpkk_beras MODIFY periode INT NULL');

        // ref_cabang: hapus kolom id_ref_cabang yang tidak terpakai
        if (Schema::hasColumn('ref_cabang', 'id_ref_cabang')) {
            Schema::table('ref_cabang', function (Blueprint $table) {
                $table->dropColumn('id_ref_cabang');
            });
        }
    }

    public function down(): void
    {
        Schema::table('ref_upload', function (Blueprint $table) {
            $table->unsignedBigInteger('id_hpkk_gabah')->nullable()->change();
            $table->unsignedBigInteger('id_hpkk_beras')->nullable()->change();
        });

        DB::statement('ALTER TABLE ref_bast_status MODIFY code_cabang VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE mas_hpkk_beras MODIFY code_cabang VARCHAR(50) NULL');
        DB::statement('ALTER TABLE mas_hpkk_beras MODIFY periode VARCHAR(20) NULL');

        Schema::table('ref_cabang', function (Blueprint $table) {
            $table->integer('id_ref_cabang')->nullable()->after('code_cabang');
        });
    }
};
