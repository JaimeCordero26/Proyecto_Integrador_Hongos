<?php

namespace App\Filament\Resources\InventarioLaboratorioResource\Pages;

use App\Filament\Resources\InventarioLaboratorioResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInventarioLaboratorio extends ViewRecord
{
    protected static string $resource = InventarioLaboratorioResource::class;

    protected function canView($record): bool
    {
        return auth()->user()?->tienePermiso('inventario_laboratorio.ver') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('inventario_laboratorio.editar') ?? false),
            Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('inventario_laboratorio.eliminar') ?? false),
        ];
    }
}
