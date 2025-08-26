<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sustrato extends Model
{
    use HasFactory;

    protected $table = 'cultivo.sustratos';
    protected $primaryKey = 'sustrato_id';
    public $timestamps = false;

    protected $fillable = [
        'nombre_sustrato',
        'descripcion',
    ];

    /**
     * Relación con LoteSustratos
     */
    public function loteSustratos(): HasMany
    {
        return $this->hasMany(LoteSustrato::class, 'sustrato_id', 'sustrato_id');
    }
}