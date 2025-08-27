<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Usuario::class => \App\Policies\UsuarioPolicy::class,
        \App\Models\Rol::class => \App\Policies\RolPolicy::class,
        \App\Models\Permiso::class => \App\Policies\PermisoPolicy::class,
    ];

    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'tieneRol') && $user->tieneRol('SUPERADMIN')) {
                return true;
            }
            if (method_exists($user, 'tienePermiso') && $user->tienePermiso($ability)) {
                return true;
            }
            return null;
        });
    }
}
