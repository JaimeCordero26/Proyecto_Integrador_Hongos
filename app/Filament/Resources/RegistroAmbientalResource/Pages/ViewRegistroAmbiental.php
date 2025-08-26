<?php

namespace App\Filament\Resources\RegistroAmbientalResource\Pages;

use App\Filament\Resources\RegistroAmbientalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRegistroAmbiental extends ViewRecord
{
    protected static string $resource = RegistroAmbientalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
