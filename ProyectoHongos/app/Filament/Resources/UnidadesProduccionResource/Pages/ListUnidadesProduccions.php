<?php

namespace App\Filament\Resources\UnidadesProduccionResource\Pages;

use App\Filament\Resources\UnidadesProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnidadesProduccions extends ListRecords
{
    protected static string $resource = UnidadesProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
