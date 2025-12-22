<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\DashboardGabah;
use App\Livewire\Serapan;

// Route Halaman Utama (Dashboard)
Route::get('/', DashboardGabah::class)->name('dashboard');

// Route Halaman Serapan
Route::get('/serapan', Serapan::class)->name('serapan');