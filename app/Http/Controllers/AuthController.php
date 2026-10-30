<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Renderiza la vista de inicio de sesión.
     */
    public function index()
    {
        return view('auth.inicio-sesion');
    }

    /**
     * Renderiza la vista de registro de pacientes.
     */
    public function registro()
    {
        return view('auth.registro');
    }

    /**
     * Procesa el registro de un nuevo paciente en el sistema.
     */
    public function registrar(Request $request)
    {
        // 1. Validamos los inputs que envía el formulario
        $request->validate([
            'tipo_documento'     => 'required|string|in:DNI,CE',
            'numero_documento'   => 'required|string|unique:users,numero_documento',
            'digito_verificador' => 'required|string|size:1',
            'fecha_emision'      => 'required|date',
            'correo'             => 'nullable|email|max:255',
            'password'           => 'required|string|min:6|confirmed',
        ]);

        // 2. Cruzamos los datos con la base oficial (Nuestra tabla simulada)
        $ciudadano = \App\Models\ReniecSimulacion::where('dni', $request->numero_documento)
                        ->where('digito_verificador', $request->digito_verificador)
                        ->where('fecha_emision', $request->fecha_emision)
                        ->first();

        // 3. Si no existe o miente en algún dato, abortamos el registro por seguridad
        if (!$ciudadano) {
            return back()->withErrors([
                'numero_documento' => 'Los datos de validación no coinciden con los registros oficiales de RENIEC.'
            ])->withInput();
        }

        // 4. Si pasó el filtro, creamos el paciente extrayendo sus nombres de la base oficial, no de lo que él escribió
        $usuario = User::create([
            'tipo_documento'   => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'nombres'          => $ciudadano->nombres,
            'apellido_paterno' => $ciudadano->apellido_paterno,
            'apellido_materno' => $ciudadano->apellido_materno,
            'correo'           => $request->correo,
            'password'         => Hash::make($request->password),
        ]);

        // Autenticamos y dejamos pasar al sistema
        Auth::login($usuario);
        return redirect()->route('dashboard');
    }

    /**
     * Procesa las credenciales para el inicio de sesión.
     */
    public function autenticar(Request $request)
    {
        // Validación de formato previo a la consulta a la base de datos.
        $credenciales = $request->validate([
            'numero_documento' => 'required|string',
            'password'         => 'required|string',
        ]);

        // Intento de autenticación. Auth::attempt maneja internamente 
        // la comparación del hash de la contraseña.
        if (Auth::attempt($credenciales)) {
            // Regeneración del identificador de sesión para prevenir vulnerabilidades de Session Fixation.
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        // Si las credenciales fallan, se retorna a la vista anterior con un mensaje de error genérico
        // por razones de seguridad (no se especifica si falló el usuario o la contraseña).
        return back()->withErrors([
            'numero_documento' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Cierra la sesión del usuario y limpia los tokens.
     */
    public function logout(Request $request)
    {
        // 1. Cierra la sesión en el framework
        Auth::logout();

        // 2. Invalida la sesión actual para borrar los datos en memoria
        $request->session()->invalidate();

        // 3. Regenera el token CSRF para evitar ataques de fijación de sesión
        $request->session()->regenerateToken();

        // 4. Redirige a la pantalla de inicio de sesión
        return redirect()->route('login');
    }

    public function verificarReniec(Request $request)
    {
        $ciudadano = \App\Models\ReniecSimulacion::where('dni', $request->numero_documento)
                        ->where('digito_verificador', $request->digito_verificador)
                        ->where('fecha_emision', $request->fecha_emision)
                        ->first();

        if ($ciudadano) {
            return response()->json([
                'success' => true,
                'nombres' => $ciudadano->nombres,
                'apellido_paterno' => $ciudadano->apellido_paterno,
                'apellido_materno' => $ciudadano->apellido_materno
            ]);
        }

        // Retornamos un error HTTP 404 si es un impostor o escribió mal
        return response()->json([
            'success' => false,
            'message' => 'Los datos no coinciden con los registros de RENIEC.'
        ], 404);
    }
}