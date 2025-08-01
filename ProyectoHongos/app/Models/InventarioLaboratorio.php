<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class InventarioLaboratorio extends Model
{
    use CrudTrait;

    protected $table = 'administracion.inventario_laboratorio';
    protected $primaryKey = 'item_id';
    public $timestamps = false;
    
    protected $fillable = [
        'nombre_item',
        'descripcion',
        'cantidad_total',
        'cantidad_disponible',
        'ubicacion',
        'estado_item'
    ];

    protected $guarded = [
        'item_id'
    ];

    protected $casts = [
        'cantidad_total' => 'integer',
        'cantidad_disponible' => 'integer'
    ];

    // Relaciones
    public function solicitudesProcuraduria()
    {
        return $this->hasMany(SolicitudProcuraduria::class, 'item_id', 'item_id');
    }

    // Métodos auxiliares
    public function getCantidadUsadaAttribute()
    {
        return $this->cantidad_total - $this->cantidad_disponible;
    }

    public function getPorcentajeDisponibleAttribute()
    {
        if ($this->cantidad_total == 0) return 0;
        return round(($this->cantidad_disponible / $this->cantidad_total) * 100, 1);
    }
}
