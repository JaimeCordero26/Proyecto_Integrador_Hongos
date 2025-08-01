<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class LoteSustrato extends Model
{
    use CrudTrait;

    protected $table = 'cultivo.lote_sustratos';
    protected $primaryKey = 'lote_sustrato_id';
    public $timestamps = false;
    
    protected $fillable = [
        'lote_id',
        'sustrato_id',
        'cantidad_gramos'
    ];

    protected $guarded = ['lote_sustrato_id'];

    protected $casts = [
        'cantidad_gramos' => 'decimal:2'
    ];

    // Relaciones
    public function loteProduccion()
    {
        return $this->belongsTo(LoteProduccion::class, 'lote_id', 'lote_id');
    }

    public function sustrato()
    {
        return $this->belongsTo(Sustrato::class, 'sustrato_id', 'sustrato_id');
    }
}
