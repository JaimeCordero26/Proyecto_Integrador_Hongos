<?php

namespace App\Filament\Resources\TipoContaminacionResource\Pages;

use App\Filament\Resources\TipoContaminacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTipoContaminacion extends ViewRecord
{
    protected static string $resource = TipoContaminacionResource::class;

    protected function canView($record): bool
    {
        return auth()->user()?->tienePermiso('tipo_contaminacion.ver') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('tipo_contaminacion.editar') ?? false),
            Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('tipo_contaminacion.eliminar') ?? false),
        ];
    }
}
