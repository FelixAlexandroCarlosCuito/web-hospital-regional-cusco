<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; // <-- El use va arriba, junto al otro
use App\Http\Controllers\PaginasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\MedicoController;
use App\Http\Middleware\CheckRolMedico;

// Ruta de inicio por defecto
Route::get('/', function () {
    return view('inicio'); 
});

//=== RUTAS DE ACCESO EXCLUSIVAS ===
Route::middleware("guest")->group(function () {
    // Rutas de autenticación de la página
    Route::get('/inicio-sesion', [AuthController::class, 'index'])->name('login');
    Route::post('/inicio-sesion', [AuthController::class, 'autenticar']);
    Route::get('/registro', [AuthController::class, 'registro'])->name('registro');
    Route::post('/registrar', [AuthController::class, 'registrar'])->name('registrar');
    // API interna para validación
    Route::post('/verificar-reniec', [AuthController::class, 'verificarReniec'])->name('api.reniec');
});

Route::middleware("auth")->group(function () {
    // Rutas de autenticación de la página
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Rutas de paciente
    Route::get('/dashboard', [PacienteController::class, 'dashboard'])->name('dashboard');
    // Rutas de citas
    Route::get('/reservar-cita', [CitaController::class, 'reservar_cita'])->name('reservar_cita');
    Route::get('/ticket/{id}', [CitaController::class, 'showTicket'])->name('ticket');
    Route::get('/reservar-cita', [CitaController::class, 'create'])->name('reservar_cita');
    Route::post('/reservar-cita', [CitaController::class, 'store'])->name('citas.store');
    Route::patch('/citas/{id}/cancelar', [CitaController::class, 'cancelar'])->name('citas.cancelar');
    Route::get('/citas/horarios', [CitaController::class, 'getHorarios'])->name('citas.horarios');

    // MÓDULO MÉDICO
    Route::middleware([CheckRolMedico::class])->group(function () {        
        Route::get('/panel-medico', [MedicoController::class, 'panel'])->name('medico.panel');        
    });
});

//=== RUTAS DE ACCESO GLOBAL ===
// Rutas de pestañas
Route::get('/inicio', [PaginasController::class, 'inicio'])->name('inicio');

// Rutas de citas

// Rutas de paciente