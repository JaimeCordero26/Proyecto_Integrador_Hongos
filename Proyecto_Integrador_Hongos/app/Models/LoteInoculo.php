<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoteInoculo extends Model
{
    use HasFactory;

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
        'notas',
    ];

    protected $casts = [
        'fecha_creacion' => 'date',
    ];

    /**
     * Relación con Cepa
     */
    public function cepa(): BelongsTo
    {
        return $this->belongsTo(Cepa::class, 'cepa_id', 'cepa_id');
    }

    /**
     * Relación con Usuario creador
     */
    public function usuarioCreador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_creador_id', 'usuario_id');
    }

    /**
     * Relación con ProcesoEsterilizacion
     */
    public function procesoEsterilizacion(): BelongsTo
    {
        return $this->belongsTo(ProcesoEsterilizacion::class, 'proceso_esterilizacion_id', 'proceso_id');
    }

    /**
     * Relación con LotesProduccion
     */
    public function lotesProduccion(): HasMany
    {
        return $this->hasMany(LoteProduccion::class, 'lote_inoculo_id', 'lote_inoculo_id');
    }
}