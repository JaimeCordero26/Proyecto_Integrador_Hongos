<?php

namespace App\Filament\Resources\CepaResource\Pages;

use App\Filament\Resources\CepaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCepa extends ViewRecord
{
    protected static string $resource = CepaResource::class;

    protected function canView($record): bool
    {
        return auth()->user()?->tienePermiso('cepa.ver') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('cepa.editar') ?? false),
            Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('cepa.eliminar') ?? false),
        ];
    }
}
