<?php

namespace App\Filament\Resources\BitacoraActividadResource\Pages;

use App\Filament\Resources\BitacoraActividadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBitacoraActividad extends EditRecord
{
    protected static string $resource = BitacoraActividadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
