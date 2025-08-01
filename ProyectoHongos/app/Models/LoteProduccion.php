<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class LoteProduccion extends Model
{
    use CrudTrait;

    protected $table = 'cultivo.lotes_produccion';
    protected $primaryKey = 'lote_id';
    public $timestamps = false;
    
    protected $fillable = [
        'cepa_id',
        'lote_inoculo_id',
        'proceso_esterilizacion_id',
        'sala_id',
        'usuario_creador_id',
        'fecha_creacion_lote',
        'metodologia_inoculacion',
        'notas_generales_lote'
    ];

    protected $guarded = ['lote_id'];

    protected $dates = [
        'fecha_creacion_lote'
    ];

    // Relaciones
    public function cepa()
    {
        return $this->belongsTo(Cepa::class, 'cepa_id', 'cepa_id');
    }

    public function loteInoculo()
    {
        return $this->belongsTo(LoteInoculo::class, 'lote_inoculo_id', 'lote_inoculo_id');
    }

    public function procesoEsterilizacion()
    {
        return $this->belongsTo(ProcesoEsterilizacion::class, 'proceso_esterilizacion_id', 'proceso_id');
    }

    public function sala()
    {
        return $this->belongsTo(SalasCultivo::class, 'sala_id', 'sala_id');
    }

    public function usuarioCreador()
    {
        return $this->belongsTo(Usuario::class, 'usuario_creador_id', 'usuario_id');
    }

    public function lotesSustratos()
    {
        return $this->hasMany(LoteSustrato::class, 'lote_id', 'lote_id');
    }

    public function unidadesProduccion()
    {
        return $this->hasMany(UnidadProduccion::class, 'lote_id', 'lote_id');
    }
}
