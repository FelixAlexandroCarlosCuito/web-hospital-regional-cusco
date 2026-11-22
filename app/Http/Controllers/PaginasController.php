<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Novedad;

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

    
}