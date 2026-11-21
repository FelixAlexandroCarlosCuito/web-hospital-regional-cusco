<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ReniecSimulacion;
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
        // 1. Validamos los inputs con mensajes claros en español
        $request->validate([
            'tipo_documento'     => 'required|string|in:DNI,CE',
            'numero_documento'   => 'required|string|unique:users,numero_documento',
            'digito_verificador' => 'required|string|size:1',
            'fecha_emision'      => 'required|date',
            'correo'             => 'nullable|email|max:255|unique:users,correo',
            'password'           => 'required|string|min:6|confirmed',
        ], [
            'numero_documento.required' => 'Debe ingresar su número de documento.',
            'numero_documento.unique'   => 'Ya existe una cuenta registrada con este número de documento. Intente iniciar sesión.',
            'digito_verificador.size'   => 'El dígito verificador debe tener exactamente 1 carácter.',
            'fecha_emision.date'        => 'Ingrese una fecha de emisión válida.',
            'correo.email'              => 'Ingrese un correo electrónico válido.',
            'correo.unique'             => 'Este correo electrónico ya está asociado a otra cuenta.',
            'password.min'              => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
        ]);

        // 2. Cruzamos los datos con la base oficial (tabla simulada)
        $ciudadano = ReniecSimulacion::where('dni', $request->numero_documento)
                        ->where('digito_verificador', $request->digito_verificador)
                        ->where('fecha_emision', $request->fecha_emision)
                        ->first();

        if (!$ciudadano) {
            return back()->withErrors([
                'numero_documento' => 'Los datos de validación no coinciden con los registros oficiales de RENIEC.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        $usuario = User::create([
            'tipo_documento'   => $request->tipo_documento,
            'numero_documento' => $request->numero_documento,
            'nombres'          => $ciudadano->nombres,
            'apellido_paterno' => $ciudadano->apellido_paterno,
            'apellido_materno' => $ciudadano->apellido_materno,
            'fecha_nacimiento' => $ciudadano->fecha_nacimiento,
            'direccion'        => $ciudadano->direccion,
            'correo'           => $request->correo,
            'password'         => Hash::make($request->password),
        ]);

        Auth::login($usuario);
        return redirect()->route('dashboard');
    }
    /**
     * Procesa las credenciales para el inicio de sesión.
     */
    public function autenticar(Request $request)
    {
        $credenciales = $request->validate([
            'numero_documento' => 'required|string',
            'password'         => 'required|string',
        ]);

        if (Auth::attempt($credenciales)) {
            // Si la cuenta fue dada de baja lógicamente, cerramos sesión de inmediato
            if (!Auth::user()->activo) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'numero_documento' => 'Esta cuenta se encuentra desactivada. Comuníquese con Administración.',
                ]);
            }

            $request->session()->regenerate();
            $rol = Auth::user()->rol;

            if ($rol === 'admin') {
                return redirect()->route('admin.panel');
            } elseif ($rol === 'medico') {
                return redirect()->route('medico.panel');
            } elseif ($rol === 'admision') {
                return redirect()->route('admision.panel');
            }

            return redirect()->route('dashboard');
        }

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
        $request->validate([
            'numero_documento'   => 'required|string',
            'digito_verificador' => 'required|string|size:1',
            'fecha_emision'      => 'required|date',
        ]);

        // 1. Primero probamos identidad física contra RENIEC (evita enumeración de pacientes)
        $ciudadano = ReniecSimulacion::where('dni', $request->numero_documento)
            ->where('digito_verificador', $request->digito_verificador)
            ->where('fecha_emision', $request->fecha_emision)
            ->first();

        if (!$ciudadano) {
            return response()->json([
                'success' => false,
                'message' => 'Los datos no coinciden con los registros oficiales de RENIEC.'
            ], 404);
        }

        // 2. Ahora que validó su DNI, verificamos si ya tiene cuenta creada
        $yaRegistrado = User::where('numero_documento', $request->numero_documento)->exists();

        if ($yaRegistrado) {
            return response()->json([
                'success'       => false,
                'ya_registrado' => true,
                'message'       => 'Ya existe una cuenta registrada con este DNI. Por favor, inicia sesión.'
            ], 409);
        }

        // 3. Todo limpio: devolvemos los datos para habilitar la Fase 2
        return response()->json([
            'success'          => true,
            'nombres'          => $ciudadano->nombres,
            'apellido_paterno' => $ciudadano->apellido_paterno,
            'apellido_materno' => $ciudadano->apellido_materno,
        ]);
    }
}