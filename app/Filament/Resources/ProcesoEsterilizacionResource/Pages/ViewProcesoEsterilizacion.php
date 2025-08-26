<?php

namespace App\Filament\Resources\ProcesoEsterilizacionResource\Pages;

use App\Filament\Resources\ProcesoEsterilizacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProcesoEsterilizacion extends ViewRecord
{
    protected static string $resource = ProcesoEsterilizacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
