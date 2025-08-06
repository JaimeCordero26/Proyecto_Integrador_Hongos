<?php

namespace App\Filament\Resources\RegistroAmbientalResource\Pages;

use App\Filament\Resources\RegistroAmbientalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegistroAmbientals extends ListRecords
{
    protected static string $resource = RegistroAmbientalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
