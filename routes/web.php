<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JalanController;
use App\Http\Controllers\DashboardController;

// Rute untuk dashboard utama
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Rute resource untuk Jalan
Route::resource('jalan', JalanController::class);
