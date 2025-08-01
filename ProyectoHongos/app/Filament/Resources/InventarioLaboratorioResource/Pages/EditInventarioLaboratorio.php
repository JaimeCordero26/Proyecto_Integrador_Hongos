<?php

namespace App\Filament\Resources\InventarioLaboratorioResource\Pages;

use App\Filament\Resources\InventarioLaboratorioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventarioLaboratorio extends EditRecord
{
    protected static string $resource = InventarioLaboratorioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
