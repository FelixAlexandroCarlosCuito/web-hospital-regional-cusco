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
        // 1. Validación estricta de los datos entrantes.
        // Se aplica la regla 'unique' para evitar duplicidad de documentos en la base de datos.
        // La regla 'confirmed' busca automáticamente el campo 'password_confirmation' en el request.
        $request->validate([
            'tipo_documento'   => 'required|string|in:DNI,CE',
            'numero_documento' => 'required|string|unique:users,numero_documento',
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'correo'           => 'nullable|email|max:255',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        // 2. Persistencia del modelo User.
        // Se utiliza Hash::make para encriptar la contraseña mediante Bcrypt antes de guardarla.
        // Nunca se debe almacenar una contraseña en texto plano.
        $usuario = User::create([
            'tipo_documento'   => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'nombres'          => $request->nombres,
            'apellidos'        => $request->apellidos,
            'correo'           => $request->correo,
            'password'         => Hash::make($request->password),
        ]);

        // 3. Autenticación automática tras el registro exitoso.
        Auth::login($usuario);

        // 4. Redirección al área protegida del paciente.
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
}