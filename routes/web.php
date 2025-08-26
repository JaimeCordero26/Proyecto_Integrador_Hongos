<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ReporteController;

/**
 * Redirige la raíz "/" directamente al login de Filament.
 * Así, al abrir http://127.0.0.1:8000 entras al login.
 */
Route::redirect('/', '/admin/login');

// Panel de admin: solo usuarios con rol ADMIN
Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    Route::get('/admin/panel', [AdminPanelController::class, 'index'])->name('admin.panel');
});

// Rutas protegidas por autenticación y permisos
Route::middleware(['auth'])->group(function () {
    Route::post('/reservas', [ReservaController::class, 'store'])
        ->middleware('permiso:reservas.crear')
        ->name('reservas.store');

    Route::put('/reservas/{id}', [ReservaController::class, 'update'])
        ->middleware('permiso:reservas.editar')
        ->name('reservas.update');

    Route::get('/reportes', [ReporteController::class, 'index'])
        ->can('reportes.ver')
        ->name('reportes.index');
});
