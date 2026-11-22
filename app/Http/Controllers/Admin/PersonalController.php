<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Gestiona el ciclo de vida de las cuentas de personal clínico (médicos) y de admisión.
 */
class PersonalController extends Controller
{
    public function index()
    {
        $hoy = now()->toDateString();

        $medicos = Medico::with(['user', 'especialidad'])
            ->withCount([
                'citas as citas_pendientes_count' => fn ($q) =>$q
                    ->where('estado', 'Pendiente')
                    ->whereDate('fecha', '>=', $hoy),
            ])
            ->orderByDesc('activo')
            ->orderBy('apellidos')
            ->get();

        $personalAdmision = User::where('rol', 'admision')
            ->orderByDesc('activo')
            ->orderBy('apellido_paterno')
            ->get();

        $especialidades = Especialidad::orderByDesc('activo')
            ->orderBy('nombre')
            ->get();

        return view('admin.personal', compact('medicos', 'personalAdmision', 'especialidades'));
    }

    public function store(Request $request)
    {
        $datos =$request->validate([
            'rol'              => 'required|in:medico,admision',
            'tipo_documento'   => 'required|in:DNI,CE',
            'numero_documento' => 'required|string|max:20|unique:users,numero_documento',
            'nombres'          => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before:today',
            'direccion'        => 'required|string|max:255',
            'telefono'         => 'nullable|string|max:15',
            'correo'           => 'nullable|email|max:255|unique:users,correo',
            'password'         => 'nullable|string|min:6',
            'especialidad_id'  => 'required_if:rol,medico|nullable|exists:especialidades,id',
            'consultorio'      => 'required_if:rol,medico|nullable|string|max:100',
        ], [
            'numero_documento.unique'     => 'Ya existe una cuenta registrada con ese número de documento.',
            'correo.unique'               => 'El correo ingresado ya pertenece a otro usuario.',
            'fecha_nacimiento.before'     => 'La fecha de nacimiento debe ser anterior a hoy.',
            'password.min'                => 'La contraseña debe tener al menos 6 caracteres.',
            'especialidad_id.required_if' => 'Debe asignar una especialidad al registrar un médico.',
            'consultorio.required_if'     => 'Debe indicar el consultorio asignado al médico.',
        ]);

        // Si no se especifica una clave inicial, se asigna el documento de identidad por política interna
        $claveInicial = !empty($datos['password']) ? $datos['password'] :$datos['numero_documento'];

        DB::transaction(function () use ($datos, $claveInicial) {$usuario = User::create([
                'rol'              => $datos['rol'],
                'activo'           => true,
                'tipo_documento'   => $datos['tipo_documento'],
                'numero_documento' => trim($datos['numero_documento']),
                'nombres'          => trim($datos['nombres']),
                'apellido_paterno' => trim($datos['apellido_paterno']),
                'apellido_materno' => trim($datos['apellido_materno']),
                'fecha_nacimiento' => $datos['fecha_nacimiento'],
                'direccion'        => trim($datos['direccion']),
                'telefono'         => isset($datos['telefono']) ? trim($datos['telefono']) : null,
                'correo'           => isset($datos['correo']) ? trim($datos['correo']) : null,
                'password'         => Hash::make($claveInicial),
            ]);

            if ($datos['rol'] === 'medico') {
                Medico::create([
                    'user_id'         => $usuario->id,
                    'especialidad_id' => $datos['especialidad_id'],
                    'nombres'         => $usuario->nombres,
                    'apellidos'       => "{$usuario->apellido_paterno} {$usuario->apellido_materno}",
                    'consultorio'     => trim($datos['consultorio']),
                    'activo'          => true,
                ]);
            }
        });

        $etiquetaRol =$datos['rol'] === 'medico' ? 'Médico' : 'Personal de Admisión';
        $avisoClave = empty($datos['password']) ? ' Clave inicial asignada: número de documento.' : '';

        return redirect()
            ->route('admin.personal.index')
            ->with('success', "{$etiquetaRol} registrado correctamente.{$avisoClave}");
    }

