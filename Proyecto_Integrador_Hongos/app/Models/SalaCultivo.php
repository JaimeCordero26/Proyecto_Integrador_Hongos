<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaCultivo extends Model
{
    use HasFactory;

    protected $table = 'administracion.salas_cultivo';
    protected $primaryKey = 'sala_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre_sala',
        'descripcion',
        'proposito',
    ];

    /**
     * Relación con RegistrosAmbientales
     */
    public function registrosAmbientales(): HasMany
    {
        return $this->hasMany(RegistroAmbiental::class, 'sala_id', 'sala_id');
    }

    /**
     * Relación con LotesProduccion
     */
    public function lotesProduccion(): HasMany
    {
        return $this->hasMany(LoteProduccion::class, 'sala_id', 'sala_id');
    }
}