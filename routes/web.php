<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\DashboardGabah;
use App\Livewire\Serapan;
use App\Livewire\InputGabah;
use App\Livewire\InputBeras; // Pastikan di-import

// Input beras

Route::get('/input-beras', InputBeras::class)->name('input.beras');

// input Gabah
Route::get('/input-gabah', InputGabah::class)->name('input.gabah');

// Route Halaman Utama (Dashboard)
Route::get('/', DashboardGabah::class)->name('dashboard');

// Route Halaman Serapan
Route::get('/serapan', Serapan::class)->name('serapan');