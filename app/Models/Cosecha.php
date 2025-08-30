<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Cosecha extends Model
{
    use HasFactory;

    protected $table = 'cultivo.cosechas';
    protected $primaryKey = 'cosecha_id';
    public $timestamps = false;

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

    public function unidadProduccion(): BelongsTo
    {
        return $this->belongsTo(UnidadProduccion::class, 'unidad_id', 'unidad_id');
    }

    public function getCepaAttribute()
    {
        return $this->unidadProduccion?->loteProduccion?->cepa;
    }

    public function scopeDeCepa($query, $cepaId)
    {
        return $query->whereHas('unidadProduccion.loteProduccion', function ($q) use ($cepaId) {
            $q->where('cepa_id', $cepaId);
        });
    }

    public function scopeAltaEficiencia($query, $minimo = 50)
    {
        return $query->where('eficiencia_biologica_calculada', '>=', $minimo);
    }

    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('fecha_cosecha', '>=', now()->subDays($dias));
    }

    /**
     * Calcula la eficiencia biológica usando la función de PostgreSQL
     * @return float|null
     */
    public function calcularEficienciaBiologica(): ?float
    {
        if (!$this->peso_cosecha_gramos) {
            return null;
        }

        $pesoSustratoSecoKg = $this->unidadProduccion?->loteProduccion?->peso_sustrato_seco_kg;
        
        if (!$pesoSustratoSecoKg || $pesoSustratoSecoKg <= 0) {
            return null;
        }

        try {
            // Convertir gramos a kilogramos para el cálculo
            $pesoHongosKg = $this->peso_cosecha_gramos / 1000;
            
            // Usar la función de PostgreSQL
            $resultado = DB::selectOne(
                'SELECT cultivo.calcular_eficiencia_biologica(?, ?) as eficiencia',
                [$pesoHongosKg, $pesoSustratoSecoKg]
            );

            return $resultado?->eficiencia;
        } catch (\Exception $e) {
            \Log::error('Error calculando eficiencia biológica con función BD: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Método estático para calcular eficiencia usando directamente los valores
     * @param float $pesoHongosKg
     * @param float $pesoSustratoKg
     * @return float|null
     */
    public static function calcularEficienciaEstatica(float $pesoHongosKg, float $pesoSustratoKg): ?float
    {
        if ($pesoSustratoKg <= 0) {
            return null;
        }

        try {
            $resultado = DB::selectOne(
                'SELECT cultivo.calcular_eficiencia_biologica(?, ?) as eficiencia',
                [$pesoHongosKg, $pesoSustratoKg]
            );

            return $resultado?->eficiencia;
        } catch (\Exception $e) {
            \Log::error('Error calculando eficiencia biológica estática con función BD: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Accessor para obtener la eficiencia formateada para PDFs
     */
    public function getEficienciaFormatoAttribute()
    {
        return $this->eficiencia_biologica_calculada 
            ? number_format($this->eficiencia_biologica_calculada, 2) . '%'
            : 'N/A';
    }

    /**
     * Accessor para obtener el peso formateado para PDFs
     */
    public function getPesoFormatoAttribute()
    {
        return number_format($this->peso_cosecha_gramos, 0) . ' g';
    }

    /**
     * Accessor para obtener la fecha formateada para PDFs
     */
    public function getFechaFormatoAttribute()
    {
        return $this->fecha_cosecha?->format('d/m/Y') ?? 'Sin fecha';
    }

    /**
     * Accessor para obtener el código de unidad para PDFs
     */
    public function getUnidadCodigoAttribute()
    {
        return $this->unidadProduccion?->codigo_unidad ?? 'Sin código';
    }

    /**
     * Boot method para eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($cosecha) {
            // Solo calcular si hay cambios en peso o unidad
            if ($cosecha->isDirty(['peso_cosecha_gramos', 'unidad_id'])) {
                $cosecha->eficiencia_biologica_calculada = $cosecha->calcularEficienciaBiologica();
            }
        });
    }
}