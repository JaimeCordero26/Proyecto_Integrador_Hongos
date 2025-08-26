<?php

namespace App\Filament\Resources\BitacoraActividadResource\Pages;

use App\Filament\Resources\BitacoraActividadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBitacoraActividad extends ViewRecord
{
    protected static string $resource = BitacoraActividadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
