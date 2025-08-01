<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InventarioLaboratorioRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class InventarioLaboratorioCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\InventarioLaboratorio::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/inventario-laboratorio');
        CRUD::setEntityNameStrings('item de inventario', 'inventario de laboratorio');
    }

	protected function setupListOperation()
{
    \Log::info('=== INVENTARIO SETUP DEBUG ===');
    
    // Test básico del modelo
    $modelClass = $this->crud->model;
    \Log::info("Model class: " . get_class($modelClass));
    \Log::info("Table: " . $modelClass->getTable());
    \Log::info("Primary key: " . $modelClass->getKeyName());
    
    // Test de consulta
    $count = $modelClass::count();
    \Log::info("Total count: " . $count);
    
    if ($count > 0) {
        $items = $modelClass::take(3)->get();
        \Log::info("Sample items: " . json_encode($items->toArray()));
        
        // Test de paginación (como lo hace Backpack)
        $paginated = $modelClass::paginate(25);
        \Log::info("Paginated count: " . $paginated->count());
        \Log::info("Paginated total: " . $paginated->total());
    }
    
    // Configurar columnas simples
    CRUD::column('item_id')->label('ID');
    CRUD::column('nombre_item')->label('Nombre');
    CRUD::column('cantidad_total')->label('Total');
    CRUD::column('cantidad_disponible')->label('Disponible');
    CRUD::column('estado_item')->label('Estado');
    
    \Log::info('=== END INVENTARIO SETUP DEBUG ===');
}

    protected function setupCreateOperation()
    {
        CRUD::setValidation(InventarioLaboratorioRequest::class);

        CRUD::addField([
            'name' => 'nombre_item',
            'type' => 'text',
            'label' => 'Nombre del Item',
            'attributes' => [
                'required' => true,
                'maxlength' => 255
            ]
        ]);
        
        CRUD::addField([
            'name' => 'descripcion',
            'type' => 'textarea',
            'label' => 'Descripción',
            'attributes' => [
                'rows' => 3
            ]
        ]);
        
        CRUD::addField([
            'name' => 'cantidad_total',
            'type' => 'number',
            'label' => 'Cantidad Total',
            'attributes' => [
                'required' => true,
                'min' => 0,
                'step' => 1
            ]
        ]);
        
        CRUD::addField([
            'name' => 'cantidad_disponible',
            'type' => 'number',
            'label' => 'Cantidad Disponible',
            'attributes' => [
                'required' => true,
                'min' => 0,
                'step' => 1
            ]
        ]);
        
        CRUD::addField([
            'name' => 'ubicacion',
            'type' => 'text',
            'label' => 'Ubicación',
            'attributes' => [
                'maxlength' => 255
            ]
        ]);
        
        CRUD::addField([
            'name' => 'estado_item',
            'type' => 'select_from_array',
            'label' => 'Estado del Item',
            'options' => [
                'Disponible' => 'Disponible',
                'Agotado' => 'Agotado',
                'En mantenimiento' => 'En mantenimiento',
                'Dañado' => 'Dañado',
                'Reservado' => 'Reservado'
            ],
            'attributes' => [
                'required' => true
            ]
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        CRUD::addColumn([
            'name' => 'item_id',
            'label' => 'ID',
            'type' => 'number'
        ]);
        
        CRUD::addColumn([
            'name' => 'nombre_item',
            'label' => 'Nombre del Item',
            'type' => 'text'
        ]);
        
        CRUD::addColumn([
            'name' => 'descripcion',
            'label' => 'Descripción',
            'type' => 'text'
        ]);
        
        CRUD::addColumn([
            'name' => 'cantidad_total',
            'label' => 'Cantidad Total',
            'type' => 'number'
        ]);
        
        CRUD::addColumn([
            'name' => 'cantidad_disponible',
            'label' => 'Disponible',
            'type' => 'number'
        ]);
        
        CRUD::addColumn([
            'name' => 'ubicacion',
            'label' => 'Ubicación',
            'type' => 'text'
        ]);
        
        CRUD::addColumn([
            'name' => 'estado_item',
            'label' => 'Estado',
            'type' => 'text'
        ]);
    }
}
