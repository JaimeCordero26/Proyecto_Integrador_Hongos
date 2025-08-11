<?php

namespace App\Filament\Resources\BitacoraActividadesResource\Pages;

use App\Filament\Resources\BitacoraActividadesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBitacoraActividades extends EditRecord
{
    protected static string $resource = BitacoraActividadesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
