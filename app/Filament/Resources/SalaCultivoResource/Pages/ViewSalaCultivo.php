<?php

namespace App\Filament\Resources\SalaCultivoResource\Pages;

use App\Filament\Resources\SalaCultivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSalaCultivo extends ViewRecord
{
    protected static string $resource = SalaCultivoResource::class;

    protected function canView($record): bool
    {
        return auth()->user()?->tienePermiso('sala_cultivo.ver') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(fn() => auth()->user()?->tienePermiso('sala_cultivo.editar') ?? false),
            Actions\DeleteAction::make()->visible(fn() => auth()->user()?->tienePermiso('sala_cultivo.eliminar') ?? false),
        ];
    }
}
