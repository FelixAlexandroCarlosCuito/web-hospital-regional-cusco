<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; // <-- El use va arriba, junto al otro

// Ruta de inicio por defecto de Laravel
Route::get('/', function () {
    return view('welcome');
});

// Rutas de nuestro Panel del Director
Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
Route::post('/admin/especialidades', [AdminController::class, 'guardarEspecialidad'])->name('admin.especialidades.store');
Route::post('/admin/medicos', [AdminController::class, 'guardarMedico'])->name('admin.medicos.store');