<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class SalasCultivo extends Model
{
    use CrudTrait;

    protected $table = 'administracion.salas_cultivo';
    protected $primaryKey = 'sala_id';
    public $timestamps = false;
    
    protected $fillable = [
        'nombre_sala',
        'descripcion',
        'proposito'
    ];

    protected $guarded = ['sala_id'];

    // Relaciones
    public function registrosAmbientales()
    {
        return $this->hasMany(RegistroAmbiental::class, 'sala_id', 'sala_id');
    }

    public function lotesProduccion()
    {
        return $this->hasMany(LoteProduccion::class, 'sala_id', 'sala_id');
    }
}
