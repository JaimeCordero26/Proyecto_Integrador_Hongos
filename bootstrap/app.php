<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'    => \App\Http\Middleware\EnsureRole::class,
            'permiso' => \App\Http\Middleware\EnsurePermission::class,
        ]);

        // (Opcional) globales: se aplican a TODAS las peticiones
        // $middleware->append(\App\Http\Middleware\EnsureRole::class);
        // $middleware->append(\App\Http\Middleware\EnsurePermission::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withProviders([
        \App\Providers\AppServiceProvider::class,
        \App\Providers\AuthServiceProvider::class,
    ])
    ->create();
