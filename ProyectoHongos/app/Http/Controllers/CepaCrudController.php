<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CepaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class CepaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Cepa::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/cepa');
        CRUD::setEntityNameStrings('cepa', 'cepas');
    }

    protected function setupListOperation()
    {
        CRUD::column('cepa_id')->label('ID')->type('number');
        CRUD::column('nombre_comun')->label('Nombre Común')->type('text');
        CRUD::column('nombre_cientifico')->label('Nombre Científico')->type('text');
        CRUD::column('codigo_interno')->label('Código Interno')->type('text');
        
        // Optimizar paginación
        CRUD::setDefaultPageLength(25);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(CepaRequest::class);

        CRUD::field('nombre_comun')
            ->type('text')
            ->label('Nombre Común')
            ->attributes(['required' => true]);
            
        CRUD::field('nombre_cientifico')
            ->type('text')
            ->label('Nombre Científico');
            
        CRUD::field('codigo_interno')
            ->type('text')
            ->label('Código Interno');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }
}
