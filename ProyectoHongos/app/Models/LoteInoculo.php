<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class LoteInoculo extends Model
{
    use CrudTrait;

    protected $table = 'cultivo.lotes_inoculo';
    protected $primaryKey = 'lote_inoculo_id';
    public $timestamps = false;
    
    protected $fillable = [
        'cepa_id',
        'usuario_creador_id',
        'fecha_creacion',
        'sustrato_grano',
        'generacion',
        'proceso_esterilizacion_id',
        'notas'
    ];

    protected $guarded = ['lote_inoculo_id'];

    protected $dates = [
        'fecha_creacion'
    ];

    // Relaciones
    public function cepa()
    {
        return $this->belongsTo(Cepa::class, 'cepa_id', 'cepa_id');
    }

    public function usuarioCreador()
    {
        return $this->belongsTo(Usuario::class, 'usuario_creador_id', 'usuario_id');
    }

    public function procesoEsterilizacion()
    {
        return $this->belongsTo(ProcesoEsterilizacion::class, 'proceso_esterilizacion_id', 'proceso_id');
    }

    public function lotesProduccion()
    {
        return $this->hasMany(LoteProduccion::class, 'lote_inoculo_id', 'lote_inoculo_id');
    }
}
