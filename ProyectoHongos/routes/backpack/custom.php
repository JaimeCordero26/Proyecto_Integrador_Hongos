<?php

use Illuminate\Support\Facades\Route;

Route::group([
    "prefix" => config("backpack.base.route_prefix", "admin"),
    "middleware" => array_merge(
        (array) config("backpack.base.web_middleware", "web"),
        (array) config("backpack.base.middleware_key", "admin")
    ),
    "namespace" => "App\Http\Controllers\Admin",
], function () {
    // Las rutas CRUD se agregarán aquí
    Route::crud('salas-cultivo', 'SalasCultivoCrudController');
    Route::crud('procesos-esterilizacion', 'ProcesosEsterilizacionCrudController');
    Route::crud('inventario-laboratorio', 'InventarioLaboratorioCrudController');
    Route::crud('solicitudes-procuraduria', 'SolicitudesProcuraduriaCrudController');
    Route::crud('sustratos', 'SustratosCrudController');
    Route::crud('tipos-contaminacion', 'TiposContaminacionCrudController');
    Route::crud('cepas', 'CepasCrudController');
    Route::crud('lotes-inoculo', 'LotesInoculoCrudController');
    Route::crud('registros-ambientales', 'RegistrosAmbientalesCrudController');
    Route::crud('lotes-produccion', 'LotesProduccionCrudController');
    Route::crud('lote-sustratos', 'LoteSustratosCrudController');
    Route::crud('unidades-produccion', 'UnidadesProduccionCrudController');
    Route::crud('cosechas', 'CosechasCrudController');
    Route::crud('roles', 'RolesCrudController');
    Route::crud('permisos', 'PermisosCrudController');
    Route::crud('bitacora-actividades', 'BitacoraActividadesCrudController');
    Route::crud('acciones', 'AccionesCrudController');
});
