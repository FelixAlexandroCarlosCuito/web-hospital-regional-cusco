<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitaController;

Route::post('/citas', [CitaController::class, 'store']);
Route::get('/citas/{id}', [CitaController::class, 'show']);
//ruta bade sde datos cuita xd
Route::get('/paciente/{documento}', [CitaController::class, 'buscarPaciente']);