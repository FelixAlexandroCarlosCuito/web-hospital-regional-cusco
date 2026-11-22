<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use App\Http\Controllers\PaginasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Gestiona el catálogo administrativo de especialidades médicas, su estado operativo
 * y la imagen de referencia mostrada en la cartera pública.
 */
class EspecialidadController extends Controller
{
    public function index()
    {
        $hoy = now()->toDateString();

        $especialidades = Especialidad::select(['id', 'nombre', 'descripcion', 'imagen', 'activo'])
            ->withCount([
                'medicos',
                'medicos as medicos_activos_count' => fn ($q) => $q->where('activo', true),
                'citas as citas_pendientes_count' => fn ($q) => $q
                    ->where('citas.estado', 'Pendiente')
                    ->where('citas.fecha', '>=', $hoy),
            ])
            ->orderByDesc('activo')
            ->orderBy('nombre')
            ->get();

        return view('admin.especialidades', compact('especialidades'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100|unique:especialidades,nombre',
            'descripcion' => 'nullable|string|max:500',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'nombre.required' => 'El nombre de la especialidad es obligatorio.',
            'nombre.unique' => 'Ya existe una especialidad registrada con ese nombre.',
            'nombre.max' => 'El nombre no debe exceder los 100 caracteres.',
            'descripcion.max' => 'La descripción no debe exceder los 500 caracteres.',
            'imagen.image' => 'El archivo seleccionado debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe estar en formato JPG, PNG o WEBP.',
            'imagen.max' => 'La imagen no debe superar los 2 MB.',
        ]);

        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            $rutaImagen = $request->file('imagen')->store('especialidades', 'public');
        }

        Especialidad::create([
            'nombre' => trim($datos['nombre']),
            'descripcion' => isset($datos['descripcion']) ? trim($datos['descripcion']) : null,
            'imagen' => $rutaImagen,
            'activo' => true,
        ]);

        PaginasController::limpiarCachePublico();
        return redirect()
            ->route('admin.especialidades.index')
            ->with('success', 'Especialidad registrada correctamente.');
    }

    public function update(Request $request, Especialidad $especialidad)
    {
        $datos = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('especialidades', 'nombre')->ignore($especialidad->id),
            ],
            'descripcion' => 'nullable|string|max:500',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'eliminar_imagen' => 'nullable|boolean',
        ], [
            'nombre.required' => 'El nombre de la especialidad es obligatorio.',
            'nombre.unique' => 'Ya existe otra especialidad registrada con ese nombre.',
            'nombre.max' => 'El nombre no debe exceder los 100 caracteres.',
            'descripcion.max' => 'La descripción no debe exceder los 500 caracteres.',
            'imagen.image' => 'El archivo seleccionado debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe estar en formato JPG, PNG o WEBP.',
            'imagen.max' => 'La imagen no debe superar los 2 MB.',
        ]);

        $rutaImagen = $especialidad->imagen;

        if ($request->hasFile('imagen')) {
            if ($rutaImagen && Storage::disk('public')->exists($rutaImagen)) {
                Storage::disk('public')->delete($rutaImagen);
            }
            $rutaImagen = $request->file('imagen')->store('especialidades', 'public');
        } elseif ($request->boolean('eliminar_imagen') && $rutaImagen) {
            if (Storage::disk('public')->exists($rutaImagen)) {
                Storage::disk('public')->delete($rutaImagen);
            }
            $rutaImagen = null;
        }

        $especialidad->update([
            'nombre' => trim($datos['nombre']),
            'descripcion' => isset($datos['descripcion']) ? trim($datos['descripcion']) : null,
            'imagen' => $rutaImagen,
        ]);

        PaginasController::limpiarCachePublico();
        return redirect()
            ->route('admin.especialidades.index')
            ->with('success', 'Especialidad actualizada correctamente.');
    }

    public function toggleEstado(Especialidad $especialidad)
    {
        $nuevoEstado = !$especialidad->activo;
        $especialidad->update(['activo' => $nuevoEstado]);

        if (!$nuevoEstado) {
            $pendientes = $especialidad->citas()
                ->where('citas.estado', 'Pendiente')
                ->where('citas.fecha', '>=', now()->toDateString())
                ->count();

            $mensaje = "Especialidad \"{$especialidad->nombre}\" desactivada. Ya no aparecerá en nuevas reservas.";
            if ($pendientes > 0) {
                $mensaje .= " Aviso: mantiene {$pendientes} cita(s) pendiente(s) ya agendada(s) que seguirán visibles para Admisión y sus médicos.";
            }

            PaginasController::limpiarCachePublico();
            return redirect()
                ->route('admin.especialidades.index')
                ->with('warning', $mensaje);
        }

        PaginasController::limpiarCachePublico();
        return redirect()
            ->route('admin.especialidades.index')
            ->with('success', "Especialidad \"{$especialidad->nombre}\" activada nuevamente.");
    }
}