<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Novedad;
use App\Models\Cita;
use App\Models\Especialidad;

class PaginasController extends Controller
{
    public function inicio()
    {
        return view('inicio');
    }

    /**
     * Muestra el listado público de novedades con filtro opcional por categoría.
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
     * Muestra el catálogo público de especialidades activas junto con su staff médico vigente.
     */
    public function especialidades()
    {
        $especialidades = Especialidad::where('activo', true)
            ->with(['medicos' => function ($query) {
                $query->where('activo', true)->orderBy('apellidos');
            }])
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