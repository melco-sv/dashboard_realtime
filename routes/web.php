<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Import Livewire Components (Auth)
use App\Livewire\Auth\Login;

// Import Livewire Components (Dashboard & Umum)
use App\Livewire\DashboardGabah;
use App\Livewire\Serapan;
use App\Livewire\UpdateProfile;

// Import Livewire Components (Gabah)
use App\Livewire\InputGabah;
use App\Livewire\ListGabah;
use App\Livewire\EditGabah;
use App\Livewire\UploadFotoGabah;
use App\Livewire\ViewFotoGabah;
use App\Livewire\BastBeras;
use App\Livewire\BastGabah;
use App\Http\Controllers\GabahPdfController;
use App\Http\Controllers\BastBerasPdfController;
use App\Http\Controllers\BastGabahPdfController;

// Import Livewire Components (Beras)
use App\Livewire\InputBeras;
use App\Livewire\ListBeras;
use App\Livewire\UploadFotoBeras;
use App\Livewire\ViewFotoBeras;
use App\Http\Controllers\BerasPdfController;

// Import Livewire Components (Laporan)
use App\Livewire\LaporanGkp;
use App\Livewire\LaporanHgl;

// Import Livewire Components (Verifikasi)
use App\Livewire\VerifikasiGabah;
use App\Livewire\VerifikasiBeras;
use App\Livewire\TarifSetting;
use App\Livewire\ActivityLog;

use App\Livewire\EditBeras; // Pastikan nanti component ini dibuat

// Route untuk update/edit
Route::get('/beras/edit/{id}', EditBeras::class)->name('edit.beras');
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. TAMU (GUEST) - Belum Login
// ==========================================
Route::middleware('guest')->group(function () {
    // Halaman Root (/) diarahkan ke Login
    Route::get('/', Login::class)->name('login');

    // Opsional: Tambahkan juga route /login eksplisit agar aman jika redirect middleware mencarinya
    Route::get('/login', Login::class);
});

// ==========================================
// 2. USER LOGIN (AUTHENTICATED)
// ==========================================
Route::middleware('auth')->group(function () {

    // Logout Route
    Route::get('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

    // --- HALAMAN UMUM (Bisa diakses Semua User Login) ---


    // PENTING: Nama route ini diganti jadi 'dashboard.gabah' agar sesuai dengan Login.php
    Route::get('/dashboard', DashboardGabah::class)->name('dashboard.gabah');

    // Halaman Serapan
    Route::get('/serapan', Serapan::class)->name('serapan');

    // Route untuk Halaman Compare PO GKP
    Route::get('/compare-po-gkp', \App\Livewire\ComparePoGkp::class)->name('compare.po.gkp');

    // Route Compare MO HGL
    Route::get('/compare-mo-hgl', \App\Livewire\CompareMoHgl::class)->name('compare.mo.hgl');

    // Halaman Settings / Update Profile
    Route::get('/settings', UpdateProfile::class)->name('settings');

    Route::get('/manage-users', \App\Livewire\ManageUser::class)->name('manage.users');
    Route::get('/tarif-setting', TarifSetting::class)->name('tarif.setting')->middleware('role:super admin');
    Route::get('/activity-log', ActivityLog::class)->name('activity.log')->middleware('role:super admin');

    // --- HALAMAN KHUSUS INSPEKTOR ---
    // (Input, Edit, List, Upload, Laporan)
    // Hanya bisa diakses jika user punya role 'inspektor' atau sesuai middleware Anda
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
        // Route::get('/edit-beras/{id}', EditBeras::class)->name('edit.beras'); // Uncomment jika sudah ada
        Route::get('/upload-foto-beras/{id}', UploadFotoBeras::class)->name('upload.beras');
        Route::get('/print/beras/{id}/{type}', [BerasPdfController::class, 'print'])->name('print.beras');

        // Laporan
        Route::get('/laporan-gkp', LaporanGkp::class)->name('laporan.gkp');
        Route::get('/laporan-hgl', LaporanHgl::class)->name('laporan.hgl');

        // BAST
        Route::get('/bast-beras', BastBeras::class)->name('bast.beras');
        Route::get('/bast-beras/pdf', [BastBerasPdfController::class, 'print'])->name('bast.beras.pdf');
        Route::get('/bast-gabah', BastGabah::class)->name('bast.gabah');
        Route::get('/bast-gabah/pdf', [BastGabahPdfController::class, 'print'])->name('bast.gabah.pdf');
    });

    // --- VIEW FOTO (Inspektor & Verification) ---
    Route::middleware(['role:inspektor,verification'])->group(function () {
        Route::get('/view-foto-gabah/{id}', ViewFotoGabah::class)->name('view.foto.gabah');
        Route::get('/view-foto-beras/{id}', ViewFotoBeras::class)->name('view.foto.beras');
    });

    // --- HALAMAN KHUSUS VERIFICATION ---
    Route::middleware(['role:verification'])->group(function () {
        Route::get('/verifikasi-gabah', VerifikasiGabah::class)->name('verifikasi.gabah');
        Route::get('/verifikasi-beras', VerifikasiBeras::class)->name('verifikasi.beras');
    });
});
