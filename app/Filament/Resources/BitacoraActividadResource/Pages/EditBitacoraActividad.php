<?php

namespace App\Filament\Resources\BitacoraActividadResource\Pages;

use App\Filament\Resources\BitacoraActividadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBitacoraActividad extends EditRecord
{
    protected static string $resource = BitacoraActividadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->tienePermiso('bitacora_actividad.eliminar') ?? false),
        ];
    }

    protected function canEdit($record): bool
    {
        return auth()->user()?->tienePermiso('bitacora_actividad.editar') ?? false;
    }
}
