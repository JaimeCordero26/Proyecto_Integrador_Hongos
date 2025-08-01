<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class SolicitudProcuraduria extends Model
{
    use CrudTrait;

    protected $table = 'administracion.solicitudes_procuraduria';
    protected $primaryKey = 'solicitud_id';
    public $timestamps = false;
    
    protected $fillable = [
        'item_id',
        'descripcion_item_nuevo',
        'usuario_solicitante_id',
        'fecha_solicitud',
        'cantidad_solicitada',
        'justificacion',
        'estado_solicitud'
    ];

    protected $guarded = ['solicitud_id'];

    protected $dates = [
        'fecha_solicitud'
    ];

    protected $casts = [
        'cantidad_solicitada' => 'integer'
    ];

    // Relaciones
    public function inventarioItem()
    {
        return $this->belongsTo(InventarioLaboratorio::class, 'item_id', 'item_id');
    }

    public function usuarioSolicitante()
    {
        return $this->belongsTo(Usuario::class, 'usuario_solicitante_id', 'usuario_id');
    }
}
