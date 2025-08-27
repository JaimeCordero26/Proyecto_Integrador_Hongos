<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\ReporteController;

Route::redirect('/', '/admin/login');

Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    Route::get('/admin/panel', [AdminPanelController::class, 'index'])->name('admin.panel');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/reportes', [ReporteController::class, 'index'])
        ->middleware('permiso:reportes.ver')
        ->name('reportes.index');
});
