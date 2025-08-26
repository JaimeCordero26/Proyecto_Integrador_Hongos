<?php

namespace App\Filament\Resources\ProcesoEsterilizacionResource\Pages;

use App\Filament\Resources\ProcesoEsterilizacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProcesoEsterilizacion extends EditRecord
{
    protected static string $resource = ProcesoEsterilizacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
