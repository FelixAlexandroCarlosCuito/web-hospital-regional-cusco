<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Novedad;
use Illuminate\Http\Request;

/**
 * Atiende las vistas públicas del portal institucional con selección acotada de columnas.
 */
class PaginasController extends Controller
{
    public function inicio()
    {
        $totalEspecialidades = Especialidad::where('activo', true)->count();
        $totalMedicos = Medico::where('activo', true)->count();

        $especialidadesDestacadas = Especialidad::select(['id', 'nombre', 'descripcion', 'imagen'])
            ->where('activo', true)
            ->withCount(['medicos as medicos_activos_count' => fn ($q) => $q->where('activo', true)])
            ->orderByDesc('medicos_activos_count')
            ->orderBy('nombre')
            ->take(6)
            ->get();

        // Excluye el campo texto largo 'contenido' en listados para reducir transferencia desde MySQL
        $ultimasNovedades = Novedad::publicadas()
            ->select(['id', 'titulo', 'slug', 'categoria', 'resumen', 'imagen', 'fecha_publicacion'])
            ->take(3)
            ->get();

        return view('inicio', compact(
            'totalEspecialidades',
            'totalMedicos',
            'especialidadesDestacadas',
            'ultimasNovedades'
        ));
    }

    public function novedades(Request $request)
    {
        $categoriaActual = $request->query('categoria');
        $categorias = Novedad::CATEGORIAS;

        $consulta = Novedad::publicadas()
            ->select(['id', 'titulo', 'slug', 'categoria', 'resumen', 'imagen', 'fecha_publicacion']);

        if ($categoriaActual && in_array($categoriaActual, $categorias, true)) {
            $consulta->where('categoria', $categoriaActual);
        } else {
            $categoriaActual = null;
        }

        $novedades = $consulta->paginate(6)->withQueryString();

        return view('novedades.index', compact('novedades', 'categorias', 'categoriaActual'));
    }

    public function verNovedad(string $slug)
    {
        $novedad = Novedad::select([
            'id', 'user_id', 'titulo', 'slug', 'categoria', 'resumen', 'contenido', 'imagen', 'fecha_publicacion',
        ])
            ->with('autor:id,nombres,apellido_paterno')
            ->where('slug', $slug)
            ->where('publicado', true)
            ->firstOrFail();

        $recientes = Novedad::publicadas()
            ->select(['id', 'titulo', 'slug', 'categoria', 'imagen', 'fecha_publicacion'])
            ->where('id', '!=', $novedad->id)
            ->take(3)
            ->get();

        return view('novedades.show', compact('novedad', 'recientes'));
    }

    public function especialidades()
    {
        $especialidades = Especialidad::select(['id', 'nombre', 'descripcion', 'imagen'])
            ->where('activo', true)
            ->withExists([
                'medicos as tiene_turnos' => fn ($query) => $query->where('activo', true),
            ])
            ->orderBy('nombre')
            ->get();

        return view('paginas.especialidades', compact('especialidades'));
    }

    public function guiaContacto()
    {
        $horasCancelacion = Cita::HORAS_MINIMAS_CANCELACION;

        return view('paginas.guia-contacto', compact('horasCancelacion'));
    }
}