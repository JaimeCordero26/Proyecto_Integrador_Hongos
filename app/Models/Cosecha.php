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

    /**
     * Relación con UnidadProduccion
     */
    public function unidadProduccion(): BelongsTo
    {
        return $this->belongsTo(UnidadProduccion::class, 'unidad_id', 'unidad_id');
    }

    /**
     * Accessor para obtener la cepa a través de la unidad
     */
    public function getCepaAttribute()
    {
        return $this->unidadProduccion?->loteProduccion?->cepa;
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

    /**
     * Calculates the biological efficiency for this harvest.
     * The formula is (fresh mushroom weight in kg / dry substrate weight in kg) * 100.
     * This method automatically converts grams to kilograms.
     *
     * @return float The calculated biological efficiency percentage.
     */
    public function calcularEficienciaBiologica(): float
    {
        // 1. Get the fresh mushroom weight in grams and convert to kilograms.
        $pesoHongosKg = $this->peso_cosecha_gramos / 1000;

        // 2. Get the dry substrate weight from the related Production Lot.
        //    It follows the relationship from Cosecha -> UnidadProduccion -> LoteProduccion.
        $pesoSustratoSecoKg = $this->unidadProduccion->loteProduccion->peso_sustrato_seco_kg ?? 0;

        // 3. Avoid division by zero.
        if ($pesoSustratoSecoKg <= 0) {
            return 0.0;
        }

        // 4. Apply the biological efficiency formula.
        return ($pesoHongosKg / $pesoSustratoSecoKg) * 100;
    }

    /**
     * The "booting" method of the model.
     * It automatically calculates the biological efficiency before saving the record.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($cosecha) {
            $cosecha->eficiencia_biologica_calculada = $cosecha->calcularEficienciaBiologica();
        });
    }
}
