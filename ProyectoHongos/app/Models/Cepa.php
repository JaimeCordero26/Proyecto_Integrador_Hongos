<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Cepa extends Model
{
    use CrudTrait;

    protected $table = 'cultivo.cepas';
    protected $primaryKey = 'cepa_id';
    public $timestamps = false;
    
    protected $fillable = [
        'nombre_comun',
        'nombre_cientifico',
        'codigo_interno'
    ];

    protected $guarded = ['cepa_id'];

    // Relaciones
    public function lotesInoculo()
    {
        return $this->hasMany(LoteInoculo::class, 'cepa_id', 'cepa_id');
    }

    public function lotesProduccion()
    {
        return $this->hasMany(LoteProduccion::class, 'cepa_id', 'cepa_id');
    }
}
