<?php

namespace App\Filament\Resources\CosechaResource\Pages;

use App\Filament\Resources\CosechaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCosecha extends ViewRecord
{
    protected static string $resource = CosechaResource::class;

    protected function canView($record): bool
    {
        return auth()->user()?->tienePermiso('cosecha.ver') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.editar') ?? false),
            Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('cosecha.eliminar') ?? false),
        ];
    }
}
