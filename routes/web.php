<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionalController;
use App\Http\Controllers\KerusakanJalanController;
use App\Http\Controllers\ProfileController; // Dari Breeze
use App\Http\Controllers\JalanController; // Controller GIS Anda
use App\Http\Controllers\DashboardController; // Pastikan ini ada

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('regional', RegionalController::class);
    Route::resource('jalan', JalanController::class);
    Route::resource('kerusakan-jalan', KerusakanJalanController::class);
    Route::resource('users', UserController::class);
    Route::get('/api/jalan/{jalan}', [JalanController::class, 'getJalanData'])->name('api.jalan.get-data');
    Route::get('/map-overview', [DashboardController::class, 'mapOverview'])->name('map.overview');
    Route::get('/kerusakan-jalan/export/pdf', [KerusakanJalanController::class, 'exportPdf'])->name('kerusakan-jalan.export-pdf');
    Route::get('/kerusakan-jalan/export/excel', [KerusakanJalanController::class, 'exportExcel'])->name('kerusakan-jalan.export-excel');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
