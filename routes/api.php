<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitaController;
use App\Models\Medico; // <-- IMPORTANTE: Importar el modelo Medico

// --- Rutas de Citas ---
Route::post('/citas', [CitaController::class, 'store']);
Route::get('/citas/{id}', [CitaController::class, 'show']);
Route::get('/paciente/{documento}', [CitaController::class, 'buscarPaciente']);

// --- Ruta para obtener médicos por especialidad (para el Frontend) ---
Route::get('/especialidades/{id}/medicos', function ($id) {
    // Busca los médicos en la base de datos que tengan ese especialidad_id
    return response()->json(Medico::where('especialidad_id', $id)->get());
});