<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accion extends Model
{
    use HasFactory;

    protected $table = 'auditoria.acciones';
    protected $primaryKey = 'auditoria_id';
    public $timestamps = false;

    protected $fillable = [
        'tabla_afectada',
        'id_registro',
        'tipo_accion',
        'usuario_id',
        'fecha_hora',
        'descripcion',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'id_registro' => 'integer',
        'usuario_id' => 'integer',
    ];

    /**
     * Scope para acciones recientes
     */
    public function scopeRecientes($query, $dias = 7)
    {
        return $query->where('fecha_hora', '>=', now()->subDays($dias));
    }

    /**
     * Scope para acciones por tipo
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_accion', $tipo);
    }

    /**
     * Scope para acciones de una tabla
     */
    public function scopeDeTabla($query, $tabla)
    {
        return $query->where('tabla_afectada', $tabla);
    }

    /**
     * Scope para acciones de un usuario
     */
    public function scopeDeUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }
}