<?php

namespace App\Filament\Resources\CosechaResource\Pages;

use App\Filament\Resources\CosechaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCosecha extends EditRecord
{
    protected static string $resource = CosechaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->tienePermiso('cosecha.eliminar') ?? false),
        ];
    }

    protected function canEdit($record): bool
    {
        return auth()->user()?->tienePermiso('cosecha.editar') ?? false;
    }
}
