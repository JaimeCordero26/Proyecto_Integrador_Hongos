<?php

namespace App\Filament\Resources\LoteProduccionResource\Pages;

use App\Filament\Resources\LoteProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoteProduccion extends EditRecord
{
    protected static string $resource = LoteProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->tienePermiso('lote_produccion.eliminar') ?? false),
        ];
    }

    protected function canEdit($record): bool
    {
        return auth()->user()?->tienePermiso('lote_produccion.editar') ?? false;
    }


protected function mutateFormDataBeforeSave(array $data): array
{
    $totalG = 0.0;
    foreach (($data['loteSustratos'] ?? []) as $it) {
        $totalG += (float) ($it['cantidad_gramos'] ?? 0);
    }
    $data['peso_sustrato_seco_kg'] = $totalG / 1000;

    return $data;
}





}
