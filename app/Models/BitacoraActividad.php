<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BitacoraActividad extends Model
{
    use HasFactory;

    protected $table = 'administracion.bitacora_actividades';
    protected $primaryKey = 'bitacora_id';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'fecha_hora',
        'tipo_actividad',
        'descripcion_detallada',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    /**
     * Relación con Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'usuario_id');
    }

    /**
     * Scope para actividades recientes
     */
    public function scopeRecientes($query, $dias = 7)
    {
        return $query->where('fecha_hora', '>=', now()->subDays($dias));
    }

    /**
     * Scope para actividades por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_actividad', $tipo);
    }
}