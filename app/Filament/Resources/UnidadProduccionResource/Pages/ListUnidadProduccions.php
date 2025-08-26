<?php

namespace App\Filament\Resources\UnidadProduccionResource\Pages;

use App\Filament\Resources\UnidadProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnidadProduccions extends ListRecords
{
    protected static string $resource = UnidadProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
