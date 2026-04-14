<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ref_upload')) {
            Schema::create('ref_upload', function (Blueprint $table) {
                $table->bigIncrements('id_upload');
                $table->unsignedBigInteger('id_hpkk_gabah')->nullable();
                $table->unsignedBigInteger('id_hpkk_beras')->nullable();
                $table->string('nama');
                $table->string('file');
                $table->string('group')->nullable();
            });
        } else {
            // Tabel sudah ada — tambahkan kolom id_hpkk_beras jika belum ada
            if (!Schema::hasColumn('ref_upload', 'id_hpkk_beras')) {
                Schema::table('ref_upload', function (Blueprint $table) {
                    $table->unsignedBigInteger('id_hpkk_beras')->nullable()->after('id_hpkk_gabah');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_upload');
    }
};
