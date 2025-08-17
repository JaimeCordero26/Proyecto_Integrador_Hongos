<?php

namespace App\Filament\Resources\TipoContaminacionResource\Pages;

use App\Filament\Resources\TipoContaminacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTipoContaminacion extends ViewRecord
{
    protected static string $resource = TipoContaminacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
