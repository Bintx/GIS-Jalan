<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController; // Pastikan ini ada
use App\Http\Controllers\ProfileController; // Dari Breeze
use App\Http\Controllers\JalanController; // Controller GIS Anda

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute untuk Jalan (juga dilindungi autentikasi)
    Route::resource('jalan', JalanController::class);
});

require __DIR__ . '/auth.php';
