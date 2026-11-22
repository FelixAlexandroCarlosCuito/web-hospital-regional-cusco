<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Novedad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Gestiona la publicación, edición, visibilidad y eliminación de novedades institucionales.
 */
class NovedadController extends Controller
{
    public function index()
    {
        $novedades = Novedad::select([
            'id', 'user_id', 'titulo', 'slug', 'categoria',
            'resumen', 'contenido', 'imagen', 'publicado', 'fecha_publicacion', 'created_at',
        ])
            ->with('autor:id,nombres,apellido_paterno')
            ->orderByDesc('created_at')
            ->get();

        $categorias = Novedad::CATEGORIAS;

        return view('admin.novedades', compact('novedades', 'categorias'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:180'],
            'categoria' => ['required', Rule::in(Novedad::CATEGORIAS)],
            'resumen' => ['required', 'string', 'max:300'],
            'contenido' => ['required', 'string', 'max:20000'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'titulo.required' => 'El título es obligatorio.',
            'categoria.in' => 'Seleccione una categoría válida.',
            'resumen.required' => 'Debe ingresar un resumen breve para la vista previa.',
            'contenido.required' => 'El contenido de la novedad es obligatorio.',
            'contenido.max' => 'El contenido supera la extensión máxima permitida.',
            'imagen.image' => 'El archivo adjunto debe ser una imagen válida.',
            'imagen.mimes' => 'Solo se permiten imágenes en formato JPG, PNG o WEBP.',
            'imagen.max' => 'La imagen no debe superar los 2 MB.',
        ]);

        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            $rutaImagen = $request->file('imagen')->store('novedades', 'public');
        }

        $publicado = $request->boolean('publicado');

        Novedad::create([
            'user_id' => Auth::id(),
            'titulo' => trim($datos['titulo']),
            'slug' => Novedad::generarSlugUnico($datos['titulo']),
            'categoria' => $datos['categoria'],
            'resumen' => trim($datos['resumen']),
            'contenido' => trim($datos['contenido']),
            'imagen' => $rutaImagen,
            'publicado' => $publicado,
            'fecha_publicacion' => $publicado ? now() : null,
        ]);

        return redirect()
            ->route('admin.novedades.index')
            ->with('success', $publicado ? 'Novedad publicada correctamente.' : 'Borrador guardado correctamente.');
    }

    public function update(Request $request, Novedad $novedad)
    {
        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:180'],
            'categoria' => ['required', Rule::in(Novedad::CATEGORIAS)],
            'resumen' => ['required', 'string', 'max:300'],
            'contenido' => ['required', 'string', 'max:20000'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'titulo.required' => 'El título es obligatorio.',
            'categoria.in' => 'Seleccione una categoría válida.',
            'resumen.required' => 'Debe ingresar un resumen breve para la vista previa.',
            'contenido.required' => 'El contenido de la novedad es obligatorio.',
            'contenido.max' => 'El contenido supera la extensión máxima permitida.',
            'imagen.image' => 'El archivo adjunto debe ser una imagen válida.',
            'imagen.mimes' => 'Solo se permiten imágenes en formato JPG, PNG o WEBP.',
            'imagen.max' => 'La imagen no debe superar los 2 MB.',
        ]);

        $rutaImagen = $novedad->imagen;

        if ($request->hasFile('imagen')) {
            if ($novedad->imagen) {
                Storage::disk('public')->delete($novedad->imagen);
            }
            $rutaImagen = $request->file('imagen')->store('novedades', 'public');
        } elseif ($request->boolean('eliminar_imagen') && $novedad->imagen) {
            Storage::disk('public')->delete($novedad->imagen);
            $rutaImagen = null;
        }

        $publicado = $request->boolean('publicado');
        $fechaPublicacion = $novedad->fecha_publicacion;
        if ($publicado && !$fechaPublicacion) {
            $fechaPublicacion = now();
        }

        $tituloLimpio = trim($datos['titulo']);
        $slug = $tituloLimpio !== $novedad->titulo
            ? Novedad::generarSlugUnico($tituloLimpio, $novedad->id)
            : $novedad->slug;

        $novedad->update([
            'titulo' => $tituloLimpio,
            'slug' => $slug,
            'categoria' => $datos['categoria'],
            'resumen' => trim($datos['resumen']),
            'contenido' => trim($datos['contenido']),
            'imagen' => $rutaImagen,
            'publicado' => $publicado,
            'fecha_publicacion' => $fechaPublicacion,
        ]);

        return redirect()
            ->route('admin.novedades.index')
            ->with('success', 'Novedad actualizada correctamente.');
    }

    public function togglePublicacion(Novedad $novedad)
    {
        $nuevoEstado = !$novedad->publicado;

        $novedad->update([
            'publicado' => $nuevoEstado,
            'fecha_publicacion' => $nuevoEstado && !$novedad->fecha_publicacion ? now() : $novedad->fecha_publicacion,
        ]);

        $mensaje = $nuevoEstado
            ? "La novedad \"{$novedad->titulo}\" ahora es visible en el portal."
            : "La novedad \"{$novedad->titulo}\" pasó a estado borrador (oculta al público).";

        return redirect()
            ->route('admin.novedades.index')
            ->with('success', $mensaje);
    }

    public function destroy(Novedad $novedad)
    {
        if ($novedad->imagen) {
            Storage::disk('public')->delete($novedad->imagen);
        }

        $novedad->delete();

        return redirect()
            ->route('admin.novedades.index')
            ->with('warning', 'Novedad eliminada definitivamente.');
    }
}