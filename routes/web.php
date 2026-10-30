<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; // <-- El use va arriba, junto al otro
use App\Http\Controllers\PaginasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\PacienteController;

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

//=== RUTAS DE ACCESO EXCLUSIVAS ===
Route::middleware("guest")->group(function () {
    // Rutas de autenticación de la página
    Route::get('/inicio_sesion', [AuthController::class, 'index'])->name('login');
    Route::get('/registro', [AuthController::class, 'registro'])->name('registro');
    Route::post('/registrar', [AuthController::class, 'registrar'])->name('registrar');
});

Route::middleware("auth")->group(function () {
    // Rutas de paciente
    Route::get('/dashboard', [PacienteController::class, 'dashboard'])->name('dashboard');
    // Rutas de citas
    Route::get('/reservar-cita', [PaginasController::class, 'reservar_cita'])->name('reservar_cita');
    Route::get('/ticket', [CitaController::class, 'ticket'])->name('ticket');
});

//=== RUTAS DE ACCESO GLOBAL ===
// Rutas de pestañas
Route::get('/inicio', [PaginasController::class, 'inicio'])->name('inicio');

// Rutas de citas

// Rutas de paciente