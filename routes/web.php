<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; // <-- El use va arriba, junto al otro

// Ruta de inicio por defecto de Laravel
Route::get('/', function () {
    return view('inicio'); 
});

// Rutas de nuestro Panel del Director
Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
Route::post('/admin/especialidades', [AdminController::class, 'guardarEspecialidad'])->name('admin.especialidades.store');
Route::post('/admin/medicos', [AdminController::class, 'guardarMedico'])->name('admin.medicos.store');
Route::get('/caja', [AdminController::class, 'indexCaja'])->name('caja.dashboard');
Route::post('/caja/pagar/{id}', [AdminController::class, 'confirmarPago'])->name('caja.pagar');
use App\Http\Controllers\MedicoController;

Route::get('/medico/{id}', [MedicoController::class, 'dashboard'])->name('medico.dashboard');
Route::get('/medico/atender/{idCita}', [MedicoController::class, 'atenderCita'])->name('medico.atender');
Route::post('/medico/atender/{idCita}', [MedicoController::class, 'guardarHistorial'])->name('medico.guardar');