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
     * @return float 
     */
    public function calcularEficienciaBiologica(): float
    {
        $pesoHongosKg = $this->peso_cosecha_gramos / 1000;

        
        $pesoSustratoSecoKg = $this->unidadProduccion->loteProduccion->peso_sustrato_seco_kg ?? 0;

        if ($pesoSustratoSecoKg <= 0) {
            return 0.0;
        }

        return ($pesoHongosKg / $pesoSustratoSecoKg) * 100;
    }

    /**
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
