<?php

namespace App\Filament\Resources\SolicitudProcuraduriaResource\Pages;

use App\Filament\Resources\SolicitudProcuraduriaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSolicitudProcuraduria extends EditRecord
{
    protected static string $resource = SolicitudProcuraduriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->tienePermiso('solicitud_procuraduria.eliminar') ?? false),
        ];
    }

    protected function canEdit($record): bool
    {
        return auth()->user()?->tienePermiso('solicitud_procuraduria.editar') ?? false;
    }
}
