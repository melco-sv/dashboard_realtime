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
        Schema::table('ref_bast_status', function (Blueprint $table) {
            $table->string('nomor_surat', 100)->nullable()->after('tgl_akhir');
        });
    }

    public function down(): void
    {
        Schema::table('ref_bast_status', function (Blueprint $table) {
            $table->dropColumn('nomor_surat');
        });
    }
};
