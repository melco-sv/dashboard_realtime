<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan ref_cabang punya PRIMARY KEY (diperlukan agar bisa menjadi target FK)
        $hasPK = DB::select("SELECT COUNT(*) as cnt FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ref_cabang'
            AND CONSTRAINT_TYPE = 'PRIMARY KEY'")[0]->cnt;

        if (!$hasPK) {
            DB::statement('ALTER TABLE ref_cabang ADD PRIMARY KEY (code_cabang)');
        }

        // Seragamkan semua code_cabang ke INT UNSIGNED agar FK constraint bisa dibuat
        DB::statement('ALTER TABLE ref_cabang MODIFY code_cabang INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE mas_user MODIFY code_cabang INT UNSIGNED NULL');
        DB::statement('ALTER TABLE mas_hpkk_gabah MODIFY code_cabang INT UNSIGNED NULL');

        // Bersihkan orphaned records: set NULL untuk code_cabang yang tidak ada di ref_cabang
        DB::statement('UPDATE mas_user SET code_cabang = NULL
            WHERE code_cabang IS NOT NULL
            AND code_cabang NOT IN (SELECT code_cabang FROM ref_cabang)');

        DB::statement('UPDATE mas_hpkk_gabah SET code_cabang = NULL
            WHERE code_cabang IS NOT NULL
            AND code_cabang NOT IN (SELECT code_cabang FROM ref_cabang)');

        DB::statement('UPDATE mas_hpkk_beras SET code_cabang = NULL
            WHERE code_cabang IS NOT NULL
            AND code_cabang NOT IN (SELECT code_cabang FROM ref_cabang)');

        DB::statement('UPDATE ref_bast_status SET code_cabang = NULL
            WHERE code_cabang IS NOT NULL
            AND code_cabang NOT IN (SELECT code_cabang FROM ref_cabang)');

        DB::statement('UPDATE ref_upload SET code_cabang = NULL
            WHERE code_cabang IS NOT NULL
            AND code_cabang NOT IN (SELECT code_cabang FROM ref_cabang)');

        // ref_upload FK columns harus signed INT agar cocok dengan PK target (auto-increment signed INT)
        DB::statement('ALTER TABLE ref_upload MODIFY id_hpkk_gabah INT NULL');
        DB::statement('ALTER TABLE ref_upload MODIFY id_hpkk_beras INT NULL');

        // Bersihkan orphaned ref_upload juga
        DB::statement('UPDATE ref_upload SET id_hpkk_gabah = NULL
            WHERE id_hpkk_gabah IS NOT NULL
            AND id_hpkk_gabah NOT IN (SELECT id_hpkk_gabah FROM mas_hpkk_gabah)');
        DB::statement('UPDATE ref_upload SET id_hpkk_beras = NULL
            WHERE id_hpkk_beras IS NOT NULL
            AND id_hpkk_beras NOT IN (SELECT id_hpkk_beras FROM mas_hpkk_beras)');

        // Drop FK yang sudah ada (jika ada) agar migration bisa dijalankan ulang
        $existingFks = DB::select("SELECT TABLE_NAME, CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            AND TABLE_NAME IN ('mas_user','mas_hpkk_gabah','mas_hpkk_beras','ref_upload','ref_bast_status')");

        foreach ($existingFks as $fk) {
            DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // mas_user → ref_cabang
        Schema::table('mas_user', function (Blueprint $table) {
            $table->foreign('code_cabang')
                  ->references('code_cabang')->on('ref_cabang')
                  ->onDelete('restrict');
        });

        // mas_hpkk_gabah → ref_cabang
        Schema::table('mas_hpkk_gabah', function (Blueprint $table) {
            $table->foreign('code_cabang')
                  ->references('code_cabang')->on('ref_cabang')
                  ->onDelete('restrict');
        });

        // mas_hpkk_beras → ref_cabang
        Schema::table('mas_hpkk_beras', function (Blueprint $table) {
            $table->foreign('code_cabang')
                  ->references('code_cabang')->on('ref_cabang')
                  ->onDelete('restrict');
        });

        // ref_upload → mas_hpkk_gabah + mas_hpkk_beras
        Schema::table('ref_upload', function (Blueprint $table) {
            $table->foreign('id_hpkk_gabah')
                  ->references('id_hpkk_gabah')->on('mas_hpkk_gabah')
                  ->onDelete('cascade');
            $table->foreign('id_hpkk_beras')
                  ->references('id_hpkk_beras')->on('mas_hpkk_beras')
                  ->onDelete('cascade');
        });

        // ref_bast_status → ref_cabang
        Schema::table('ref_bast_status', function (Blueprint $table) {
            $table->foreign('code_cabang')
                  ->references('code_cabang')->on('ref_cabang')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('ref_bast_status', function (Blueprint $table) {
            $table->dropForeign(['code_cabang']);
        });

        Schema::table('ref_upload', function (Blueprint $table) {
            $table->dropForeign(['id_hpkk_gabah']);
            $table->dropForeign(['id_hpkk_beras']);
        });

        Schema::table('mas_hpkk_beras', function (Blueprint $table) {
            $table->dropForeign(['code_cabang']);
        });

        Schema::table('mas_hpkk_gabah', function (Blueprint $table) {
            $table->dropForeign(['code_cabang']);
        });

        Schema::table('mas_user', function (Blueprint $table) {
            $table->dropForeign(['code_cabang']);
        });
    }
};
