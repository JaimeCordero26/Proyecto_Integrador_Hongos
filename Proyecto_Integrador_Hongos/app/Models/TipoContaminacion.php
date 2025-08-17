<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoContaminacion extends Model
{
    use HasFactory;

    protected $table = 'cultivo.tipos_contaminacion';
    protected $primaryKey = 'tipo_contaminacion_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre_comun',
        'agente_causal',
    ];

    /**
     * Relación con UnidadesProduccion
     */
    public function unidadesProduccion(): HasMany
    {
        return $this->hasMany(UnidadProduccion::class, 'tipo_contaminacion_id', 'tipo_contaminacion_id');
    }
}