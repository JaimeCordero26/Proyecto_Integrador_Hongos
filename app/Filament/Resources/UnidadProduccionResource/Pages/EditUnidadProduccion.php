<?php

namespace App\Filament\Resources\UnidadProduccionResource\Pages;

use App\Filament\Resources\UnidadProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnidadProduccion extends EditRecord
{
    protected static string $resource = UnidadProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->tienePermiso('unidad_produccion.eliminar') ?? false),
        ];
    }

    protected function canEdit($record): bool
    {
        return auth()->user()?->tienePermiso('unidad_produccion.editar') ?? false;
    }
}
