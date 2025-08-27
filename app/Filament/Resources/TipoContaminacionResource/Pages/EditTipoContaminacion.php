<?php

namespace App\Filament\Resources\TipoContaminacionResource\Pages;

use App\Filament\Resources\TipoContaminacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoContaminacion extends EditRecord
{
    protected static string $resource = TipoContaminacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->tienePermiso('tipo_contaminacion.eliminar') ?? false),
        ];
    }

    protected function canEdit($record): bool
    {
        return auth()->user()?->tienePermiso('tipo_contaminacion.editar') ?? false;
    }
}
