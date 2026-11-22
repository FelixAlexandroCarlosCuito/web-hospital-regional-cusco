<?php

use App\Http\Controllers\Admin\EspecialidadController as AdminEspecialidadController;
use App\Http\Controllers\Admin\NovedadController as AdminNovedadController;
use App\Http\Controllers\Admin\PanelController as AdminPanelController;
use App\Http\Controllers\Admin\PersonalController as AdminPersonalController;
use App\Http\Controllers\AdmisionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PaginasController;
use App\Http\Middleware\CheckRolAdmin;
use App\Http\Middleware\CheckRolAdmision;
use App\Http\Middleware\CheckRolMedico;
use App\Http\Middleware\CheckUsuarioActivo;
use Illuminate\Support\Facades\Route;

// Rutas públicas del portal institucional
Route::get('/', [PaginasController::class, 'inicio'])->name('inicio');
Route::get('/inicio', [PaginasController::class, 'inicio']);
Route::get('/novedades', [PaginasController::class, 'novedades'])->name('novedades.index');
Route::get('/novedades/{slug}', [PaginasController::class, 'verNovedad'])
    ->where('slug', '[a-z0-9-]+')
    ->name('novedades.show');
Route::get('/especialidades', [PaginasController::class, 'especialidades'])->name('especialidades');
Route::get('/guia-contacto', [PaginasController::class, 'guiaContacto'])->name('guia.contacto');

// Verificación pública de constancias por QR protegida con firma criptográfica y límite de tasa
Route::get('/verificar-cita/{cita}', [CitaController::class, 'verificarTicket'])
    ->whereNumber('cita')
    ->middleware(['signed', 'throttle:30,1'])
    ->name('citas.verificar');

// Rutas de autenticación y registro para usuarios no autenticados
Route::middleware('guest')->group(function () {
    Route::get('/inicio-sesion', [AuthController::class, 'index'])->name('login');
    Route::post('/inicio-sesion', [AuthController::class, 'autenticar'])
        ->middleware('throttle:10,1');

    Route::get('/registro', [AuthController::class, 'registro'])->name('registro');
    Route::post('/registrar', [AuthController::class, 'registrar'])
        ->middleware('throttle:10,1')
        ->name('registrar');

    Route::post('/verificar-reniec', [AuthController::class, 'verificarReniec'])
        ->middleware('throttle:15,1')
        ->name('api.reniec');
});

// Rutas protegidas que requieren sesión activa
Route::middleware(['auth', CheckUsuarioActivo::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Módulo de pacientes y gestión de citas
    Route::get('/dashboard', [PacienteController::class, 'dashboard'])->name('dashboard');
    Route::get('/reservar-cita', [CitaController::class, 'create'])->name('reservar_cita');
    Route::post('/reservar-cita', [CitaController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('citas.store');
    Route::get('/ticket/{id}', [CitaController::class, 'showTicket'])
        ->whereNumber('id')
        ->name('ticket');
    Route::patch('/citas/{id}/cancelar', [CitaController::class, 'cancelar'])
        ->whereNumber('id')
        ->middleware('throttle:10,1')
        ->name('citas.cancelar');
    Route::get('/api/horarios-disponibles', [CitaController::class, 'horariosDisponibles'])
        ->middleware('throttle:30,1')
        ->name('api.horarios.disponibles');

    // Módulo clínico de médicos
    Route::middleware([CheckRolMedico::class])->group(function () {
        Route::get('/panel-medico', [MedicoController::class, 'panel'])->name('medico.panel');
        Route::patch('/panel-medico/citas/{id}/atender', [MedicoController::class, 'atender'])
            ->whereNumber('id')
            ->name('medico.citas.atender');
    });

    // Módulo operativo de admisión
    Route::middleware([CheckRolAdmision::class])->group(function () {
        Route::get('/panel-admision', [AdmisionController::class, 'panel'])->name('admision.panel');
        Route::patch('/panel-admision/citas/{id}/cancelar', [AdmisionController::class, 'cancelarCita'])
            ->whereNumber('id')
            ->name('admision.citas.cancelar');
    });

    // Módulo administrativo
    Route::middleware([CheckRolAdmin::class])
        ->prefix('panel-admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/', [AdminPanelController::class, 'panel'])->name('panel');

            Route::get('/especialidades', [AdminEspecialidadController::class, 'index'])->name('especialidades.index');
            Route::post('/especialidades', [AdminEspecialidadController::class, 'store'])->name('especialidades.store');
            Route::put('/especialidades/{especialidad}', [AdminEspecialidadController::class, 'update'])
                ->whereNumber('especialidad')
                ->name('especialidades.update');
            Route::patch('/especialidades/{especialidad}/estado', [AdminEspecialidadController::class, 'toggleEstado'])
                ->whereNumber('especialidad')
                ->name('especialidades.toggle');

            Route::get('/personal', [AdminPersonalController::class, 'index'])->name('personal.index');
            Route::post('/personal', [AdminPersonalController::class, 'store'])->name('personal.store');
            Route::put('/personal/{user}', [AdminPersonalController::class, 'update'])
                ->whereNumber('user')
                ->name('personal.update');
            Route::patch('/personal/{user}/estado', [AdminPersonalController::class, 'toggleEstado'])
                ->whereNumber('user')
                ->name('personal.toggle');

            Route::get('/novedades', [AdminNovedadController::class, 'index'])->name('novedades.index');
            Route::post('/novedades', [AdminNovedadController::class, 'store'])->name('novedades.store');
            Route::put('/novedades/{novedad}', [AdminNovedadController::class, 'update'])
                ->whereNumber('novedad')
                ->name('novedades.update');
            Route::patch('/novedades/{novedad}/estado', [AdminNovedadController::class, 'togglePublicacion'])
                ->whereNumber('novedad')
                ->name('novedades.toggle');
            Route::delete('/novedades/{novedad}', [AdminNovedadController::class, 'destroy'])
                ->whereNumber('novedad')
                ->name('novedades.destroy');
        });
});