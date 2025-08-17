<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cepa extends Model
{
    use HasFactory;

    protected $table = 'cultivo.cepas';
    protected $primaryKey = 'cepa_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre_comun',
        'nombre_cientifico',
        'codigo_interno',
    ];

    /**
     * Relación con LotesProduccion
     */
    public function lotesProduccion(): HasMany
    {
        return $this->hasMany(LoteProduccion::class, 'cepa_id', 'cepa_id');
    }

    /**
     * Relación con LotesInoculo
     */
    public function lotesInoculo(): HasMany
    {
        return $this->hasMany(LoteInoculo::class, 'cepa_id', 'cepa_id');
    }

    /**
     * Accessor para nombre completo
     */
    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre_comun . 
               ($this->nombre_cientifico ? " ({$this->nombre_cientifico})" : '') .
               ($this->codigo_interno ? " [{$this->codigo_interno}]" : '');
    }
}