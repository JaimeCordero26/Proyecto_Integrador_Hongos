<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoteProduccion extends Model
{
    use HasFactory;

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
        'notas_generales_lote',
    ];

    protected $casts = [
        'fecha_creacion_lote' => 'date',
    ];

    /**
     * Relación con Cepa
     */
    public function cepa(): BelongsTo
    {
        return $this->belongsTo(Cepa::class, 'cepa_id', 'cepa_id');
    }

    /**
     * Relación con LoteInoculo
     */
    public function loteInoculo(): BelongsTo
    {
        return $this->belongsTo(LoteInoculo::class, 'lote_inoculo_id', 'lote_inoculo_id');
    }

    /**
     * Relación con ProcesoEsterilizacion
     */
    public function procesoEsterilizacion(): BelongsTo
    {
        return $this->belongsTo(ProcesoEsterilizacion::class, 'proceso_esterilizacion_id', 'proceso_id');
    }

    /**
     * Relación con SalaCultivo
     */
    public function salaCultivo(): BelongsTo
    {
        return $this->belongsTo(SalaCultivo::class, 'sala_id', 'sala_id');
    }

    /**
     * Relación con Usuario creador
     */
    public function usuarioCreador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_creador_id', 'usuario_id');
    }

    /**
     * Relación con UnidadesProduccion
     */
    public function unidadesProduccion(): HasMany
    {
        return $this->hasMany(UnidadProduccion::class, 'lote_id', 'lote_id');
    }

    /**
     * Relación con LoteSustratos
     */
    public function loteSustratos(): HasMany
    {
        return $this->hasMany(LoteSustrato::class, 'lote_id', 'lote_id');
    }
}