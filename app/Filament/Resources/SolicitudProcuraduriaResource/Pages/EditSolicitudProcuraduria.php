<?php

namespace App\Filament\Resources\SolicitudProcuraduriaResource\Pages;

use App\Filament\Resources\SolicitudProcuraduriaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSolicitudProcuraduria extends EditRecord
{
    protected static string $resource = SolicitudProcuraduriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
