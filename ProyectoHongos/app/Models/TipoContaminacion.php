<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class TipoContaminacion extends Model
{
    use CrudTrait;

    protected $table = 'cultivo.tipos_contaminacion';
    protected $primaryKey = 'tipo_contaminacion_id';
    public $timestamps = false;
    
    protected $fillable = [
        'nombre_comun',
        'agente_causal'
    ];

    protected $guarded = ['tipo_contaminacion_id'];

    // Relaciones
    public function unidadesProduccion()
    {
        return $this->hasMany(UnidadProduccion::class, 'tipo_contaminacion_id', 'tipo_contaminacion_id');
    }
}    
