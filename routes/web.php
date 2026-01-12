<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/sucursales', 'sucursales.index')
        ->name('sucursales.index');
    
    Route::view('/especies', 'especies.index')
        ->name('especies.index');
});

require __DIR__.'/settings.php';
