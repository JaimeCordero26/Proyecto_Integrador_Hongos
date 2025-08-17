<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'seguridad.usuarios';
    protected $primaryKey = 'usuario_id';
    public $timestamps = false;

    protected $fillable = [
        'rol_id',
        'nombre_completo',
        'email',
        'password_hash',
        'activo',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // CRITICAL: Mapear los campos de autenticación
    public function getAuthIdentifierName()
    {
        return 'email'; // Campo para identificar usuario
    }

    public function getAuthIdentifier()
    {
        return $this->email;
    }

    public function getAuthPassword()
    {
        $hash = $this->password_hash;
        // Convertir $2b$ a $2y$ para compatibilidad con PHP
        if (substr($hash, 0, 4) === '$2b$') {
            $hash = '$2y$' . substr($hash, 4);
        }
        return $hash;
    }

    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    // Deshabilitar remember token (ya que no lo usas)
    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // No hacer nada
    }

    public function getRememberTokenName()
    {
        return null;
    }

    // Accessor para el nombre (requerido por Filament)
    public function getNameAttribute()
    {
        return $this->nombre_completo;
    }

    // Verificar si el usuario está activo
    public function isActive()
    {
        return $this->activo;
    }

    /**
     * Relación con Rol
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'rol_id');
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}