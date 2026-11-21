<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdmisionController;
use App\Http\Controllers\PaginasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\MedicoController;
use App\Http\Middleware\CheckRolMedico;
use App\Http\Middleware\CheckRolAdmision;

//=== RUTAS PÚBLICAS ===
Route::get('/', [PaginasController::class, 'inicio'])->name('inicio');
Route::get('/inicio', [PaginasController::class, 'inicio']);

//=== RUTAS DE INVITADOS (NO AUTENTICADOS) ===
Route::middleware('guest')->group(function () {
    Route::get('/inicio-sesion', [AuthController::class, 'index'])->name('login');
    Route::post('/inicio-sesion', [AuthController::class, 'autenticar'])
        ->middleware('throttle:10,1'); // Máx. 10 intentos por minuto

    Route::get('/registro', [AuthController::class, 'registro'])->name('registro');
    Route::post('/registrar', [AuthController::class, 'registrar'])
        ->middleware('throttle:10,1')
        ->name('registrar');

    // API interna para validación de identidad (limitada contra fuerza bruta)
    Route::post('/verificar-reniec', [AuthController::class, 'verificarReniec'])
        ->middleware('throttle:15,1')
        ->name('api.reniec');
});

//=== RUTAS PROTEGIDAS (AUTENTICADOS) ===
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Módulo Paciente y Citas
    Route::get('/dashboard', [PacienteController::class, 'dashboard'])->name('dashboard');
    Route::get('/reservar-cita', [CitaController::class, 'create'])->name('reservar_cita');
    Route::post('/reservar-cita', [CitaController::class, 'store'])->name('citas.store');
    Route::get('/ticket/{id}', [CitaController::class, 'showTicket'])->name('ticket');
    Route::patch('/citas/{id}/cancelar', [CitaController::class, 'cancelar'])->name('citas.cancelar');
    Route::get('/citas/horarios', [CitaController::class, 'getHorarios'])->name('citas.horarios');

    // Módulo Médico
    Route::middleware([CheckRolMedico::class])->group(function () {
        Route::get('/panel-medico', [MedicoController::class, 'panel'])->name('medico.panel');
        Route::patch('/panel-medico/citas/{id}/atender', [MedicoController::class, 'atender'])->name('medico.citas.atender');
    });

    // Módulo Admisión
    Route::middleware([CheckRolAdmision::class])->group(function () {
        Route::get('/panel-admision', [AdmisionController::class, 'panel'])->name('admision.panel');
        Route::patch('/panel-admision/citas/{id}/cancelar', [AdmisionController::class, 'cancelarCita'])->name('admision.citas.cancelar');
    });
});