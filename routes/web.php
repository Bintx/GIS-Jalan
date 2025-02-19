<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JalanController;

Route::get('/', function () {
    return view('welcome');
});


Route::resource('jalan', JalanController::class);
