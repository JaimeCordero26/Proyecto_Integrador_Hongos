<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use CrudTrait;

    protected $table = 'seguridad.permisos';
    protected $primaryKey = 'permiso_id';
    public $timestamps = false;
    
    protected $fillable = [
        'nombre_permiso',
        'descripcion'
    ];

    protected $guarded = ['permiso_id'];

    // Relaciones
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'seguridad.roles_permisos', 'permiso_id', 'rol_id');
    }
}
