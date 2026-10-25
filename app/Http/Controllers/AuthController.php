<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    // Método que renderiza la vista para iniciar sesión
    public function index()
    {
        return view('auth.inicio_sesion');
    }

    // Método que renderiza la vista para registrarse
    public function registro()
    {
        return view('auth.registro');
    }

    // Método que procesará el formulario a la base de datos (lógica pendiente)
    public function registrar(Request $request)
    {
        // Aquí programaremos la validación y el registro en la base de datos
    }
}