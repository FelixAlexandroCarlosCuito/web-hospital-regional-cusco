<?php

use App\Http\Controllers\Admin\EspecialidadController as AdminEspecialidadController;
use App\Http\Controllers\Admin\PanelController as AdminPanelController;
use App\Http\Controllers\Admin\PersonalController as AdminPersonalController;
use App\Http\Controllers\Admin\NovedadController as AdminNovedadController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdmisionController;
use App\Http\Controllers\PaginasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\MedicoController;
use App\Http\Middleware\CheckRolMedico;
use App\Http\Middleware\CheckRolAdmision;
use App\Http\Middleware\CheckRolAdmin;

//=== RUTAS PÚBLICAS ===
Route::get('/', [PaginasController::class, 'inicio'])->name('inicio');
Route::get('/inicio', [PaginasController::class, 'inicio']);
Route::get('/novedades', [PaginasController::class, 'novedades'])->name('novedades.index');
Route::get('/novedades/{slug}', [PaginasController::class, 'verNovedad'])->name('novedades.show');
Route::get('/especialidades', [PaginasController::class, 'especialidades'])->name('especialidades');
Route::get('/guia-contacto', [PaginasController::class, 'guiaContacto'])->name('guia.contacto');

//=== RUTAS PÚBLICAS (Sujeto a evalucación de seguridad)===
Route::get('/verificar-cita/{cita}', [CitaController::class, 'verificarTicket'])
    ->name('citas.verificar')
    ->middleware('signed');

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
    Route::get('/api/horarios-disponibles', [CitaController::class, 'horariosDisponibles'])
    ->name('api.horarios.disponibles');

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

    // Módulo Administrador
    Route::middleware([CheckRolAdmin::class])
        ->prefix('panel-admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/', [AdminPanelController::class, 'panel'])->name('panel');
            Route::get('/especialidades', [AdminEspecialidadController::class, 'index'])->name('especialidades.index');
            Route::post('/especialidades', [AdminEspecialidadController::class, 'store'])->name('especialidades.store');
            Route::put('/especialidades/{especialidad}', [AdminEspecialidadController::class, 'update'])->name('especialidades.update');
            Route::patch('/especialidades/{especialidad}/estado', [AdminEspecialidadController::class, 'toggleEstado'])->name('especialidades.toggle');

            Route::get('/personal', [AdminPersonalController::class, 'index'])->name('personal.index');
            Route::post('/personal', [AdminPersonalController::class, 'store'])->name('personal.store');
            Route::put('/personal/{user}', [AdminPersonalController::class, 'update'])->name('personal.update');
            Route::patch('/personal/{user}/estado', [AdminPersonalController::class, 'toggleEstado'])->name('personal.toggle');

            Route::get('/novedades', [AdminNovedadController::class, 'index'])->name('novedades.index');
            Route::post('/novedades', [AdminNovedadController::class, 'store'])->name('novedades.store');
            Route::put('/novedades/{novedad}', [AdminNovedadController::class, 'update'])->name('novedades.update');
            Route::patch('/novedades/{novedad}/estado', [AdminNovedadController::class, 'togglePublicacion'])->name('novedades.toggle');
            Route::delete('/novedades/{novedad}', [AdminNovedadController::class, 'destroy'])->name('novedades.destroy');
        });
});