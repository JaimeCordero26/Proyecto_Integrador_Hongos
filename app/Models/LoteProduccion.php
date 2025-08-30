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
        'peso_sustrato_seco_kg',
    ];

    protected $casts = [
        'fecha_creacion_lote'   => 'date',
        'peso_sustrato_seco_kg' => 'float',
    ];

    // Para que salgan en ->toArray() (reportes, APIs, etc.)
    protected $appends = [
        'cepa_nombre',
        'proceso_esterilizacion_nombre',
        'sala_nombre',
        'usuario_creador_nombre',
        'peso_sustrato_seco_kg_fmt',
    ];


    public function cepa(): BelongsTo
    {
        return $this->belongsTo(Cepa::class, 'cepa_id', 'cepa_id');
    }

    public function loteInoculo(): BelongsTo
    {
        return $this->belongsTo(LoteInoculo::class, 'lote_inoculo_id', 'lote_inoculo_id');
    }

    public function procesoEsterilizacion(): BelongsTo
    {
        return $this->belongsTo(ProcesoEsterilizacion::class, 'proceso_esterilizacion_id', 'proceso_id');
    }

    public function salaCultivo(): BelongsTo
    {
        return $this->belongsTo(SalaCultivo::class, 'sala_id', 'sala_id');
    }

    public function usuarioCreador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_creador_id', 'usuario_id');
    }

    public function unidadesProduccion(): HasMany
    {
        return $this->hasMany(UnidadProduccion::class, 'lote_id', 'lote_id');
    }

    public function loteSustratos(): HasMany
    {
        return $this->hasMany(LoteSustrato::class, 'lote_id', 'lote_id');
    }


    public function getCepaNombreAttribute(): string
    {
        return $this->cepa?->nombre_cientifico ?? 'Sin cepa';
    }

    public function getProcesoEsterilizacionNombreAttribute(): string
    {
        return $this->procesoEsterilizacion?->metodo ?? 'Sin proceso';
    }

    public function getSalaNombreAttribute(): string
    {
        return $this->salaCultivo?->nombre_sala ?? 'Sin sala';
    }

    public function getUsuarioCreadorNombreAttribute(): string
    {
        return $this->usuarioCreador?->nombre_completo ?? 'Sin usuario';
    }
    public function getPesoSustratoSecoKgAttribute($value): float
    {
        if ($value !== null) {
            return (float) $value;
        }

        if ($this->relationLoaded('loteSustratos')) {
            $totalG = (float) $this->loteSustratos->sum('cantidad_gramos');
        } else {
            $totalG = (float) $this->loteSustratos()->sum('cantidad_gramos');
        }

        return $totalG / 1000.0;
    }

    public function getPesoSustratoSecoKgFmtAttribute(): string
    {
        return number_format((float) $this->peso_sustrato_seco_kg, 3) . ' kg';
    }


    public function calcularPesoTotalKg(): float
    {
        $totalG = $this->relationLoaded('loteSustratos')
            ? (float) $this->loteSustratos->sum('cantidad_gramos')
            : (float) $this->loteSustratos()->sum('cantidad_gramos');

        return $totalG / 1000.0;
    }
}
