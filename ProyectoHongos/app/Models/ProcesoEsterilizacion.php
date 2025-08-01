<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class ProcesoEsterilizacion extends Model
{
    use CrudTrait;

    protected $table = 'administracion.procesos_esterilizacion';
    protected $primaryKey = 'proceso_id';
    public $timestamps = false;
    
    protected $fillable = [
        'metodo',
        'temperatura_alcanzada_c',
        'presion_psi',
        'duracion_minutos',
        'descripcion_adicional'
    ];

    protected $guarded = ['proceso_id'];

    protected $casts = [
        'temperatura_alcanzada_c' => 'decimal:1',
        'presion_psi' => 'decimal:1',
        'duracion_minutos' => 'integer'
    ];

    // Relaciones
    public function lotesInoculo()
    {
        return $this->hasMany(LoteInoculo::class, 'proceso_esterilizacion_id', 'proceso_id');
    }

    public function lotesProduccion()
    {
        return $this->hasMany(LoteProduccion::class, 'proceso_esterilizacion_id', 'proceso_id');
    }
}
