<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\DashboardGabah;
use App\Livewire\Serapan;
use App\Livewire\InputGabah;
use App\Livewire\InputBeras; 
use App\Livewire\ListGabah;
use App\Http\Controllers\GabahPdfController;
use App\Livewire\EditGabah; 
use App\Livewire\UploadFotoGabah;
use App\Livewire\ListBeras; 
use App\Http\Controllers\BerasPdfController;  
use App\Livewire\UploadFotoBeras; 
use App\Livewire\LaporanGkp;

Route::get('/laporan-gkp', LaporanGkp::class)->name('laporan.gkp');

// ... route gabah yang sudah ada ...

// === ROUTE BERAS ===
Route::get('/list-beras', ListBeras::class)->name('list.beras');
Route::get('/print/beras/{id}/{type}', [BerasPdfController::class, 'print'])->name('print.beras');

// Route Upload Foto Beras (Kita buat component terpisah agar rapi)
Route::get('/upload-foto-beras/{id}', UploadFotoBeras::class)->name('upload.beras');

// Route Edit Beras (Opsional jika ingin fitur edit aktif)
// Route::get('/edit-beras/{id}', EditBeras::class)->name('edit.beras');

// Ref Upload
Route::get('/upload-foto-gabah/{id}', UploadFotoGabah::class)->name('upload.gabah');


// Route Edit Gabah (Menerima parameter ID)
Route::get('/edit-gabah/{id}', EditGabah::class)->name('edit.gabah');
//  Route ke Halaman List
Route::get('/list-gabah', ListGabah::class)->name('list.gabah');

//  Route untuk Print PDF (Menggunakan Controller biasa, bukan Livewire)
Route::get('/print/gabah/{id}/{type}', [GabahPdfController::class, 'print'])->name('print.gabah');

// Input beras

Route::get('/input-beras', InputBeras::class)->name('input.beras');

// input Gabah
Route::get('/input-gabah', InputGabah::class)->name('input.gabah');

// Route Halaman Utama (Dashboard)
Route::get('/', DashboardGabah::class)->name('dashboard');

// Route Halaman Serapan
Route::get('/serapan', Serapan::class)->name('serapan');