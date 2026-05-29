<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ref_bast_status', function (Blueprint $table) {
            $table->id();
            $table->string('code_cabang');
            $table->enum('jenis', ['HGL', 'GKP']);
            $table->date('tgl_mulai');
            $table->date('tgl_akhir');
            $table->enum('status', ['BELUM DIBAYAR', 'DIBAYAR'])->default('BELUM DIBAYAR');
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->unique(['code_cabang', 'jenis', 'tgl_mulai', 'tgl_akhir'], 'unique_bast_periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_bast_status');
    }
};
