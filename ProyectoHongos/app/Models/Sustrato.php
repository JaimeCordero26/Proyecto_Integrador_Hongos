<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Sustrato extends Model
{
    use CrudTrait;

    protected $table = 'cultivo.sustratos';
    protected $primaryKey = 'sustrato_id';
    public $timestamps = false;
    
    protected $fillable = [
        'nombre_sustrato',
        'descripcion'
    ];

    protected $guarded = ['sustrato_id'];

    // Relaciones
    public function lotesSustratos()
    {
        return $this->hasMany(LoteSustrato::class, 'sustrato_id', 'sustrato_id');
    }
}
