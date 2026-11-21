<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRolAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->rol === 'admin' && Auth::user()->activo) {
            return $next($request);
        }

        abort(403, 'Acceso denegado. Área exclusiva para Administración del sistema.');
    }
}