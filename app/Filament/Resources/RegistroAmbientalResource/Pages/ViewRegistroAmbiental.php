<?php

namespace App\Filament\Resources\RegistroAmbientalResource\Pages;

use App\Filament\Resources\RegistroAmbientalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRegistroAmbiental extends ViewRecord
{
    protected static string $resource = RegistroAmbientalResource::class;

    protected function canView($record): bool
    {
        return auth()->user()?->tienePermiso('registro_ambiental.ver') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('registro_ambiental.editar') ?? false),
            Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('registro_ambiental.eliminar') ?? false),
        ];
    }
}
