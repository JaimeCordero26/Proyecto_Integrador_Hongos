<?php

namespace App\Http\Middleware;

use Closure;

class EnsurePermission
{
    public function handle($request, Closure $next, string $permiso)
    {
        if (!auth()->check() || !auth()->user()->tienePermiso($permiso)) {
            abort(403, 'Permiso requerido: '.$permiso);
        }
        return $next($request);
    }
}
