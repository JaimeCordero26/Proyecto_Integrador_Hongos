<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class BitacoraActividad extends Model
{
    use CrudTrait;

    protected $table = 'seguridad.bitacora_actividades';
    protected $primaryKey = 'bitacora_id';
    public $timestamps = false;
    
    protected $fillable = [
        'usuario_id',
        'fecha_hora',
        'tipo_actividad',
        'descripcion_detallada'
    ];

    protected $guarded = ['bitacora_id'];

    protected $dates = [
        'fecha_hora'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'usuario_id');
    }
}
