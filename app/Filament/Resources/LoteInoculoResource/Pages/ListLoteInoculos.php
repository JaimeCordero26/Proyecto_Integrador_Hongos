<?php

namespace App\Filament\Resources\LoteInoculoResource\Pages;

use App\Filament\Resources\LoteInoculoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoteInoculos extends ListRecords
{
    protected static string $resource = LoteInoculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
