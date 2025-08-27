<?php

namespace App\Filament\Resources\SolicitudProcuraduriaResource\Pages;

use App\Filament\Resources\SolicitudProcuraduriaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSolicitudProcuraduria extends ViewRecord
{
    protected static string $resource = SolicitudProcuraduriaResource::class;

    protected function canView($record): bool
    {
        return auth()->user()?->tienePermiso('solicitud_procuraduria.ver') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('solicitud_procuraduria.editar') ?? false),
            Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('solicitud_procuraduria.eliminar') ?? false),
        ];
    }
}
