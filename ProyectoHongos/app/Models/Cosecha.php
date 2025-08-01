<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Cosecha extends Model
{
    use CrudTrait;

    protected $table = 'cultivo.cosechas';
    protected $primaryKey = 'cosecha_id';
    public $timestamps = false;
    
    protected $fillable = [
        'unidad_id',
        'numero_cosecha',
        'fecha_cosecha',
        'peso_cosecha_gramos',
        'eficiencia_biologica_calculada'
    ];

    protected $guarded = ['cosecha_id'];

    protected $dates = [
        'fecha_cosecha'
    ];

    protected $casts = [
        'numero_cosecha' => 'integer',
        'peso_cosecha_gramos' => 'decimal:2',
        'eficiencia_biologica_calculada' => 'decimal:2'
    ];

    // Relaciones
    public function unidadProduccion()
    {
        return $this->belongsTo(UnidadProduccion::class, 'unidad_id', 'unidad_id');
    }
}
