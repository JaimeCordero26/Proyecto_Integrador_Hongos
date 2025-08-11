<?php

namespace App\Filament\Resources\LotesProduccionResource\Pages;

use App\Filament\Resources\LotesProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLotesProduccions extends ListRecords
{
    protected static string $resource = LotesProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
