<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

/**
 * Configura la paginación con Bootstrap 5 y reglas de auditoría de Eloquent contra N+1 y asignación masiva.
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        $enDesarrollo = !$this->app->isProduction();

        // Detecta consultas N+1 por carga perezosa registrándolas en log sin interrumpir la navegación
        Model::preventLazyLoading($enDesarrollo);
        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation): void {
            Log::warning('Carga perezosa detectada en Eloquent', [
                'modelo' => $model::class,
                'relacion' => $relation,
            ]);
        });

        // Rechaza silencios al intentar asignar masivamente atributos excluidos de $fillable (ej. rol o activo)
        Model::preventSilentlyDiscardingAttributes($enDesarrollo);

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }
}