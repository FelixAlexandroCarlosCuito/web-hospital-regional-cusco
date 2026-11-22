<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Novedad;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;

class PaginasController extends Controller
{
    /**
     * Renderiza la portada institucional con indicadores reales, especialidades activas y últimas novedades.
     */
    public function inicio()
    {
        $totalEspecialidades = Especialidad::where('activo', true)->count();
        $totalMedicos = Medico::where('activo', true)->count();

        $especialidadesDestacadas = Especialidad::where('activo', true)
            ->withCount(['medicos as medicos_activos_count' => fn ($q) => $q->where('activo', true)])
            ->orderByDesc('medicos_activos_count')
            ->orderBy('nombre')
            ->take(6)
            ->get();

        $ultimasNovedades = Novedad::publicadas()
            ->take(3)
            ->get();

        return view('inicio', compact(
            'totalEspecialidades',
            'totalMedicos',
            'especialidadesDestacadas',
            'ultimasNovedades'
        ));
    }

    /**
     * Muestra el portal público de novedades con filtro opcional por categoría y paginación.
     */
    public function novedades(Request $request)
    {
        $categoriaActual = $request->query('categoria');
        $categorias = Novedad::CATEGORIAS;

        $consulta = Novedad::publicadas();

        if ($categoriaActual && in_array($categoriaActual, $categorias, true)) {
            $consulta->where('categoria', $categoriaActual);
        } else {
            $categoriaActual = null;
        }

        $novedades = $consulta->paginate(6)->withQueryString();

        return view('novedades.index', compact('novedades', 'categorias', 'categoriaActual'));
    }

    /**
     * Muestra el contenido completo de una novedad publicada y sugerencias recientes.
     */
    public function verNovedad(string $slug)
    {
        $novedad = Novedad::with('autor')
            ->where('slug', $slug)
            ->where('publicado', true)
            ->firstOrFail();

        $recientes = Novedad::publicadas()
            ->where('id', '!=', $novedad->id)
            ->take(3)
            ->get();

        return view('novedades.show', compact('novedad', 'recientes'));
    }

    /**
     * Muestra la cartera pública de especialidades activas y su disponibilidad operativa
     */
    public function especialidades()
    {
        $especialidades = Especialidad::where('activo', true)
            ->withExists([
                'medicos as tiene_turnos' => fn ($query) => $query->where('activo', true),
            ])
            ->orderBy('nombre')
            ->get();

        return view('paginas.especialidades', compact('especialidades'));
    }

    /**
     * Renderiza la guía operativa de atención, preguntas frecuentes y canales de contacto.
     */
    public function guiaContacto()
    {
        // Se lee desde el modelo para mantener una única fuente de verdad sobre la regla de cancelación
        $horasCancelacion = Cita::HORAS_MINIMAS_CANCELACION;

        return view('paginas.guia-contacto', compact('horasCancelacion'));
    }
    
}