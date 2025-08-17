<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroAmbiental extends Model
{
    use HasFactory;

    protected $table = 'cultivo.registros_ambientales';
    protected $primaryKey = 'registro_id';
    public $timestamps = false;

    protected $fillable = [
        'sala_id',
        'fecha_hora',
        'temperatura_celsius',
        'humedad_relativa',
        'co2_ppm',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'temperatura_celsius' => 'decimal:2',
        'humedad_relativa' => 'decimal:2',
        'co2_ppm' => 'integer',
    ];

    /**
     * Relación con SalaCultivo
     */
    public function salaCultivo(): BelongsTo
    {
        return $this->belongsTo(SalaCultivo::class, 'sala_id', 'sala_id');
    }

    /**
     * Scope para registros recientes
     */
    public function scopeRecientes($query, $dias = 7)
    {
        return $query->where('fecha_hora', '>=', now()->subDays($dias));
    }

    /**
     * Scope para registros de una sala
     */
    public function scopeDeSala($query, $salaId)
    {
        return $query->where('sala_id', $salaId);
    }
}