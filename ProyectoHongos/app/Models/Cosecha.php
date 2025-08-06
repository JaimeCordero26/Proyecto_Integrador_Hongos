<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cosecha extends Model
{
    use HasFactory;

    protected $table = 'cultivo.cosechas';
    protected $primaryKey = 'cosecha_id';

    protected $fillable = [
        'unidad_id',
        'numero_cosecha',
        'fecha_cosecha',
        'peso_cosecha_gramos',
        'eficiencia_biologica_calculada',
    ];

    protected $casts = [
        'fecha_cosecha' => 'date',
        'peso_cosecha_gramos' => 'decimal:2',
        'eficiencia_biologica_calculada' => 'decimal:2',
    ];

    /**
     * Relación con UnidadProduccion
     */
    public function unidadProduccion(): BelongsTo
    {
        return $this->belongsTo(UnidadProduccion::class, 'unidad_id', 'unidad_id');
    }

    /**
     * Accessor para obtener la eficiencia con formato
     */
    public function getEficienciaFormateadaAttribute(): string
    {
        return number_format($this->eficiencia_biologica_calculada, 2) . '%';
    }

    /**
     * Accessor para obtener el peso con formato
     */
    public function getPesoFormateadoAttribute(): string
    {
        return number_format($this->peso_cosecha_gramos, 1) . ' g';
    }

    /**
     * Scope para cosechas de una cepa específica
     */
    public function scopeDeCepa($query, $cepaId)
    {
        return $query->whereHas('unidadProduccion.loteProduccion', function ($q) use ($cepaId) {
            $q->where('cepa_id', $cepaId);
        });
    }

    /**
     * Scope para cosechas con alta eficiencia
     */
    public function scopeAltaEficiencia($query, $minimo = 50)
    {
        return $query->where('eficiencia_biologica_calculada', '>=', $minimo);
    }

    /**
     * Scope para cosechas recientes
     */
    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('fecha_cosecha', '>=', now()->subDays($dias));
    }
}
