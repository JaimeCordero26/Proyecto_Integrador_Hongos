<?php

namespace App\Filament\Resources\ProcesoEsterilizacionResource\Pages;

use App\Filament\Resources\ProcesoEsterilizacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProcesoEsterilizacion extends ViewRecord
{
    protected static string $resource = ProcesoEsterilizacionResource::class;

    protected function canView($record): bool
    {
        return auth()->user()?->tienePermiso('proceso_esterilizacion.ver') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('proceso_esterilizacion.editar') ?? false),
            Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('proceso_esterilizacion.eliminar') ?? false),
        ];
    }
}
