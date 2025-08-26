<?php

namespace App\Filament\Resources\LoteProduccionResource\Pages;

use App\Filament\Resources\LoteProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoteProduccions extends ListRecords
{
    protected static string $resource = LoteProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
