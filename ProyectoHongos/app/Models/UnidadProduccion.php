<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class UnidadProduccion extends Model
{
    use CrudTrait;

    protected $table = 'cultivo.unidades_produccion';
    protected $primaryKey = 'unidad_id';
    public $timestamps = false;
    
    protected $fillable = [
        'lote_id',
        'codigo_unidad',
        'peso_inicial_gramos',
        'fecha_inoculacion',
        'estado_unidad',
        'tipo_contaminacion_id',
        'notas_contaminacion'
    ];

    protected $guarded = ['unidad_id'];

    protected $dates = [
        'fecha_inoculacion'
    ];

    protected $casts = [
        'peso_inicial_gramos' => 'decimal:2'
    ];

    // Relaciones
    public function loteProduccion()
    {
        return $this->belongsTo(LoteProduccion::class, 'lote_id', 'lote_id');
    }

    public function tipoContaminacion()
    {
        return $this->belongsTo(TipoContaminacion::class, 'tipo_contaminacion_id', 'tipo_contaminacion_id');
    }

    public function cosechas()
    {
        return $this->hasMany(Cosecha::class, 'unidad_id', 'unidad_id');
    }
}
