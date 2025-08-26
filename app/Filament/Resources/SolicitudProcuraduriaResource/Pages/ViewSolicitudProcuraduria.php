<?php

namespace App\Filament\Resources\SolicitudProcuraduriaResource\Pages;

use App\Filament\Resources\SolicitudProcuraduriaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSolicitudProcuraduria extends ViewRecord
{
    protected static string $resource = SolicitudProcuraduriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
