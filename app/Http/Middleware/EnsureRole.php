<?php

namespace App\Http\Middleware;

use Closure;

class EnsureRole
{
    public function handle($request, Closure $next, string $rol)
    {
        if (!auth()->check() || !auth()->user()->tieneRol($rol)) {
            abort(403, 'Rol requerido: '.$rol);
        }
        return $next($request);
    }
}
