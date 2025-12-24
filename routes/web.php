<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Import Livewire Components
use App\Livewire\Auth\Login;
use App\Livewire\DashboardGabah;
use App\Livewire\Serapan;
use App\Livewire\UpdateProfile; // <--- Import Component UpdateProfile

// Gabah Components
use App\Livewire\InputGabah;
use App\Livewire\ListGabah;
use App\Livewire\EditGabah;
use App\Livewire\UploadFotoGabah;
use App\Http\Controllers\GabahPdfController;

// Beras Components
use App\Livewire\InputBeras;
use App\Livewire\ListBeras;
use App\Livewire\UploadFotoBeras;
use App\Http\Controllers\BerasPdfController;

// Laporan Components
use App\Livewire\LaporanGkp;
use App\Livewire\LaporanHgl;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. TAMU (GUEST) - Belum Login
// ==========================================
Route::middleware('guest')->group(function () {
    // Halaman utama adalah Login
    Route::get('/', Login::class)->name('login');
});

// ==========================================
// 2. USER LOGIN (AUTHENTICATED)
// ==========================================
Route::middleware('auth')->group(function () {

    // Logout Route
    Route::get('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');

    // --- HALAMAN UMUM (Bisa diakses Inspektor, Super Admin, Verification) ---
    
    // Dashboard
    Route::get('/dashboard', DashboardGabah::class)->name('dashboard'); 
    
    // Halaman Serapan
    Route::get('/serapan', Serapan::class)->name('serapan');

    // Halaman Settings / Update Profile (BARU)
    Route::get('/settings', UpdateProfile::class)->name('settings');


    // --- HALAMAN KHUSUS INSPEKTOR ---
    // (Input, Edit, List, Upload, Laporan)
    // Middleware 'role:inspektor' memblokir Admin/Verifikator masuk sini
    Route::middleware(['role:inspektor'])->group(function () {
        
        // Operasional Gabah
        Route::get('/input-gabah', InputGabah::class)->name('input.gabah');
        Route::get('/list-gabah', ListGabah::class)->name('list.gabah');
        Route::get('/edit-gabah/{id}', EditGabah::class)->name('edit.gabah');
        Route::get('/upload-foto-gabah/{id}', UploadFotoGabah::class)->name('upload.gabah');
        Route::get('/print/gabah/{id}/{type}', [GabahPdfController::class, 'print'])->name('print.gabah');

        // Operasional Beras
        Route::get('/input-beras', InputBeras::class)->name('input.beras');
        Route::get('/list-beras', ListBeras::class)->name('list.beras');
        // Route::get('/edit-beras/{id}', EditBeras::class)->name('edit.beras'); // Aktifkan jika ada componentnya
        Route::get('/upload-foto-beras/{id}', UploadFotoBeras::class)->name('upload.beras');
        Route::get('/print/beras/{id}/{type}', [BerasPdfController::class, 'print'])->name('print.beras');

        // Laporan
        Route::get('/laporan-gkp', LaporanGkp::class)->name('laporan.gkp');
        Route::get('/laporan-hgl', LaporanHgl::class)->name('laporan.hgl');
    });

});