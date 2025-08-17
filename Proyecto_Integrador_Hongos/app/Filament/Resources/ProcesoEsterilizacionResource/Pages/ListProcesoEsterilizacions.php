<?php

namespace App\Filament\Resources\ProcesoEsterilizacionResource\Pages;

use App\Filament\Resources\ProcesoEsterilizacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcesoEsterilizacions extends ListRecords
{
    protected static string $resource = ProcesoEsterilizacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
