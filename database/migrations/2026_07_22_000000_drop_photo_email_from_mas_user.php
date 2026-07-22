<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // mas_user — kolom photo & email tidak lagi dipakai di form manapun
        Schema::table('mas_user', function (Blueprint $table) {
            $table->dropColumn([
                'photo',
                'email',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('mas_user', function (Blueprint $table) {
            $table->string('photo', 255)->nullable();
            $table->string('email', 100)->nullable();
        });
    }
};
