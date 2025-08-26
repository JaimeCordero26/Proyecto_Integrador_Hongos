<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Blade::if('role', fn (string $rol) =>
            auth()->check() && auth()->user()->tieneRol($rol)
        );

        Blade::if('permiso', fn (string $perm) =>
            auth()->check() && auth()->user()->tienePermiso($perm)
        );
    }
}
