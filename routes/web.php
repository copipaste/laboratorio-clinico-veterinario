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
    
    Route::view('/veterinarias', 'veterinarias.index')
        ->name('veterinarias.index');
    

    Route::view('/muestras', 'muestras.index')
        ->name('muestras.index');
    
    Route::view('/muestras/escanear', 'muestras.escanear')
        ->name('muestras.escanear');
    
    // Ruta para imprimir etiqueta de muestra
    Route::get('/muestras/{muestra}/etiqueta', function (\App\Models\Muestra $muestra) {
        return view('components.etiqueta-muestra', ['muestra' => $muestra]);
    })->name('muestras.etiqueta');

    // Ruta para registrar resultados de análisis
    Route::get('/analisis/{analisis}/resultados', function (\App\Models\Analisis $analisis) {
        return view('analisis.registrar-resultados', ['analisisId' => $analisis->id]);
    })->name('analisis.resultados');

    Route::view('/especies', 'especies.index')
        ->name('especies.index');

    
    Route::view('/tipos-analisis', 'tipos-analisis.index')
        ->name('tipos-analisis.index');
    
    Route::view('/roles', 'roles.index')
        ->name('roles.index');
    
    Route::view('/permisos', 'permisos.index')
        ->name('permisos.index');
});

require __DIR__.'/settings.php';
