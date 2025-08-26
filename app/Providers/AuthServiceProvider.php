<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // SUPERADMIN: acceso total
        Gate::before(function ($user, $ability) {
            return method_exists($user, 'tieneRol') && $user->tieneRol('SUPERADMIN') ? true : null;
        });

        // No usamos Policies; resolvemos permisos directo
        Gate::guessPolicyNamesUsing(fn () => null);

        // Cualquier "ability" es un permiso en tu BD
        Gate::define('*', fn ($user, $ability) =>
            method_exists($user, 'tienePermiso') && $user->tienePermiso($ability)
        );
    }
}
