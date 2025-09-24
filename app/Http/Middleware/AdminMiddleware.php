<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Log de intento de acceso a admin
        Log::info('Intento de acceso a sección admin', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl()
        ]);

        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            Log::warning('Acceso denegado a admin: Usuario no autenticado', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder al panel de administración.');
        }

        // Verificar si el usuario es administrador
        // Por ahora usamos un campo 'is_admin' en la tabla users
        if (!Auth::user()->is_admin) {
            Log::warning('Acceso denegado a admin: Usuario no es administrador', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'ip' => $request->ip(),
                'url' => $request->fullUrl()
            ]);
            
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder al panel de administración.');
        }

        // Log de acceso exitoso
        Log::info('Acceso exitoso a sección admin', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'ip' => $request->ip(),
            'url' => $request->fullUrl()
        ]);

        return $next($request);
    }
}