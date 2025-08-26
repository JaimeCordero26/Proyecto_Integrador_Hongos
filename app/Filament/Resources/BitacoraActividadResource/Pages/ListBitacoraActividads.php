<?php

namespace App\Filament\Resources\BitacoraActividadResource\Pages;

use App\Filament\Resources\BitacoraActividadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBitacoraActividads extends ListRecords
{
    protected static string $resource = BitacoraActividadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
