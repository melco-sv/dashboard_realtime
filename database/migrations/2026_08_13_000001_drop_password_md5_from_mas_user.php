<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // mas_user — kolom legacy password_md5 tidak pernah dibaca saat login.
        // Verifikasi dua format (bcrypt / MD5) dilakukan pada kolom `password`
        // itu sendiri, jadi kolom ini hanya menyimpan hash lemah tanpa guna.
        //
        // hasColumn dicek dulu karena di server produksi kolom ini sudah lebih
        // dulu di-drop lewat SQL manual di phpMyAdmin — tanpa penjagaan ini,
        // migrasi akan gagal di sana.
        if (Schema::hasColumn('mas_user', 'password_md5')) {
            Schema::table('mas_user', function (Blueprint $table) {
                $table->dropColumn('password_md5');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('mas_user', 'password_md5')) {
            Schema::table('mas_user', function (Blueprint $table) {
                $table->string('password_md5', 255)->nullable();
            });
        }
    }
};
