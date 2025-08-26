<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolPermiso extends Model
{
    use HasFactory;

    protected $table = 'seguridad.roles_permisos';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'rol_id',
        'permiso_id',
    ];

    /**
     * Relación con Rol
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id', 'rol_id');
    }

    /**
     * Relación con Permiso
     */
    public function permiso(): BelongsTo
    {
        return $this->belongsTo(Permiso::class, 'permiso_id', 'permiso_id');
    }
}