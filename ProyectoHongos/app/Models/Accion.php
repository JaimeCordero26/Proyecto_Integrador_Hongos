<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Accion extends Model
{
    use CrudTrait;

    protected $table = 'auditoria.acciones';
    protected $primaryKey = 'auditoria_id';
    public $timestamps = false;
    
    protected $fillable = [
        'tabla_afectada',
        'id_registro',
        'tipo_accion',
        'usuario_id',
        'fecha_hora',
        'descripcion'
    ];

    protected $guarded = ['auditoria_id'];

    protected $dates = [
        'fecha_hora'
    ];

    protected $casts = [
        'id_registro' => 'integer'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'usuario_id');
    }
}
