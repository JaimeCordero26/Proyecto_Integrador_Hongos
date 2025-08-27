<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\ReportesController;

Route::redirect('/', '/admin/login');

Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    Route::get('/admin/panel', [AdminPanelController::class, 'index'])->name('admin.panel');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/reportes', [ReporteController::class, 'index'])
        ->middleware('permiso:reportes.ver')
        ->name('reportes.index');
});

Route::get('/reportes/ambiental', [ReportesController::class, 'reporteAmbiental']);
Route::get('/reportes/cosechas/pdf', [ReportesController::class, 'reporteCosechaPDF']);
Route::get('/reportes/acciones/pdf', [ReportesController::class, 'reporteAccionesPDF'])->name('reportes.acciones.pdf');
