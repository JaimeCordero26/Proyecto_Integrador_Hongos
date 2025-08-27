<?php

namespace App\Filament\Resources\BitacoraActividadResource\Pages;

use App\Filament\Resources\BitacoraActividadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBitacoraActividad extends ViewRecord
{
    protected static string $resource = BitacoraActividadResource::class;

    protected function canView($record): bool
    {
        return auth()->user()?->tienePermiso('bitacora_actividad.ver') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('bitacora_actividad.editar') ?? false),
            Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('bitacora_actividad.eliminar') ?? false),
        ];
    }
}
