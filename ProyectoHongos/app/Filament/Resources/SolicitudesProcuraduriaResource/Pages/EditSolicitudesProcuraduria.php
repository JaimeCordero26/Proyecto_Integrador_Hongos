<?php

namespace App\Filament\Resources\SolicitudesProcuraduriaResource\Pages;

use App\Filament\Resources\SolicitudesProcuraduriaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSolicitudesProcuraduria extends EditRecord
{
    protected static string $resource = SolicitudesProcuraduriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
