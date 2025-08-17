<?php

namespace App\Filament\Resources\AccionResource\Pages;

use App\Filament\Resources\AccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAccion extends ViewRecord
{
    protected static string $resource = AccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
