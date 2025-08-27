<?php

namespace App\Filament\Resources\SalaCultivoResource\Pages;

use App\Filament\Resources\SalaCultivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalaCultivo extends EditRecord
{
    protected static string $resource = SalaCultivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->tienePermiso('sala_cultivo.eliminar') ?? false),
        ];
    }

    protected function canEdit($record): bool
    {
        return auth()->user()?->tienePermiso('sala_cultivo.editar') ?? false;
    }
}
