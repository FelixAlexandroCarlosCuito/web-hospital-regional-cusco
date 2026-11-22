<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Representa comunicados, campañas y noticias institucionales publicadas por administración.
 */
class Novedad extends Model
{
    public const CATEGORIAS = [
        'Comunicado',
        'Campaña de Salud',
        'Horarios y Atención',
        'Institucional',
    ];

    // Nombre explícito porque la pluralización en inglés de Laravel generaría "novedads"
    protected $table = 'novedades';

    protected $fillable = [
        'user_id',
        'titulo',
        'slug',
        'categoria',
        'resumen',
        'contenido',
        'imagen',
        'publicado',
        'fecha_publicacion',
    ];

    protected $casts = [
        'publicado' => 'boolean',
        'fecha_publicacion' => 'datetime',
    ];

    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Filtra únicamente novedades visibles para el portal público ordenadas por fecha de publicación.
     */
    public function scopePublicadas($query)
    {
        return $query->where('publicado', true)
            ->orderByDesc('fecha_publicacion')
            ->orderByDesc('id');
    }

    /**
     * Genera un slug único a partir del título para evitar colisiones de URL.
     */
    public static function generarSlugUnico(string $titulo, ?int $ignorarId = null): string
    {
        $base = Str::slug($titulo) ?: 'novedad';
        $slug = $base;
        $contador = 2;

        while (self::where('slug', $slug)
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->exists()) {
            $slug = "{$base}-{$contador}";
            $contador++;
        }

        return $slug;
    }
}