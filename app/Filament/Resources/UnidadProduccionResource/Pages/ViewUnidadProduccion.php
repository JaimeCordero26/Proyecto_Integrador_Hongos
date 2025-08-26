<?php

namespace App\Filament\Resources\UnidadProduccionResource\Pages;

use App\Filament\Resources\UnidadProduccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUnidadProduccion extends ViewRecord
{
    protected static string $resource = UnidadProduccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
