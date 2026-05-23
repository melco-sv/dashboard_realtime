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
        Schema::create('ref_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('value');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed tarif default
        DB::table('ref_settings')->insert([
            'key'         => 'tarif_bast',
            'value'       => '46.40',
            'description' => 'Tarif pemeriksaan BAST (Rp/Kg)',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_settings');
    }
};
