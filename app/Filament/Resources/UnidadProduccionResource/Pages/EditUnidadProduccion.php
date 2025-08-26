<?php

namespace App\Filament\Resources\UnidadProduccionResource\Pages;

use App\Filament\Resources\UnidadProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnidadProduccion extends EditRecord
{
    protected static string $resource = UnidadProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
