<?php

namespace App\Http\Controllers;

use App\Models\ReniecSimulacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Gestiona el registro con validación RENIEC, inicio de sesión y cierre seguro de sesiones.
 */
class AuthController extends Controller
{
    public function index()
    {
        return view('auth.inicio-sesion');
    }

    public function registro()
    {
        return view('auth.registro');
    }

    public function registrar(Request $request)
    {
        $datos = $request->validate([
            'tipo_documento' => 'required|string|in:DNI,CE',
            'numero_documento' => 'required|string|max:20|unique:users,numero_documento',
            'digito_verificador' => 'required|string|size:1',
            'fecha_emision' => 'required|date',
            'correo' => 'nullable|email|max:255|unique:users,correo',
            'password' => 'required|string|min:6|max:100|confirmed',
        ], [
            'numero_documento.required' => 'Debe ingresar su número de documento.',
            'numero_documento.unique' => 'Ya existe una cuenta registrada con este número de documento. Intente iniciar sesión.',
            'digito_verificador.size' => 'El dígito verificador debe tener exactamente 1 carácter.',
            'fecha_emision.date' => 'Ingrese una fecha de emisión válida.',
            'correo.email' => 'Ingrese un correo electrónico válido.',
            'correo.unique' => 'Este correo electrónico ya está asociado a otra cuenta.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $numeroDocumento = trim($datos['numero_documento']);

        $ciudadano = ReniecSimulacion::select([
            'id', 'nombres', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento', 'direccion',
        ])
            ->where('dni', $numeroDocumento)
            ->where('digito_verificador', $datos['digito_verificador'])
            ->where('fecha_emision', $datos['fecha_emision'])
            ->first();

        if (!$ciudadano) {
            return back()->withErrors([
                'numero_documento' => 'Los datos de validación no coinciden con los registros oficiales de RENIEC.',
            ])->withInput($request->except('password', 'password_confirmation'));
        }

        // Asigna rol y estado fuera de $fillable para garantizar el principio de mínimo privilegio
        $usuario = new User([
            'tipo_documento' => $datos['tipo_documento'],
            'numero_documento' => $numeroDocumento,
            'nombres' => $ciudadano->nombres,
            'apellido_paterno' => $ciudadano->apellido_paterno,
            'apellido_materno' => $ciudadano->apellido_materno,
            'fecha_nacimiento' => $ciudadano->fecha_nacimiento,
            'direccion' => $ciudadano->direccion,
            'correo' => isset($datos['correo']) ? trim($datos['correo']) : null,
            'password' => Hash::make($datos['password']),
        ]);

        $usuario->rol = 'paciente';
        $usuario->activo = true;
        $usuario->save();

        Auth::login($usuario);
        // Regenera el identificador de sesión para mitigar ataques de fijación de sesión
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function autenticar(Request $request)
    {
        $datos = $request->validate([
            'numero_documento' => 'required|string|max:20',
            'password' => 'required|string|max:100',
        ]);

        $credenciales = [
            'numero_documento' => trim($datos['numero_documento']),
            'password' => $datos['password'],
        ];

        if (Auth::attempt($credenciales)) {
            if (!Auth::user()->activo) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'numero_documento' => 'Esta cuenta se encuentra desactivada. Comuníquese con Administración.',
                ]);
            }

            $request->session()->regenerate();

            return match (Auth::user()->rol) {
                'admin' => redirect()->route('admin.panel'),
                'medico' => redirect()->route('medico.panel'),
                'admision' => redirect()->route('admision.panel'),
                default => redirect()->route('dashboard'),
            };
        }

        return back()->withErrors([
            'numero_documento' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function verificarReniec(Request $request)
    {
        $datos = $request->validate([
            'numero_documento' => 'required|string|max:20',
            'digito_verificador' => 'required|string|size:1',
            'fecha_emision' => 'required|date',
        ]);

        $numeroDocumento = trim($datos['numero_documento']);

        // Valida identidad física antes de comprobar existencia en users para prevenir enumeración de cuentas
        $ciudadano = ReniecSimulacion::select(['id', 'nombres', 'apellido_paterno', 'apellido_materno'])
            ->where('dni', $numeroDocumento)
            ->where('digito_verificador', $datos['digito_verificador'])
            ->where('fecha_emision', $datos['fecha_emision'])
            ->first();

        if (!$ciudadano) {
            return response()->json([
                'success' => false,
                'message' => 'Los datos no coinciden con los registros oficiales de RENIEC.',
            ], 404);
        }

        if (User::where('numero_documento', $numeroDocumento)->exists()) {
            return response()->json([
                'success' => false,
                'ya_registrado' => true,
                'message' => 'Ya existe una cuenta registrada con este DNI. Por favor, inicia sesión.',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'nombres' => $ciudadano->nombres,
            'apellido_paterno' => $ciudadano->apellido_paterno,
            'apellido_materno' => $ciudadano->apellido_materno,
        ]);
    }
}