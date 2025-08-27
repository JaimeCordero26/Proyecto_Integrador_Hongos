<?php

namespace App\Filament\Resources\AccionResource\Pages;

use App\Filament\Resources\AccionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccion extends EditRecord
{
    protected static string $resource = AccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->tienePermiso('accion.eliminar') ?? false),
        ];
    }

    protected function canEdit($record): bool
    {
        return auth()->user()?->tienePermiso('accion.editar') ?? false;
    }
}
