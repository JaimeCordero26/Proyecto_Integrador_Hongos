<?php

namespace App\Filament\Resources\SustratoResource\Pages;

use App\Filament\Resources\SustratoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSustrato extends EditRecord
{
    protected static string $resource = SustratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->tienePermiso('sustrato.eliminar') ?? false),
        ];
    }

    protected function canEdit($record): bool
    {
        return auth()->user()?->tienePermiso('sustrato.editar') ?? false;
    }
}
