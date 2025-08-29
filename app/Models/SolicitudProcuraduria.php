<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudProcuraduria extends Model
{
    use HasFactory;

    protected $table = 'administracion.solicitudes_procuraduria';
    protected $primaryKey = 'solicitud_id';
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'descripcion_item_nuevo',
        'usuario_solicitante_id',
        'fecha_solicitud',
        'cantidad_solicitada',
        'justificacion',
        'estado_solicitud',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'cantidad_solicitada' => 'integer',
    ];

    /**
     * Relación con InventarioLaboratorio
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(InventarioLaboratorio::class, 'item_id', 'item_id');
    }

    /**
     * Relación con Usuario solicitante
     */
    public function usuarioSolicitante(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_solicitante_id', 'usuario_id');
    }

    /**
     * Scope para solicitudes pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado_solicitud', 'Pendiente');
    }

    /**
     * Scope para solicitudes aprobadas
     */
    public function scopeAprobadas($query)
    {
        return $query->where('estado_solicitud', 'Aprobada');
    }
    
    public function getItemNombreAttribute(): string
    {
        return $this->item?->nombre_item ?? 'N/A';
    }

    public function getUsuarioSolicitanteNombreAttribute(): string
    {
        return $this->usuarioSolicitante?->nombre_completo ?? 'N/A';
    }
}
