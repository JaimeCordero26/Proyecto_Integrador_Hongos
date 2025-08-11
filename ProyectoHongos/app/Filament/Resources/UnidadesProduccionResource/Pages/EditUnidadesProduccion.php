<?php

namespace App\Filament\Resources\UnidadesProduccionResource\Pages;

use App\Filament\Resources\UnidadesProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnidadesProduccion extends EditRecord
{
    protected static string $resource = UnidadesProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
