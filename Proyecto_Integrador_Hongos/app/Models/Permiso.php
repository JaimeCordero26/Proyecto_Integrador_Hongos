<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permiso extends Model
{
    use HasFactory;

    protected $table = 'seguridad.permisos';
    protected $primaryKey = 'permiso_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre_permiso',
        'descripcion',
    ];

    /**
     * Relación muchos a muchos con Roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Rol::class,
            'seguridad.roles_permisos',
            'permiso_id',
            'rol_id',
            'permiso_id',
            'rol_id'
        );
    }
}