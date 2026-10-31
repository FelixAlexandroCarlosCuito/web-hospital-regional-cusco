<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Añadir esto

class CheckRolMedico
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificamos que esté logueado y que su rol sea estrictamente 'medico'
        if (Auth::check() && Auth::user()->rol === 'medico') {
            return $next($request);
        }

        // Si es un paciente curioso intentando entrar al panel médico, lo bloqueamos
        abort(403, 'Acceso denegado. Esta área es exclusiva para el personal médico del hospital.');
    }
}