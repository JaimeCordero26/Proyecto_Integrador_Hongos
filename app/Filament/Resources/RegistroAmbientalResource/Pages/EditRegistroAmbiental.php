<?php

namespace App\Filament\Resources\RegistroAmbientalResource\Pages;

use App\Filament\Resources\RegistroAmbientalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegistroAmbiental extends EditRecord
{
    protected static string $resource = RegistroAmbientalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->tienePermiso('registro_ambiental.eliminar') ?? false),
        ];
    }

    protected function canEdit($record): bool
    {
        return auth()->user()?->tienePermiso('registro_ambiental.editar') ?? false;
    }
}
