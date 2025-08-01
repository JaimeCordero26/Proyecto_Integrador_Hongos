<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class RegistroAmbiental extends Model
{
    use CrudTrait;

    protected $table = 'cultivo.registros_ambientales';
    protected $primaryKey = 'registro_id';
    public $timestamps = false;
    
    protected $fillable = [
        'sala_id',
        'fecha_hora',
        'temperatura_celsius',
        'humedad_relativa',
        'co2_ppm'
    ];

    protected $guarded = ['registro_id'];

    protected $dates = [
        'fecha_hora'
    ];

    protected $casts = [
        'temperatura_celsius' => 'decimal:1',
        'humedad_relativa' => 'decimal:1',
        'co2_ppm' => 'integer'
    ];

    // Relaciones
    public function sala()
    {
        return $this->belongsTo(SalasCultivo::class, 'sala_id', 'sala_id');
    }
}