    public function update(Request $request, User$user)
    {
        // Restringe la edición exclusivamente a cuentas de personal gestionable desde este módulo
        if (!in_array($user->rol, ['medico', 'admision'], true)) {
            abort(404);
        }

        $reglas = [
            'tipo_documento'   => 'required|in:DNI,CE',
            'numero_documento' => ['required', 'string', 'max:20', Rule::unique('users', 'numero_documento')->ignore($user->id)],
            'nombres'          => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date|before:today',
            'direccion'        => 'required|string|max:255',
            'telefono'         => 'nullable|string|max:15',
            'correo'           => ['nullable', 'email', 'max:255', Rule::unique('users', 'correo')->ignore($user->id)],
            'password'         => 'nullable|string|min:6',
        ];

        if ($user->rol === 'medico') {
            $reglas['especialidad_id'] = 'required|exists:especialidades,id';$reglas['consultorio'] = 'required|string|max:100';
        }

        $datos = $request->validate($reglas, [
            'numero_documento.unique'  => 'Ya existe otra cuenta con ese número de documento.',
            'correo.unique'            => 'El correo ingresado ya pertenece a otro usuario.',
            'fecha_nacimiento.before'  => 'La fecha de nacimiento debe ser anterior a hoy.',
            'password.min'             => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'especialidad_id.required' => 'La especialidad es obligatoria para el personal médico.',
            'consultorio.required'     => 'El consultorio es obligatorio para el personal médico.',
        ]);

        DB::transaction(function () use ($user, $datos) {$camposUsuario = [
                'tipo_documento'   => $datos['tipo_documento'],
                'numero_documento' => trim($datos['numero_documento']),
                'nombres'          => trim($datos['nombres']),
                'apellido_paterno' => trim($datos['apellido_paterno']),
                'apellido_materno' => trim($datos['apellido_materno']),
                'fecha_nacimiento' => $datos['fecha_nacimiento'],
                'direccion'        => trim($datos['direccion']),
                'telefono'         => isset($datos['telefono']) ? trim($datos['telefono']) : null,
                'correo'           => isset($datos['correo']) ? trim($datos['correo']) : null,
            ];

            if (!empty($datos['password'])) {
                $camposUsuario['password'] = Hash::make($datos['password']);
            }

            $user->update($camposUsuario);

            // Mantiene sincronizados los datos replicados en el perfil clínico
            if ($user->rol === 'medico') {
                Medico::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'especialidad_id' => $datos['especialidad_id'],
                        'nombres'         => $user->nombres,
                        'apellidos'       => "{$user->apellido_paterno} {$user->apellido_materno}",
                        'consultorio'     => trim($datos['consultorio']),
                        'activo'          => $user->activo,
                    ]
                );
            }
        });

        return redirect()
            ->route('admin.personal.index')
            ->with('success', 'Datos del personal actualizados correctamente.');
    }

    public function toggleEstado(User $user)
    {
        if (!in_array($user->rol, ['medico', 'admision'], true)) {
            abort(404);
        }

        $nuevoEstado = !$user->activo;

        DB::transaction(function () use ($user,$nuevoEstado) {
            $user->update(['activo' =>$nuevoEstado]);

            if ($user->rol === 'medico' &&$user->medico) {
                $user->medico->update(['activo' =>$nuevoEstado]);
            }
        });

        $nombreCompleto = "{$user->nombres} {$user->apellido_paterno}";

        if (!$nuevoEstado) {$mensaje = "Cuenta de {$nombreCompleto} desactivada. Se ha bloqueado su acceso al sistema.";

            if ($user->rol === 'medico' &&$user->medico) {
                $pendientes =$user->medico->citas()
                    ->where('estado', 'Pendiente')
                    ->whereDate('fecha', '>=', now()->toDateString())
                    ->count();

                if ($pendientes > 0) {
                    $mensaje .= " Aviso: mantiene {$pendientes} cita(s) pendiente(s) que deben coordinarse con Admisión.";
                }
            }

            return redirect()
                ->route('admin.personal.index')
                ->with('warning', $mensaje);
        }

        return redirect()
            ->route('admin.personal.index')
            ->with('success', "Cuenta de {$nombreCompleto} reactivada correctamente.");
    }
}