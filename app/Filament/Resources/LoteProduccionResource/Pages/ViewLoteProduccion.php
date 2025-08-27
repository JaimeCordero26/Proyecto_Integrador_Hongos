<?php

namespace App\Filament\Resources\LoteProduccionResource\Pages;

use App\Filament\Resources\LoteProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLoteProduccion extends ViewRecord
{
    protected static string $resource = LoteProduccionResource::class;

    protected function canView($record): bool
    {
        return auth()->user()?->tienePermiso('lote_produccion.ver') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('lote_produccion.editar') ?? false),
            Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('lote_produccion.eliminar') ?? false),
        ];
    }
}
