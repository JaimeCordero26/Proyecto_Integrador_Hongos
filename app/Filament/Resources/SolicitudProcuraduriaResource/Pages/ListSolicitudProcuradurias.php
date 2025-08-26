<?php

namespace App\Filament\Resources\SolicitudProcuraduriaResource\Pages;

use App\Filament\Resources\SolicitudProcuraduriaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSolicitudProcuradurias extends ListRecords
{
    protected static string $resource = SolicitudProcuraduriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
