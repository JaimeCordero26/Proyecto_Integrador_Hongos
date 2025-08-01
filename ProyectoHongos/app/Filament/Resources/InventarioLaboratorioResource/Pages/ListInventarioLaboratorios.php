<?php

namespace App\Filament\Resources\InventarioLaboratorioResource\Pages;

use App\Filament\Resources\InventarioLaboratorioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventarioLaboratorios extends ListRecords
{
    protected static string $resource = InventarioLaboratorioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
