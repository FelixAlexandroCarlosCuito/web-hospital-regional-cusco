<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Revoca de inmediato cualquier sesión activa perteneciente a una cuenta desactivada.
 */
class CheckUsuarioActivo
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Esta cuenta se encuentra desactivada.',
                ], 403);
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'numero_documento' => 'Esta cuenta se encuentra desactivada. Comuníquese con Administración.',
                ]);
        }

        return $next($request);
    }
}