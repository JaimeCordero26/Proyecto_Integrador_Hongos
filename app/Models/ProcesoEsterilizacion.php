<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcesoEsterilizacion extends Model
{
    use HasFactory;

    protected $table = 'administracion.procesos_esterilizacion';
    protected $primaryKey = 'proceso_id';
    public $timestamps = false;

    protected $fillable = [
        'metodo',
        'temperatura_alcanzada_c',
        'presion_psi',
        'duracion_minutos',
        'descripcion_adicional',
    ];

    protected $casts = [
        'temperatura_alcanzada_c' => 'decimal:2',
        'presion_psi' => 'decimal:2',
        'duracion_minutos' => 'integer',
    ];

    /**
     * Relación con LotesInoculo
     */
    public function lotesInoculo(): HasMany
    {
        return $this->hasMany(LoteInoculo::class, 'proceso_esterilizacion_id', 'proceso_id');
    }

    /**
     * Relación con LotesProduccion
     */
    public function lotesProduccion(): HasMany
    {
        return $this->hasMany(LoteProduccion::class, 'proceso_esterilizacion_id', 'proceso_id');
    }

}