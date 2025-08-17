<?php

namespace App\Filament\Resources\InventarioLaboratorioResource\Pages;

use App\Filament\Resources\InventarioLaboratorioResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInventarioLaboratorio extends ViewRecord
{
    protected static string $resource = InventarioLaboratorioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
