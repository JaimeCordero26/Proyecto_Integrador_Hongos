<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadProduccion extends Model
{
    use HasFactory;

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
        'notas_contaminacion',
    ];

    protected $casts = [
        'fecha_inoculacion' => 'date',
        'peso_inicial_gramos' => 'decimal:2',
    ];

    /**
     * Relación con LoteProduccion
     */
    public function loteProduccion(): BelongsTo
    {
        return $this->belongsTo(LoteProduccion::class, 'lote_id', 'lote_id');
    }

    /**
     * Relación con TipoContaminacion
     */
    public function tipoContaminacion(): BelongsTo
    {
        return $this->belongsTo(TipoContaminacion::class, 'tipo_contaminacion_id', 'tipo_contaminacion_id');
    }

    /**
     * Relación con Cosechas
     */
    public function cosechas(): HasMany
    {
        return $this->hasMany(Cosecha::class, 'unidad_id', 'unidad_id');
    }

    /**
     * Scope para unidades activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado_unidad', 'Activo');
    }

    /**
     * Scope para unidades contaminadas
     */
    public function scopeContaminadas($query)
    {
        return $query->where('estado_unidad', 'Contaminado');
    }

    /**
     * Accessor para días desde inoculación
     */
    public function getDiasDesdeInoculacionAttribute(): int
    {
        return $this->fecha_inoculacion->diffInDays(now());
    }

    /**
     * Accessor para peso total cosechado
     */
    public function getPesoTotalCosechadoAttribute(): float
    {
        return $this->cosechas->sum('peso_cosecha_gramos');
    }

    /**
     * Accessor para eficiencia total
     */
    public function getEficienciaTotalAttribute(): float
    {
        if ($this->peso_inicial_gramos > 0) {
            return ($this->peso_total_cosechado / $this->peso_inicial_gramos) * 100;
        }
        return 0;
    }

    public function getNombreTipoContaminacionAttribute(): ?string
    {
        return $this->tipoContaminacion?->nombre_comun;
    }


}