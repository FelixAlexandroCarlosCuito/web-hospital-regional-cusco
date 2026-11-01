<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRolAdmision
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->rol === 'admision') {
            return $next($request);
        }
        abort(403, 'Acceso denegado. Área exclusiva para personal de Admisión.');
    }
}