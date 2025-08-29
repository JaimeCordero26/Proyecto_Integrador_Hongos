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
     * Relación con el modelo Usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'usuario_id');
    }

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

    // Métodos para formatear datos para PDF

    /**
     * Obtiene el nombre del usuario para el PDF
     */
    public function getUsuarioNombreAttribute()
    {
        return $this->usuario ? $this->usuario->nombre_completo : 'Usuario no encontrado';
    }

    /**
     * Formatea la fecha y hora para el PDF
     */
    public function getFechaHoraFormatoAttribute()
    {
        return $this->fecha_hora ? $this->fecha_hora->format('d/m/Y H:i:s') : 'Sin fecha';
    }

    /**
     * Formatea el tipo de acción para el PDF
     */
    public function getTipoAccionFormatoAttribute()
    {
        $tipos = [
            'CREATE' => 'Crear',
            'UPDATE' => 'Actualizar',
            'DELETE' => 'Eliminar',
            'VIEW' => 'Ver',
            'LOGIN' => 'Iniciar Sesión',
            'LOGOUT' => 'Cerrar Sesión',
        ];

        return $tipos[$this->tipo_accion] ?? $this->tipo_accion;
    }

    /**
     * Formatea la descripción limitando caracteres si es muy larga
     */
    public function getDescripcionFormatoAttribute()
    {
        if (!$this->descripcion) {
            return 'Sin descripción';
        }

        return strlen($this->descripcion) > 100 
            ? substr($this->descripcion, 0, 97) . '...' 
            : $this->descripcion;
    }

    /**
     * Accessor para obtener todos los datos formateados para el PDF
     */
    public function getDatosParaPdfAttribute()
    {
        return [
            'auditoria_id' => $this->auditoria_id,
            'tabla_afectada' => $this->tabla_afectada ?? 'No especificada',
            'id_registro' => $this->id_registro ?? 'N/A',
            'tipo_accion' => $this->tipo_accion_formato,
            'usuario_nombre' => $this->usuario_nombre,
            'fecha_hora_formato' => $this->fecha_hora_formato,
            'descripcion' => $this->descripcion_formato,
        ];
    }
}