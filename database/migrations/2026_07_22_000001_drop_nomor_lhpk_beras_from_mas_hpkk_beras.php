<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mas_hpkk_beras', function (Blueprint $table) {
            $table->dropColumn('nomor_lhpk_beras');
        });
    }

    public function down(): void
    {
        Schema::table('mas_hpkk_beras', function (Blueprint $table) {
            $table->string('nomor_lhpk_beras', 100)->nullable();
        });
    }
};
