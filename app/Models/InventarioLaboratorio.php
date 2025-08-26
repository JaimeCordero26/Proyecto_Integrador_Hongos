<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventarioLaboratorio extends Model
{
    use HasFactory;

    protected $table = 'administracion.inventario_laboratorio';
    protected $primaryKey = 'item_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre_item',
        'descripcion',
        'cantidad_total',
        'cantidad_disponible',
        'ubicacion',
        'estado_item',
    ];

    protected $casts = [
        'cantidad_total' => 'integer',
        'cantidad_disponible' => 'integer',
    ];

    /**
     * Relación con SolicitudesProcuraduria
     */
    public function solicitudesProcuraduria(): HasMany
    {
        return $this->hasMany(SolicitudProcuraduria::class, 'item_id', 'item_id');
    }

    /**
     * Accessor para cantidad en uso
     */
    public function getCantidadEnUsoAttribute(): int
    {
        return $this->cantidad_total - $this->cantidad_disponible;
    }

    /**
     * Accessor para porcentaje disponible
     */
    public function getPorcentajeDisponibleAttribute(): float
    {
        if ($this->cantidad_total == 0) return 0;
        return round(($this->cantidad_disponible / $this->cantidad_total) * 100, 2);
    }

    /**
     * Scope para items con stock crítico
     */
    public function scopeStockCritico($query, $porcentaje = 10)
    {
        return $query->whereRaw('(cantidad_disponible::decimal / cantidad_total * 100) <= ?', [$porcentaje]);
    }
}