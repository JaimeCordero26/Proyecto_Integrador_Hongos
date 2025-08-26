<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'seguridad.roles';
    protected $primaryKey = 'rol_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre_rol',
        'descripcion_rol',
    ];

    /**
     * Relación con Usuarios
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'rol_id', 'rol_id');
    }

    /**
     * Relación muchos a muchos con Permisos
     */
    public function permisos(): BelongsToMany
    {
        return $this->belongsToMany(
            Permiso::class,
            'seguridad.roles_permisos',
            'rol_id',
            'permiso_id',
            'rol_id',
            'permiso_id'
        );
    }
}