<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use CrudTrait;

    protected $table = 'seguridad.roles';
    protected $primaryKey = 'rol_id';
    public $timestamps = false;
    
    protected $fillable = [
        'nombre_rol',
        'descripcion_rol'
    ];

    protected $guarded = ['rol_id'];

    // Relaciones
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'rol_id', 'rol_id');
    }

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'seguridad.roles_permisos', 'rol_id', 'permiso_id');
    }
}
