<?php

namespace App\Filament\Resources\SolicitudesProcuraduriaResource\Pages;

use App\Filament\Resources\SolicitudesProcuraduriaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSolicitudesProcuradurias extends ListRecords
{
    protected static string $resource = SolicitudesProcuraduriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
