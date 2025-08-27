<?php

namespace App\Filament\Resources\SustratoResource\Pages;

use App\Filament\Resources\SustratoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSustrato extends ViewRecord
{
    protected static string $resource = SustratoResource::class;

    protected function canView($record): bool
    {
        return auth()->user()?->tienePermiso('sustrato.ver') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('sustrato.editar') ?? false),
            Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('sustrato.eliminar') ?? false),
        ];
    }
}
