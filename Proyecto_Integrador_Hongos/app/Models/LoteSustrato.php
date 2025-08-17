<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoteSustrato extends Model
{
    use HasFactory;

    protected $table = 'cultivo.lote_sustratos';
    protected $primaryKey = 'lote_sustrato_id';
    public $timestamps = false;

    protected $fillable = [
        'lote_id',
        'sustrato_id',
        'cantidad_gramos',
    ];

    protected $casts = [
        'cantidad_gramos' => 'decimal:2',
    ];

    /**
     * Relación con LoteProduccion
     */
    public function loteProduccion(): BelongsTo
    {
        return $this->belongsTo(LoteProduccion::class, 'lote_id', 'lote_id');
    }

    /**
     * Relación con Sustrato
     */
    public function sustrato(): BelongsTo
    {
        return $this->belongsTo(Sustrato::class, 'sustrato_id', 'sustrato_id');
    }
}