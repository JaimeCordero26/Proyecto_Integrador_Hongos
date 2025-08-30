<?php

namespace App\Filament\Resources\LoteProduccionResource\Pages;

use App\Filament\Resources\LoteProduccionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoteProduccion extends CreateRecord
{
    protected static string $resource = LoteProduccionResource::class;

    protected function canCreate(): bool
    {
        return auth()->user()?->tienePermiso('lote_produccion.crear') ?? false;
    }

protected function mutateFormDataBeforeCreate(array $data): array
{
    $totalG = 0.0;
    foreach (($data['loteSustratos'] ?? []) as $it) {
        $totalG += (float) ($it['cantidad_gramos'] ?? 0);
    }
    $data['peso_sustrato_seco_kg'] = $totalG / 1000;

    return $data;
}








}



