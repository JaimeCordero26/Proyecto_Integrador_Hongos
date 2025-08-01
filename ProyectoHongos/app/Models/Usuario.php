<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
    ];

    protected $rememberTokenName = null;

    public function getAuthPassword()
    {
        $hash = $this->password_hash;

        // Convierte $2b$ a $2y$ si es necesario (compatibilidad con bcrypt)
        if (substr($hash, 0, 4) === '$2b$') {
            $hash = '$2y$' . substr($hash, 4);
        }

        return $hash;
    }

    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // Ignorado
    }

    public function getRememberTokenName()
    {
        return null;
    }

    public function getNameAttribute()
    {
        return $this->nombre_completo;
    }

    public function isActive()
    {
        return $this->activo;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'rol_id');
    }
}
