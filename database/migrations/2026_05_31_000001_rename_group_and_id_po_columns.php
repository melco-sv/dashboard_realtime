<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // mas_user: group → code_cabang
        Schema::table('mas_user', function (Blueprint $table) {
            $table->renameColumn('group', 'code_cabang');
        });

        // mas_hpkk_gabah: group → code_cabang, id_po → id_hpkk_gabah
        Schema::table('mas_hpkk_gabah', function (Blueprint $table) {
            $table->renameColumn('group', 'code_cabang');
            $table->renameColumn('id_po', 'id_hpkk_gabah');
        });

        // mas_hpkk_beras: group → code_cabang
        Schema::table('mas_hpkk_beras', function (Blueprint $table) {
            $table->renameColumn('group', 'code_cabang');
        });

        // ref_upload: group → code_cabang
        Schema::table('ref_upload', function (Blueprint $table) {
            $table->renameColumn('group', 'code_cabang');
        });
    }

    public function down(): void
    {
        Schema::table('mas_user', function (Blueprint $table) {
            $table->renameColumn('code_cabang', 'group');
        });

        Schema::table('mas_hpkk_gabah', function (Blueprint $table) {
            $table->renameColumn('code_cabang', 'group');
            $table->renameColumn('id_hpkk_gabah', 'id_po');
        });

        Schema::table('mas_hpkk_beras', function (Blueprint $table) {
            $table->renameColumn('code_cabang', 'group');
        });

        Schema::table('ref_upload', function (Blueprint $table) {
            $table->renameColumn('code_cabang', 'group');
        });
    }
};
